@extends('crm.layouts.crm')
@section('title','Dashboard')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Dashboard</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <h1 class="text-2xl font-black text-slate-900">CRM Dashboard</h1>
    <form method="POST" action="{{ route('crm.contacts.sync') }}">@csrf
        <button class="crm-btn">🔄 Sync Contacts from Orders</button>
    </form>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php $statCards = [
        ['💰','Total Revenue','₹'.number_format($stats['total_revenue']),'This month: ₹'.number_format($stats['this_month_rev']),'teal'],
        ['👥','Total Contacts',$stats['total_contacts'],'CRM profiles','blue'],
        ['💼','Active Leads',$stats['active_leads'],'In pipeline','indigo'],
        ['🌟','Points Awarded',number_format($stats['points_awarded']),'Loyalty earned','yellow'],
        ['🎫','Open Tickets',$stats['open_tickets'],'Support cases','orange'],
        ['🔁','Repeat Buyers',$stats['repeat_customers'],'2+ orders','green'],
        ['📣','Campaigns Sent',$stats['campaigns_sent'],'Completed','purple'],
        ['📦','Pending Sync',\App\Models\User::has('orders')->whereDoesntHave('crmContact')->count(),'Users without CRM','gray'],
    ] @endphp
    @foreach($statCards as [$icon,$label,$value,$sub,$color])
    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-{{ $color }}-100 flex items-center justify-center text-xl flex-shrink-0">{{ $icon }}</div>
            <div>
                <p class="text-xs text-slate-500 font-medium">{{ $label }}</p>
                <p class="text-xl font-black text-slate-900">{{ $value }}</p>
                <p class="text-xs text-slate-400">{{ $sub }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left col --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Revenue Chart --}}
        <div class="card">
            <h3 class="font-bold text-slate-800 mb-4">📈 Revenue — Last 6 Months</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        {{-- Segment Breakdown --}}
        <div class="card">
            <h3 class="font-bold text-slate-800 mb-4">🎯 Contact Segments</h3>
            @php
            $segColors=['budget'=>'gray','mid_range'=>'blue','upper_mid'=>'indigo','premium'=>'purple','flagship'=>'yellow','unclassified'=>'slate'];
            $segLabels=['budget'=>'Budget (₹9K–20K)','mid_range'=>'Mid-Range (₹20K–40K)','upper_mid'=>'Upper Mid (₹40K–70K)','premium'=>'Premium (₹70K–1L)','flagship'=>'Flagship (₹1L–1.45L)','unclassified'=>'Unclassified'];
            $total = array_sum($segments) ?: 1;
            @endphp
            <div class="space-y-3">
                @foreach($segLabels as $key=>$label)
                @php $count = $segments[$key] ?? 0; $pct = round($count/$total*100); @endphp
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-semibold text-slate-700">{{ $label }}</span>
                        <span class="badge badge-{{ $segColors[$key] }}">{{ $count }} contacts</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $segColors[$key] }}-400 rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pipeline --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">💼 Sales Pipeline</h3>
                <a href="{{ route('crm.leads.index') }}" class="text-xs text-teal-600 font-semibold hover:underline">View all →</a>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                @php $pipeColors=['new'=>'slate','contacted'=>'blue','qualified'=>'indigo','proposal'=>'purple','negotiation'=>'orange']; @endphp
                @foreach($pipeline as $row)
                <div class="text-center bg-{{ $pipeColors[$row->stage] ?? 'slate' }}-50 rounded-xl p-3 border border-{{ $pipeColors[$row->stage] ?? 'slate' }}-200">
                    <p class="text-lg font-black text-slate-800">{{ $row->count }}</p>
                    <p class="text-xs text-slate-500 capitalize font-medium">{{ $row->stage }}</p>
                    <p class="text-xs font-bold text-teal-600 mt-0.5">₹{{ number_format($row->total_value/1000,1) }}K</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right col --}}
    <div class="space-y-6">

        {{-- Top contacts --}}
        <div class="card">
            <h3 class="font-bold text-slate-800 mb-3">🏆 Top Customers</h3>
            <div class="space-y-2">
                @foreach($topContacts as $i=>$c)
                <a href="{{ route('crm.contacts.show',$c) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 transition">
                    <span class="w-6 h-6 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-xs font-black">{{ $i+1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $c->name }}</p>
                        <p class="text-xs text-slate-400">₹{{ number_format($c->total_spent) }}</p>
                    </div>
                    <span class="badge badge-{{ $c->segment_color }}">{{ Str::limit($c->segment_label,10) }}</span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Overdue Tickets --}}
        @if($overdueTickets->isNotEmpty())
        <div class="card border-red-200 bg-red-50">
            <h3 class="font-bold text-red-800 mb-3">⚠️ Overdue Tickets</h3>
            <div class="space-y-2">
                @foreach($overdueTickets as $t)
                <div class="bg-white rounded-lg p-2.5 border border-red-100">
                    <p class="text-xs font-bold text-red-700">{{ $t->ticket_number }}</p>
                    <p class="text-sm text-slate-700 font-medium truncate">{{ $t->subject }}</p>
                    <p class="text-xs text-slate-400">SLA: {{ $t->sla_due_at?->diffForHumans() }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recent Interactions --}}
        <div class="card">
            <h3 class="font-bold text-slate-800 mb-3">🕐 Recent Activity</h3>
            <div class="space-y-2.5">
                @foreach($recentInteractions as $int)
                <div class="flex items-start gap-2.5">
                    <span class="text-base mt-0.5">{{ match($int->type){'visit'=>'👁','call'=>'📞','whatsapp'=>'💬','sms'=>'📱','email'=>'📧','note'=>'📝','purchase'=>'🛍','support'=>'🎫',default=>'•'} }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $int->contact?->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Str::limit($int->description,50) }}</p>
                        <p class="text-xs text-slate-300">{{ $int->interacted_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const labels = @json($monthlyRevenue->pluck('month'));
const data   = @json($monthlyRevenue->pluck('revenue'));
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: { labels, datasets: [{ label: 'Revenue (₹)', data, backgroundColor: 'rgba(13,148,136,.7)', borderRadius: 6 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: v => '₹'+v.toLocaleString('en-IN') } } } }
});
</script>
@endpush
