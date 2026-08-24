<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\{CrmTicket,CrmContact,Order};
use Illuminate\Http\Request;

class CrmTicketController extends Controller
{
    public function index(Request $request)
    {
        $q = CrmTicket::with('contact','order');
        if ($request->status)   $q->where('status',  $request->status);
        if ($request->priority) $q->where('priority',$request->priority);
        if ($request->search)   $q->where(fn($s) => $s->where('subject','like','%'.$request->search.'%')
                                                       ->orWhere('ticket_number','like','%'.$request->search.'%'));

        $tickets  = $q->latest()->paginate(20)->withQueryString();
        $contacts = CrmContact::orderBy('name')->get(['id','name','phone']);
        $overdue  = CrmTicket::whereNotIn('status',['resolved','closed'])->where('sla_due_at','<',now())->count();

        return view('crm.tickets.index', compact('tickets','contacts','overdue'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'        => 'required|string|max:200',
            'description'    => 'required|string',
            'crm_contact_id' => 'nullable|exists:crm_contacts,id',
            'order_id'       => 'nullable|exists:orders,id',
            'priority'       => 'required|in:low,medium,high,urgent',
            'category'       => 'required|in:order_issue,payment,product,return,other',
        ]);
        // SLA: urgent=4h, high=8h, medium=24h, low=72h
        $slaHours = match($data['priority']) { 'urgent'=>4,'high'=>8,'medium'=>24,default=>72 };
        $data['ticket_number'] = CrmTicket::generateNumber();
        $data['sla_due_at']    = now()->addHours($slaHours);
        CrmTicket::create($data);
        return back()->with('success','Ticket #'.$data['ticket_number'].' created.');
    }

    public function update(Request $request, CrmTicket $ticket)
    {
        $data = $request->validate([
            'status'     => 'required|in:open,in_progress,waiting,resolved,closed',
            'priority'   => 'required|in:low,medium,high,urgent',
            'resolution' => 'nullable|string',
        ]);
        if (in_array($data['status'],['resolved','closed']) && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }
        $ticket->update($data);
        return back()->with('success','Ticket updated.');
    }
}
