<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExchangeOffer;
use App\Models\Product;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public function getOffer($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $offer   = ExchangeOffer::where('product_id', $product->id)
            ->where('is_active', true)->first();

        if (!$offer) {
            return response()->json(['offer' => null]);
        }

        return response()->json([
            'offer' => [
                'max_value'    => (float) $offer->max_exchange_value,
                'terms'        => $offer->terms,
                'multipliers'  => [
                    'excellent' => 1.00,
                    'good'      => 0.75,
                    'fair'      => 0.50,
                    'poor'      => 0.25,
                ],
            ],
        ]);
    }

    public function estimate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'condition'  => 'required|in:excellent,good,fair,poor',
        ]);

        $offer = ExchangeOffer::where('product_id', $request->product_id)
            ->where('is_active', true)->first();

        if (!$offer) {
            return response()->json(['estimated_value' => 0]);
        }

        return response()->json([
            'estimated_value' => $offer->calculateValue($request->condition),
        ]);
    }

    public function verifyImei(Request $request)
    {
        $request->validate(['imei' => 'required|string|size:15|regex:/^[0-9]+$/']);

        $imei = $request->imei;
        $sum  = 0;
        for ($i = 0; $i < 15; $i++) {
            $d = (int) $imei[$i];
            if ($i % 2 === 1) { $d *= 2; if ($d > 9) $d -= 9; }
            $sum += $d;
        }

        $valid = ($sum % 10 === 0);
        return response()->json([
            'valid'   => $valid,
            'message' => $valid ? 'IMEI verified successfully.' : 'Invalid IMEI number.',
        ]);
    }
}
