@extends('crm.layouts.crm')
@section('title','Contacts')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Contacts</span>@endsection
@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <h1 class="text-2xl font-black text-slate-900">Contacts <span class="text-slate-400 font-normal text-lg">({{ $contacts->total() }})</span></h1>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('crm.contacts.sync') }}">@csrf
            <button class="crm-btn text-sm">🔄 Sync All Customers</button>
        </form>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="crm-btn text-sm">+ Add Contact</button>
    </div>
</div>

{{-- ── TABS ── --}}
@php
$tabs = [
    'all'        => ['label' => '👥 All',          'color' => 'teal'],
    'buyers'     => ['label' => '🛍️ Buyers',       'color' => 'indigo'],
    'registered' => ['label' => '📱 Registered',   'color' => 'blue'],
    'tally'      => ['label' => '📁 Tally Import', 'color' => 'orange'],
    'manual'     => ['label' => '✏️ Manual/Prospect','color'=>'slate'],
];
@endphp
<div class="flex gap-1 bg-white border border-slate-200 rounded-xl p-1 mb-5 overflow-x-auto">
    @foreach($tabs as $key => $t)
    <a href="{{ route('crm.contacts.index', array_merge(request()->except(['tab','page']), ['tab' => $key])) }}"
       class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap
              {{ $tab === $key ? 'bg-'.$t['color'].'-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100' }}">
        {{ $t['label'] }}
        <span class="text-xs {{ $tab === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }} px-1.5 py-0.5 rounded-full font-black">
            {{ number_format($tabCounts[$key]) }}
        </span>
    </a>
    @endforeach
</div>

{{-- Tab description --}}
@php $tabDesc = [
    'all'        => 'All contacts across every source.',
    'buyers'     => 'Customers who have placed at least one order.',
    'registered' => 'Users who signed up in the app but haven\'t ordered yet. Great for conversion campaigns.',
    'tally'      => 'Contacts imported from Tally ERP exports.',
    'manual'     => 'Contacts added manually or marked as prospects.',
]; @endphp
<div class="text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 mb-5">
    {{ $tabDesc[$tab] ?? '' }}
    @if($tab === 'registered' && $tabCounts['registered'] > 0)
    <span class="ml-2 font-semibold text-indigo-600">
        💡 Run a campaign targeting these users to get their first order!
    </span>
    @endif
</div>

{{-- Segment chips --}}
@php $segDefs=['budget'=>['Budget','gray'],'mid_range'=>['Mid-Range','blue'],'upper_mid'=>['Upper Mid','indigo'],'premium'=>['Premium','purple'],'flagship'=>['Flagship','yellow'],'unclassified'=>['Unclassified','slate']]; @endphp

{{-- Filters --}}
<form method="GET" action="{{ route('crm.contacts.index') }}" class="card mb-5 flex flex-wrap gap-3 items-end">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / phone / email…" class="input" style="max-width:220px">
    <select name="segment" class="input" style="max-width:160px">
        <option value="">All Segments</option>
        @foreach(['budget'=>'Budget','mid_range'=>'Mid-Range','upper_mid'=>'Upper Mid','premium'=>'Premium','flagship'=>'Flagship','unclassified'=>'Unclassified'] as $k=>$label)
        <option value="{{ $k }}" {{ request('segment')===$k?'selected':'' }}>{{ $label }}</option>
        @endforeach
    </select>
    <select name="city" class="input" style="max-width:160px"><option value="">All Cities</option>@foreach($cities as $c)<option {{ request('city')===$c?'selected':'' }}>{{ $c }}</option>@endforeach</select>
    <select name="state" class="input" style="max-width:160px"><option value="">All States</option>@foreach($states as $s)<option {{ request('state')===$s?'selected':'' }}>{{ $s }}</option>@endforeach</select>
    <select name="status" class="input" style="max-width:140px">
        <option value="">All Status</option>
        @foreach(['active','prospect','inactive','churned'] as $s)<option {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
    </select>
    <select name="sort" class="input" style="max-width:150px">
        <option value="">Latest</option>
        <option value="spend" {{ request('sort')==='spend'?'selected':'' }}>Highest Spend</option>
        <option value="orders" {{ request('sort')==='orders'?'selected':'' }}>Most Orders</option>
        <option value="recent" {{ request('sort')==='recent'?'selected':'' }}>Recently Contacted</option>
    </select>
    <label class="flex items-center gap-1.5 text-sm font-medium text-slate-700 cursor-pointer">
        <input type="checkbox" name="multi_buyer" value="1" {{ request('multi_buyer')?'checked':'' }} class="rounded">
        Repeat Buyers Only
    </label>
    <button class="crm-btn">Filter</button>
    <a href="{{ route('crm.contacts.index') }}" class="text-sm text-slate-400 hover:text-slate-600">Reset</a>
</form>

{{-- Table --}}
<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Contact</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Phone</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Location</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Segment</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Spend</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Orders</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Due</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($contacts as $c)
                <tr class="hover:bg-slate-50 group">
                    <td class="px-4 py-3">
                        <a href="{{ route('crm.contacts.show',$c) }}" class="font-semibold text-slate-800 hover:text-teal-600">{{ $c->name }}</a>
                        @if($c->email)<p class="text-xs text-slate-400">{{ $c->email }}</p>@endif
                        <span class="badge text-xs mt-0.5 {{ match($c->contact_type ?? 'prospect') {
                            'buyer'        => 'badge-indigo',
                            'registered'   => 'badge-blue',
                            'tally_import' => 'badge-orange',
                            default        => 'badge-gray',
                        } }}">{{ match($c->contact_type ?? 'prospect') {
                            'buyer'        => '🛍️ Buyer',
                            'registered'   => '📱 Registered',
                            'tally_import' => '📁 Tally',
                            default        => '✏️ Manual',
                        } }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $c->phone ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ collect([$c->city,$c->state])->filter()->implode(', ') ?: '—' }}</td>
                    <td class="px-4 py-3"><span class="badge badge-{{ $segDefs[$c->segment][1] ?? 'gray' }}">{{ $segDefs[$c->segment][0] ?? $c->segment }}</span></td>
                    <td class="px-4 py-3 font-semibold text-slate-800">₹{{ number_format($c->total_spent) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="{{ $c->total_orders >= 2 ? 'badge badge-teal' : 'text-slate-500' }}">{{ $c->total_orders }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge {{ match($c->status){'active'=>'badge-green','prospect'=>'badge-blue','churned'=>'badge-red',default=>'badge-gray'} }}">{{ ucfirst($c->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs {{ $c->due_date && $c->due_date->isPast() ? 'text-red-600 font-bold' : 'text-slate-400' }}">
                        {{ $c->due_date?->format('d M') ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('crm.contacts.show',$c) }}" class="text-teal-600 hover:text-teal-800 text-xs font-semibold opacity-0 group-hover:opacity-100">View →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-10 text-slate-400">No contacts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contacts->hasPages())<div class="px-4 py-3 border-t">{{ $contacts->links() }}</div>@endif
</div>

{{-- Add Contact Modal --}}
<div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">Add New Contact</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold text-xl">×</button>
        </div>
        <form method="POST" action="{{ route('crm.contacts.store') }}" class="grid grid-cols-2 gap-3">
            @csrf
            <div class="col-span-2"><label class="label">Name *</label><input type="text" name="name" class="input" required></div>
            <div><label class="label">Phone</label><input type="text" name="phone" class="input"></div>
            <div><label class="label">WhatsApp</label><input type="text" name="whatsapp" class="input"></div>
            <div class="col-span-2"><label class="label">Email</label><input type="email" name="email" class="input"></div>
            <div><label class="label">City</label><input type="text" name="city" class="input"></div>
            <div><label class="label">State</label><input type="text" name="state" class="input"></div>
            <div><label class="label">Segment</label>
                <select name="segment" class="input">
                    @foreach($segDefs as $k=>[$l,$c])<option value="{{ $k }}">{{ $l }}</option>@endforeach
                </select>
            </div>
            <div><label class="label">Source</label>
                <select name="source" class="input">
                    @foreach(['organic','referral','campaign','walk_in','social','other'] as $s)<option>{{ $s }}</option>@endforeach
                </select>
            </div>
            <div><label class="label">Due Date</label><input type="date" name="due_date" class="input"></div>
            <div class="col-span-2"><label class="label">Notes</label><textarea name="notes" rows="2" class="input"></textarea></div>
            <div class="col-span-2 flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm hover:bg-slate-50">Cancel</button>
                <button type="submit" class="flex-1 crm-btn justify-center">Save Contact</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('addModal').addEventListener('click',function(e){if(e.target===this)this.classList.add('hidden');});
document.getElementById('addModal').style.display='';
</script>
@endsection
