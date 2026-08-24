<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\{CrmContact,CrmLead,CrmTicket,CrmCampaign,Order,User,LoyaltyTransaction};
use Illuminate\Support\Facades\DB;

class CrmDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_contacts'   => CrmContact::count(),
            'active_leads'     => CrmLead::whereNotIn('stage',['won','lost'])->count(),
            'open_tickets'     => CrmTicket::whereNotIn('status',['resolved','closed'])->count(),
            'points_awarded'   => (int) LoyaltyTransaction::where('type','earned')->sum('points'),
            'total_revenue'    => (float) Order::where('status','delivered')->sum('total'),
            'this_month_rev'   => (float) Order::where('status','delivered')->whereMonth('created_at',now()->month)->whereYear('created_at',now()->year)->sum('total'),
            'repeat_customers' => User::has('orders','>=',2)->count(),
            'campaigns_sent'   => CrmCampaign::where('status','completed')->count(),
        ];

        // Segment breakdown
        $segments = CrmContact::select('segment', DB::raw('count(*) as count'))
            ->groupBy('segment')->pluck('count','segment')->toArray();

        // Lead pipeline value per stage
        $pipeline = CrmLead::select('stage', DB::raw('count(*) as count'), DB::raw('sum(value) as total_value'))
            ->whereNotIn('stage',['won','lost'])->groupBy('stage')->get();

        // Monthly revenue last 6 months
        $monthlyRevenue = Order::where('status','delivered')
            ->where('created_at','>=',now()->subMonths(5)->startOfMonth())
            ->select(DB::raw('DATE_FORMAT(created_at,"%Y-%m") as month'), DB::raw('sum(total) as revenue'))
            ->groupBy('month')->orderBy('month')->get();

        // Recent interactions
        $recentInteractions = \App\Models\CrmInteraction::with('contact')
            ->latest('interacted_at')->limit(8)->get();

        // Overdue tickets
        $overdueTickets = CrmTicket::with('contact')
            ->whereNotIn('status',['resolved','closed'])
            ->where('sla_due_at','<',now())->limit(5)->get();

        // Top contacts by spend
        $topContacts = CrmContact::orderByDesc('total_spent')->limit(5)->get();

        return view('crm.dashboard', compact(
            'stats','segments','pipeline','monthlyRevenue',
            'recentInteractions','overdueTickets','topContacts'
        ));
    }
}
