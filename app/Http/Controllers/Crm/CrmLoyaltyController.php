<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\{User,LoyaltyTransaction,CrmContact,CrmSetting};
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class CrmLoyaltyController extends Controller
{
    public function index(Request $request)
    {
        // Use transaction sum as the source of truth — works even if
        // users.loyalty_points was never written (mass-assignment bug)
        $q = User::withSum('loyaltyTransactions as points_sum', 'points')
                  ->having('points_sum', '>', 0);

        if ($request->search) $q->where(fn($s) => $s->where('name','like','%'.$request->search.'%')
                                                      ->orWhere('phone','like','%'.$request->search.'%'));

        $users      = $q->orderByDesc('points_sum')->paginate(20)->withQueryString();
        $pointValue = LoyaltyService::pointValue();
        $totals = [
            'total_points_outstanding' => (int) LoyaltyTransaction::sum('points'),
            'total_inr_value'          => round((int) LoyaltyTransaction::sum('points') * $pointValue, 2),
            'total_points_earned'      => (int) LoyaltyTransaction::where('type','earned')->sum('points'),
            'total_points_redeemed'    => (int) LoyaltyTransaction::where('type','redeemed')->sum('points'),
            'total_users_with_points'  => User::withSum('loyaltyTransactions as pts','points')->having('pts','>',0)->count(),
        ];
        $recent   = LoyaltyTransaction::with('user','order')->latest()->limit(10)->get();
        $settings = CrmSetting::whereIn('key',['loyalty_point_value','loyalty_points_per_100','loyalty_max_redemption'])->get()->keyBy('key');

        return view('crm.loyalty.index', compact('users','totals','recent','pointValue','settings'));
    }

    public function recalculate()
    {
        // Sync users.loyalty_points from loyalty_transactions for all users
        $users = User::has('loyaltyTransactions')->get();
        $count = 0;
        foreach ($users as $user) {
            $balance = $user->loyaltyTransactions()->sum('points');
            $balance = max(0, $balance);
            // Use DB directly to bypass any potential fillable issues
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update(['loyalty_points' => $balance]);
            $count++;
        }
        return back()->with('success', "Recalculated loyalty balances for {$count} users.");
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'loyalty_point_value'    => 'required|numeric|min:0.01|max:100',
            'loyalty_points_per_100' => 'required|numeric|min:0.1|max:100',
            'loyalty_max_redemption' => 'required|numeric|min:1|max:100',
        ]);
        foreach ($data as $key => $value) {
            CrmSetting::set($key, $value);
        }
        return back()->with('success', 'Loyalty settings updated. 1 point = ₹'.$data['loyalty_point_value']);
    }

    public function adjust(Request $request, User $user)
    {
        $request->validate([
            'points'      => 'required|integer|not_in:0',
            'description' => 'required|string|max:200',
        ]);
        $type = $request->points > 0 ? 'bonus' : 'adjusted';
        $user->addLoyaltyPoints((int)$request->points, $type, $request->description);
        return back()->with('success','Points adjusted. New balance: '.$user->fresh()->loyalty_points);
    }

    public function sendNotification(Request $request, User $user)
    {
        $request->validate(['channel' => 'required|in:whatsapp,sms']);
        $contact    = $user->crmContact;
        $phone      = $contact?->whatsapp ?: $user->phone;
        if (!$phone) return back()->withErrors(['msg'=>'No phone number on file.']);

        $pointValue = LoyaltyService::pointValue();
        $inrValue   = number_format(LoyaltyService::pointsToInr($user->loyalty_points), 2);

        $msg = "Hi {$user->name}! 🎉 You have *{$user->loyalty_points} Harahori Loyalty Points* worth *₹{$inrValue}*."
             . " (1 point = ₹{$pointValue})"
             . " Redeem them on your next purchase! 🛍 Shop now: " . url('/');

        $link = $request->channel === 'whatsapp'
            ? LoyaltyService::buildWhatsappLink($phone, $msg)
            : LoyaltyService::buildSmsLink($phone, $msg);

        return view('crm.loyalty.send', compact('user','link','msg','request','pointValue','inrValue'));
    }
}