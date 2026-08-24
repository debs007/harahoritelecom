<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\{CrmContact,CrmInteraction,User};
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class CrmContactController extends Controller
{
    public function index(Request $request)
    {
        $q = CrmContact::with('user')->withCount('tickets');

        // ── Tab filter ──────────────────────────────────────────────────
        $tab = $request->tab ?? 'all';
        if ($tab === 'buyers')          $q->where('contact_type', 'buyer');
        elseif ($tab === 'registered')  $q->where('contact_type', 'registered');
        elseif ($tab === 'tally')       $q->where('contact_type', 'tally_import');
        elseif ($tab === 'manual')      $q->whereIn('contact_type', ['manual', 'prospect']);

        if ($request->segment)    $q->where('segment', $request->segment);
        if ($request->status)     $q->where('status',  $request->status);
        if ($request->city)       $q->where('city',    $request->city);
        if ($request->state)      $q->where('state',   $request->state);
        if ($request->multi_buyer) $q->where('total_orders', '>=', 2);

        if ($request->search) {
            $term = '%' . $request->search . '%';
            $q->where(function ($sq) use ($term) {
                $sq->where('name',  'like', $term)
                   ->orWhere('phone', 'like', $term)
                   ->orWhere('email', 'like', $term)
                   ->orWhereHas('user.orders.items', fn($iq) =>
                       $iq->where('product_name', 'like', $term)
                   );
            });
        }

        if ($request->sort === 'spend')      $q->orderByDesc('total_spent');
        elseif ($request->sort === 'orders') $q->orderByDesc('total_orders');
        elseif ($request->sort === 'recent') $q->latest('last_contacted_at');
        elseif ($request->sort === 'newest') $q->latest();
        else $q->orderByDesc('total_spent'); // default: high value first — feature #5

        $contacts = $q->paginate(20)->withQueryString();

        // Tab counts
        $tabCounts = [
            'all'        => CrmContact::count(),
            'buyers'     => CrmContact::where('contact_type', 'buyer')->count(),
            'registered' => CrmContact::where('contact_type', 'registered')->count(),
            'tally'      => CrmContact::where('contact_type', 'tally_import')->count(),
            'manual'     => CrmContact::whereIn('contact_type', ['manual','prospect'])->count(),
        ];

        $cities  = CrmContact::whereNotNull('city')->distinct()->orderBy('city')->pluck('city');
        $states  = CrmContact::whereNotNull('state')->distinct()->orderBy('state')->pluck('state');
        $segmentCounts = CrmContact::select('segment', \Illuminate\Support\Facades\DB::raw('count(*) as c'))
            ->groupBy('segment')->pluck('c', 'segment');

        return view('crm.contacts.index', compact(
            'contacts', 'cities', 'states', 'segmentCounts', 'tab', 'tabCounts'
        ));
    }

    public function show(CrmContact $contact)
    {
        $contact->load(['user.orders.items','interactions','leads','tickets']);
        $orders = $contact->user?->orders()->with('items.product')->latest()->get() ?? collect();

        // Track visit — if visited before, log interaction
        $contact->increment('visit_count');
        if ($contact->visit_count >= 2) {
            CrmInteraction::create([
                'crm_contact_id' => $contact->id,
                'type'           => 'visit',
                'description'    => 'CRM profile viewed (visit #'.$contact->visit_count.')',
                'interacted_at'  => now(),
            ]);
        }
        $contact->update(['last_contacted_at' => now()->toDateString()]);

        return view('crm.contacts.show', compact('contact','orders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'nullable|email',
            'phone'       => 'nullable|string|max:20',
            'whatsapp'    => 'nullable|string|max:20',
            'city'        => 'nullable|string',
            'state'       => 'nullable|string',
            'pincode'     => 'nullable|string',
            'segment'     => 'nullable|in:budget,mid_range,upper_mid,premium,flagship,unclassified',
            'source'      => 'nullable|in:organic,referral,campaign,tally_import,walk_in,social,other',
            'notes'       => 'nullable|string',
            'due_date'    => 'nullable|date',
        ]);
        $data['segment']      = $data['segment'] ?? 'unclassified';
        $data['source']       = $data['source']  ?? 'organic';
        $data['contact_type'] = 'manual';
        CrmContact::create($data);
        return back()->with('success','Contact added.');
    }

    public function update(Request $request, CrmContact $contact)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'nullable|email',
            'phone'       => 'nullable|string|max:20',
            'whatsapp'    => 'nullable|string|max:20',
            'city'        => 'nullable|string',
            'state'       => 'nullable|string',
            'pincode'     => 'nullable|string',
            'segment'     => 'required|in:budget,mid_range,upper_mid,premium,flagship,unclassified',
            'status'      => 'required|in:active,inactive,prospect,churned',
            'notes'       => 'nullable|string',
            'due_date'    => 'nullable|date',
        ]);
        $contact->update($data);

        // Keep users.crm_segment in sync when segment is manually changed
        if ($contact->user && isset($data['segment'])) {
            $contact->user->update(['crm_segment' => $data['segment']]);
        }

        return back()->with('success','Contact updated.');
    }

    public function addInteraction(Request $request, CrmContact $contact)
    {
        $request->validate([
            'type'        => 'required|in:visit,call,whatsapp,sms,email,note,purchase,support',
            'description' => 'nullable|string',
            'outcome'     => 'nullable|string|max:200',
        ]);
        CrmInteraction::create([
            'crm_contact_id' => $contact->id,
            'type'           => $request->type,
            'description'    => $request->description,
            'outcome'        => $request->outcome,
            'interacted_at'  => now(),
        ]);
        $contact->update(['last_contacted_at' => now()->toDateString()]);
        return back()->with('success','Interaction logged.');
    }

    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '256M');
        set_time_limit(120);

        // ── Exact same query as index() — just without paginate() ────────────
        $q = CrmContact::query();

        $tab = $request->tab ?? 'all';
        if ($tab === 'buyers')         $q->where('contact_type', 'buyer');
        elseif ($tab === 'registered') $q->where('contact_type', 'registered');
        elseif ($tab === 'tally')      $q->where('contact_type', 'tally_import');
        elseif ($tab === 'manual')     $q->whereIn('contact_type', ['manual','prospect']);

        if ($request->segment)     $q->where('segment', $request->segment);
        if ($request->status)      $q->where('status',  $request->status);
        if ($request->city)        $q->where('city',    $request->city);
        if ($request->state)       $q->where('state',   $request->state);
        if ($request->multi_buyer) $q->where('total_orders', '>=', 2);

        if ($request->search) {
            $term = '%' . $request->search . '%';
            $q->where(function ($sq) use ($term) {
                $sq->where('name',   'like', $term)
                   ->orWhere('phone', 'like', $term)
                   ->orWhere('email', 'like', $term)
                   ->orWhereHas('user.orders.items', fn($iq) =>
                       $iq->where('product_name', 'like', $term)
                   );
            });
        }

        if ($request->sort === 'orders')      $q->orderByDesc('total_orders');
        elseif ($request->sort === 'recent')  $q->latest('last_contacted_at');
        elseif ($request->sort === 'newest')  $q->latest();
        else $q->orderByDesc('total_spent');

        $contacts = $q->get(); // all rows matching current filters

        $segDefs = [
            'budget'       => 'Budget',
            'mid_range'    => 'Mid-Range',
            'upper_mid'    => 'Upper Mid',
            'premium'      => 'Premium',
            'flagship'     => 'Flagship',
            'unclassified' => 'Unclassified',
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'crm.contacts.pdf',
            compact('contacts', 'segDefs', 'tab')
        )
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'DejaVu Sans',
            'chroot'               => public_path(),
        ]);

        return $pdf->download('harahori-contacts-' . now()->format('Y-m-d') . '.pdf');
    }

    public function syncFromOrders()
    {
        // Sync ALL customers — both buyers (with orders) and registered (no orders)
        $users = User::where('role', 'customer')->with(['orders', 'addresses'])->get();
        $synced = 0;
        foreach ($users as $user) {
            LoyaltyService::syncContactFromUser($user);
            $synced++;
        }

        $buyers     = \App\Models\CrmContact::where('contact_type', 'buyer')->count();
        $registered = \App\Models\CrmContact::where('contact_type', 'registered')->count();

        return back()->with('success',
            "Synced {$synced} contacts — {$buyers} buyers, {$registered} registered customers."
        );
    }
}