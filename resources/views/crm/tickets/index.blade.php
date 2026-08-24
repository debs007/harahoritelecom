@extends('crm.layouts.crm')
@section('title','Support Tickets')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Support</span>@endsection
@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <div>
    <h1 class="text-2xl font-black text-slate-900">Support Tickets</h1>
    @if($overdue > 0)<p class="text-sm text-red-600 font-bold mt-0.5">⚠ {{ $overdue }} ticket(s) breached SLA</p>@endif
  </div>
  <button onclick="document.getElementById('addTicketModal').classList.remove('hidden');document.getElementById('addTicketModal').style.display='flex'" class="crm-btn">+ New Ticket</button>
</div>

{{-- Status chips --}}
<div class="flex flex-wrap gap-2 mb-5">
  @foreach([''=>'All','open'=>'Open','in_progress'=>'In Progress','waiting'=>'Waiting','resolved'=>'Resolved','closed'=>'Closed'] as $val=>$label)
  <a href="{{ route('crm.tickets.index',array_merge(request()->all(),['status'=>$val])) }}"
     class="badge {{ request('status')===$val ? 'badge-teal' : 'badge-gray' }}">{{ $label }}</a>
  @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="card mb-5 flex flex-wrap gap-3 items-end">
  <input type="text" name="search" value="{{ request('search') }}" placeholder="Ticket # or subject…" class="input" style="max-width:220px">
  <select name="priority" class="input" style="max-width:140px">
    <option value="">All Priority</option>
    @foreach(['low','medium','high','urgent'] as $p)<option value="{{ $p }}" {{ request('priority')===$p?'selected':'' }}>{{ ucfirst($p) }}</option>@endforeach
  </select>
  <button class="crm-btn">Filter</button>
  <a href="{{ route('crm.tickets.index') }}" class="text-sm text-slate-400">Reset</a>
</form>

<div class="card overflow-hidden p-0">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Ticket</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Contact</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Category</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Priority</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
          <th class="text-left px-4 py-3 font-semibold text-slate-600">SLA</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($tickets as $ticket)
        @php $overdue = $ticket->isOverdue(); @endphp
        <tr class="hover:bg-slate-50 group {{ $overdue ? 'bg-red-50' : '' }}" x-data="{edit:false}">
          <td class="px-4 py-3">
            <p class="font-bold text-xs text-slate-500">{{ $ticket->ticket_number }}</p>
            <p class="font-semibold text-slate-800">{{ Str::limit($ticket->subject,45) }}</p>
          </td>
          <td class="px-4 py-3">
            @if($ticket->contact)<a href="{{ route('crm.contacts.show',$ticket->contact) }}" class="text-teal-600 hover:underline text-sm font-medium">{{ $ticket->contact->name }}</a>@else<span class="text-slate-400">—</span>@endif
          </td>
          <td class="px-4 py-3"><span class="badge badge-slate">{{ ucfirst(str_replace('_',' ',$ticket->category)) }}</span></td>
          <td class="px-4 py-3">
            <span class="badge {{ match($ticket->priority){'urgent'=>'badge-red','high'=>'badge-orange','medium'=>'badge-yellow',default=>'badge-gray'} }}">{{ ucfirst($ticket->priority) }}</span>
          </td>
          <td class="px-4 py-3">
            <span class="badge {{ match($ticket->status){'resolved'=>'badge-green','closed'=>'badge-slate','open'=>'badge-blue','in_progress'=>'badge-indigo',default=>'badge-orange'} }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
          </td>
          <td class="px-4 py-3 text-xs {{ $overdue ? 'text-red-600 font-bold' : 'text-slate-500' }}">
            @if($ticket->sla_due_at)
              {{ $overdue ? '⚠ ' : '' }}{{ $ticket->sla_due_at->format('d M, h:i A') }}
              <br><span class="text-slate-400">{{ $ticket->sla_due_at->diffForHumans() }}</span>
            @else — @endif
          </td>
          <td class="px-4 py-3">
            <button @click="edit=!edit" class="text-xs text-indigo-600 font-semibold opacity-0 group-hover:opacity-100">Update</button>
            <div x-show="edit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display:none" @click.self="edit=false">
              <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
                <h3 class="font-bold mb-1">Update Ticket</h3>
                <p class="text-xs text-slate-500 mb-4">{{ $ticket->ticket_number }} — {{ $ticket->subject }}</p>
                <form method="POST" action="{{ route('crm.tickets.update',$ticket) }}" class="space-y-3">
                  @csrf @method('PUT')
                  <div class="grid grid-cols-2 gap-3">
                    <div><label class="label text-xs">Status</label>
                      <select name="status" class="input text-sm">@foreach(['open','in_progress','waiting','resolved','closed'] as $s)<option value="{{ $s }}" {{ $ticket->status===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select>
                    </div>
                    <div><label class="label text-xs">Priority</label>
                      <select name="priority" class="input text-sm">@foreach(['low','medium','high','urgent'] as $p)<option value="{{ $p }}" {{ $ticket->priority===$p?'selected':'' }}>{{ ucfirst($p) }}</option>@endforeach</select>
                    </div>
                  </div>
                  <div><label class="label text-xs">Resolution / Notes</label><textarea name="resolution" rows="3" class="input text-sm">{{ $ticket->resolution }}</textarea></div>
                  <div class="flex gap-3 pt-2">
                    <button type="button" @click="edit=false" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 crm-btn justify-center text-sm">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center py-10 text-slate-400">No tickets found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($tickets->hasPages())<div class="px-4 py-3 border-t">{{ $tickets->links() }}</div>@endif
</div>

{{-- Add Ticket Modal --}}
<div id="addTicketModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-lg">New Support Ticket</h3>
      <button onclick="document.getElementById('addTicketModal').style.display='none'" class="text-slate-400 font-bold text-xl">×</button>
    </div>
    <form method="POST" action="{{ route('crm.tickets.store') }}" class="space-y-3">
      @csrf
      <div><label class="label text-xs">Subject *</label><input type="text" name="subject" class="input text-sm" required></div>
      <div><label class="label text-xs">Contact</label>
        <select name="crm_contact_id" class="input text-sm">
          <option value="">— No contact —</option>
          @foreach($contacts as $c)<option value="{{ $c->id }}">{{ $c->name }} {{ $c->phone ? '('.$c->phone.')' : '' }}</option>@endforeach
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label text-xs">Priority</label>
          <select name="priority" class="input text-sm">@foreach(['low','medium','high','urgent'] as $p)<option>{{ $p }}</option>@endforeach</select>
        </div>
        <div><label class="label text-xs">Category</label>
          <select name="category" class="input text-sm">@foreach(['order_issue','payment','product','return','other'] as $c)<option value="{{ $c }}">{{ ucfirst(str_replace('_',' ',$c)) }}</option>@endforeach</select>
        </div>
      </div>
      <div><label class="label text-xs">Description *</label><textarea name="description" rows="4" class="input text-sm" required></textarea></div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('addTicketModal').style.display='none'" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm">Cancel</button>
        <button type="submit" class="flex-1 crm-btn justify-center text-sm">Create Ticket</button>
      </div>
    </form>
  </div>
</div>
@endsection
