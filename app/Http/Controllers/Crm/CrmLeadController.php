<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\{CrmLead,CrmContact};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmLeadController extends Controller
{
    public function index(Request $request)
    {
        $q = CrmLead::with('contact');
        if ($request->stage)     $q->where('stage', $request->stage);
        if ($request->search)    $q->where('title', 'like', '%'.$request->search.'%');
        if ($request->date_from) $q->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $q->whereDate('created_at', '<=', $request->date_to);
        if ($request->auto_only) $q->where('notes', 'like', '%Auto-created%');

        // Pipeline summary per stage
        $stages = ['new','contacted','qualified','proposal','negotiation','won','lost'];
        $pipelineSummary = CrmLead::select('stage',DB::raw('count(*) as count'),DB::raw('sum(value) as total'))
            ->groupBy('stage')->pluck('count','stage');
        $pipelineValue = CrmLead::select('stage',DB::raw('sum(value) as total'))
            ->groupBy('stage')->pluck('total','stage');

        $leads    = $q->latest()->paginate(20)->withQueryString();
        $contacts = CrmContact::orderBy('name')->get(['id','name','phone']);

        // Forecast: expected revenue from non-closed leads this month
        $forecast = CrmLead::whereNotIn('stage',['won','lost'])
            ->whereMonth('expected_close_date',now()->month)
            ->whereYear('expected_close_date',now()->year)
            ->sum('value');

        return view('crm.leads.index', compact('leads','stages','pipelineSummary','pipelineValue','contacts','forecast'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:200',
            'crm_contact_id'      => 'nullable|exists:crm_contacts,id',
            'value'               => 'nullable|numeric|min:0',
            'stage'               => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost',
            'score'               => 'nullable|integer|min:0|max:100',
            'source'              => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'product_interest'    => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);
        CrmLead::create($data);
        return back()->with('success','Lead created.');
    }

    public function update(Request $request, CrmLead $lead)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:200',
            'stage'               => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost',
            'value'               => 'nullable|numeric|min:0',
            'score'               => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'product_interest'    => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);
        $lead->update($data);
        return back()->with('success','Lead updated.');
    }

    public function updateStage(Request $request, CrmLead $lead)
    {
        $request->validate(['stage' => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost']);
        $lead->update(['stage' => $request->stage]);
        return response()->json(['ok' => true]);
    }

    public function destroy(CrmLead $lead)
    {
        $lead->delete();
        return back()->with('success','Lead deleted.');
    }
}
