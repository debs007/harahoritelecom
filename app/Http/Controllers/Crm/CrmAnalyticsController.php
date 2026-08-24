<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\{Order,User,CrmContact,CrmLead,CrmTicket,LoyaltyTransaction,CrmSetting};
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\DB;

class CrmAnalyticsController extends Controller
{
    public function index()
    {
        // Revenue last 12 months
        $monthlyRevenue = Order::where('status','delivered')
            ->where('created_at','>=',now()->subMonths(11)->startOfMonth())
            ->select(DB::raw('DATE_FORMAT(created_at,"%Y-%m") as month'), DB::raw('sum(total) as revenue'), DB::raw('count(*) as orders'))
            ->groupBy('month')->orderBy('month')->get();

        // Segment revenue — use subquery to avoid MySQL ONLY_FULL_GROUP_BY issues
        $segmentRevenue = DB::table(DB::raw('(
            SELECT
                COALESCE(
                    NULLIF(cc.segment, "unclassified"),
                    NULLIF(u.crm_segment, "unclassified"),
                    "unclassified"
                ) as segment,
                o.total
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            LEFT JOIN crm_contacts cc ON cc.user_id = u.id
            WHERE o.status = "delivered"
        ) as seg_data'))
            ->select('segment', DB::raw('sum(total) as revenue'), DB::raw('count(*) as orders'))
            ->groupBy('segment')
            ->get();

        // Top cities by revenue
        $cityRevenue = DB::table('orders')
            ->join('addresses','addresses.id','=','orders.address_id')
            ->where('orders.status','delivered')
            ->whereNotNull('addresses.city')
            ->select('addresses.city', DB::raw('sum(orders.total) as revenue'), DB::raw('count(*) as orders'))
            ->groupBy('addresses.city')->orderByDesc('revenue')->limit(10)->get();

        // Lead conversion funnel
        $leadFunnel = CrmLead::select('stage', DB::raw('count(*) as count'), DB::raw('sum(value) as value'))
            ->groupBy('stage')->get()->keyBy('stage');

        // Ticket resolution metrics
        $ticketMetrics = [
            'avg_resolution_hours' => CrmTicket::whereNotNull('resolved_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_h'))->value('avg_h'),
            'overdue_count'        => CrmTicket::whereNotIn('status',['resolved','closed'])->where('sla_due_at','<',now())->count(),
            'by_status'            => CrmTicket::select('status',DB::raw('count(*) as c'))->groupBy('status')->pluck('c','status'),
            'by_priority'          => CrmTicket::select('priority',DB::raw('count(*) as c'))->groupBy('priority')->pluck('c','priority'),
        ];

        $pointValue = LoyaltyService::pointValue();

        // Loyalty summary
        $loyaltyStats = [
            'total_outstanding'   => User::sum('loyalty_points'),
            'total_inr_value'     => round(User::sum('loyalty_points') * $pointValue, 2),
            'earned_this_month'   => (int) LoyaltyTransaction::where('type','earned')->whereMonth('created_at',now()->month)->sum('points'),
            'redeemed_this_month' => (int) LoyaltyTransaction::where('type','redeemed')->whereMonth('created_at',now()->month)->sum('points'),
            'top_earners'         => User::where('loyalty_points','>',0)->orderByDesc('loyalty_points')->limit(5)->get(['name','phone','loyalty_points']),
            'point_value'         => $pointValue,
        ];

        // Multi-buyer stats
        $multiBuyers = [
            '1_order'  => User::has('orders','=',1)->count(),
            '2_orders' => User::has('orders','=',2)->count(),
            '3_plus'   => User::has('orders','>=',3)->count(),
        ];

        return view('crm.analytics.index', compact(
            'monthlyRevenue','segmentRevenue','cityRevenue',
            'leadFunnel','ticketMetrics','loyaltyStats','multiBuyers'
        ));
    }
}