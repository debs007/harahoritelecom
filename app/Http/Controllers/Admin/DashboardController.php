<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders'    => Order::count(),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'total_revenue'   => Order::where('payment_status', 'paid')->sum('total'),
            'total_products'  => Product::active()->count(),
            'total_users'     => User::where('role', 'customer')->count(),
            'pending_reviews' => Review::where('status', 'pending')->count(),
        ];

        $recentOrders = Order::with(['user', 'items'])
            ->latest()->take(10)->get();

        $topProducts = DB::table('order_items')
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(subtotal) as revenue')
            )
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)->get();

        $monthlySales = Order::where('payment_status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->get()
            ->keyBy('month');

        return view('admin.dashboard.index', compact(
            'stats', 'recentOrders', 'topProducts', 'monthlySales'
        ));
    }

    public function salesData(\Illuminate\Http\Request $request)
    {
        $period = $request->get('period', '7days');

        $query = match ($period) {
            '7days'   => Order::where('created_at', '>=', now()->subDays(7)),
            '30days'  => Order::where('created_at', '>=', now()->subDays(30)),
            '12months'=> Order::where('created_at', '>=', now()->subYear()),
            default   => Order::query(),
        };

        $data = $query
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    public function exportOrders()
    {
        $orders = Order::with(['user', 'items', 'address'])
            ->latest()->get();

        $csv  = "Order Number,Customer,Email,Total,Status,Payment,Date\n";
        foreach ($orders as $o) {
            $csv .= implode(',', [
                $o->order_number,
                '"' . $o->user->name . '"',
                $o->user->email,
                $o->total,
                $o->status,
                $o->payment_method,
                $o->created_at->format('Y-m-d'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders_' . now()->format('Ymd') . '.csv"',
        ]);
    }
}
