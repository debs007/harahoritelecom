@extends('crm.layouts.crm')
@section('title','Analytics')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Analytics</span>@endsection
@section('content')
<h1 class="text-2xl font-black text-slate-900 mb-6">📈 Analytics & Reporting</h1>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- Revenue chart --}}
  <div class="card lg:col-span-2">
    <h3 class="font-bold text-slate-800 mb-4">Monthly Revenue & Orders (Last 12 Months)</h3>
    <canvas id="revenueChart" height="60"></canvas>
  </div>

  {{-- Segment Revenue --}}
  <div class="card">
    <h3 class="font-bold text-slate-800 mb-4">Revenue by Customer Segment</h3>
    @php $segColors=['budget'=>'gray','mid_range'=>'blue','upper_mid'=>'indigo','premium'=>'purple','flagship'=>'yellow','unclassified'=>'slate'];
    $segLabels=['budget'=>'Budget','mid_range'=>'Mid-Range','upper_mid'=>'Upper Mid','premium'=>'Premium','flagship'=>'Flagship','unclassified'=>'Unclassified'];
    $totalSegRev = $segmentRevenue->sum('revenue') ?: 1; @endphp
    <div class="space-y-3">
      @foreach($segmentRevenue->sortByDesc('revenue') as $row)
      @php $pct = round($row->revenue / $totalSegRev * 100); $col = $segColors[$row->segment] ?? 'slate'; @endphp
      <div>
        <div class="flex justify-between text-sm mb-1">
          <span class="font-semibold text-slate-700">{{ $segLabels[$row->segment] ?? $row->segment }}</span>
          <div class="flex gap-3 text-xs text-slate-500">
            <span>{{ $row->orders }} orders</span>
            <span class="font-bold text-slate-800">₹{{ number_format($row->revenue) }}</span>
          </div>
        </div>
        <div class="h-2 bg-slate-100 rounded-full"><div class="h-full bg-{{ $col }}-400 rounded-full" style="width:{{ $pct }}%"></div></div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Top Cities --}}
  <div class="card">
    <h3 class="font-bold text-slate-800 mb-4">Top Cities by Revenue</h3>
    <div class="space-y-2">
      @foreach($cityRevenue as $i=>$city)
      <div class="flex items-center gap-3">
        <span class="w-6 h-6 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-xs font-black flex-shrink-0">{{ $i+1 }}</span>
        <div class="flex-1"><p class="text-sm font-semibold text-slate-800">{{ $city->city }}</p></div>
        <div class="text-right">
          <p class="text-sm font-bold text-teal-600">₹{{ number_format($city->revenue) }}</p>
          <p class="text-xs text-slate-400">{{ $city->orders }} orders</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Lead Funnel --}}
  <div class="card">
    <h3 class="font-bold text-slate-800 mb-4">Lead Conversion Funnel</h3>
    @php $funnelStages=['new','contacted','qualified','proposal','negotiation','won','lost'];
    $fColors=['new'=>'slate','contacted'=>'blue','qualified'=>'indigo','proposal'=>'purple','negotiation'=>'orange','won'=>'green','lost'=>'red'];
    $totalLeads = $leadFunnel->sum('count') ?: 1; @endphp
    <div class="space-y-2">
      @foreach($funnelStages as $s)
      @php $row=$leadFunnel[$s] ?? null; $count=$row?->count ?? 0; $val=$row?->value ?? 0; $pct=round($count/$totalLeads*100); $col=$fColors[$s]; @endphp
      <div>
        <div class="flex justify-between text-xs mb-0.5">
          <span class="font-bold text-slate-700 capitalize">{{ $s }}</span>
          <span class="text-slate-500">{{ $count }} leads · ₹{{ number_format($val/1000,1) }}K</span>
        </div>
        <div class="h-2 bg-slate-100 rounded-full"><div class="h-full bg-{{ $col }}-400 rounded-full" style="width:{{ $pct }}%"></div></div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Ticket Metrics --}}
  <div class="card">
    <h3 class="font-bold text-slate-800 mb-4">Support Ticket Metrics</h3>
    <div class="grid grid-cols-2 gap-3 mb-4">
      <div class="bg-slate-50 rounded-xl p-3 text-center">
        <p class="text-2xl font-black text-slate-800">{{ round($ticketMetrics['avg_resolution_hours'] ?? 0) }}h</p>
        <p class="text-xs text-slate-500">Avg. Resolution Time</p>
      </div>
      <div class="bg-red-50 rounded-xl p-3 text-center">
        <p class="text-2xl font-black text-red-600">{{ $ticketMetrics['overdue_count'] }}</p>
        <p class="text-xs text-slate-500">SLA Breaches</p>
      </div>
    </div>
    <div class="space-y-1.5">
      @foreach($ticketMetrics['by_status'] as $s=>$c)
      <div class="flex justify-between text-sm"><span class="capitalize text-slate-600">{{ str_replace('_',' ',$s) }}</span><span class="font-bold text-slate-800">{{ $c }}</span></div>
      @endforeach
    </div>
  </div>

  {{-- Customer Loyalty --}}
  <div class="card">
    <h3 class="font-bold text-slate-800 mb-4">🌟 Loyalty Overview</h3>
    <div class="grid grid-cols-2 gap-3 mb-4">
      <div class="bg-yellow-50 rounded-xl p-3 text-center">
        <p class="text-xl font-black text-yellow-600">{{ number_format($loyaltyStats['total_outstanding']) }}</p>
        <p class="text-xs text-slate-500">Outstanding Points</p>
      </div>
      <div class="bg-teal-50 rounded-xl p-3 text-center">
        <p class="text-xl font-black text-teal-600">{{ number_format($loyaltyStats['earned_this_month']) }}</p>
        <p class="text-xs text-slate-500">Earned This Month</p>
      </div>
    </div>
    <h4 class="font-semibold text-slate-700 text-xs mb-2">Top Earners</h4>
    @foreach($loyaltyStats['top_earners'] as $u)
    <div class="flex justify-between items-center py-1.5 border-b border-slate-100 last:border-0">
      <span class="text-sm text-slate-700">{{ $u->name }}</span>
      <span class="text-sm font-black text-yellow-600">{{ number_format($u->loyalty_points) }} pts</span>
    </div>
    @endforeach
  </div>

  {{-- Buyer Behaviour --}}
  <div class="card">
    <h3 class="font-bold text-slate-800 mb-4">🔁 Customer Purchase Behaviour</h3>
    <canvas id="buyerChart" height="160"></canvas>
    <div class="grid grid-cols-3 gap-3 mt-4 text-center text-xs">
      <div class="bg-slate-50 rounded-xl p-2"><p class="text-xl font-black text-slate-700">{{ number_format($multiBuyers['1_order']) }}</p><p class="text-slate-500">One-time</p></div>
      <div class="bg-blue-50 rounded-xl p-2"><p class="text-xl font-black text-blue-600">{{ number_format($multiBuyers['2_orders']) }}</p><p class="text-slate-500">2 orders</p></div>
      <div class="bg-teal-50 rounded-xl p-2"><p class="text-xl font-black text-teal-600">{{ number_format($multiBuyers['3_plus']) }}</p><p class="text-slate-500">3+ orders</p></div>
    </div>
  </div>

</div>
@endsection
@push('scripts')
<script>
// Revenue chart
const rLabels = @json($monthlyRevenue->pluck('month'));
const rData   = @json($monthlyRevenue->pluck('revenue'));
const oData   = @json($monthlyRevenue->pluck('orders'));
new Chart(document.getElementById('revenueChart'), {
  type:'bar',
  data:{labels:rLabels,datasets:[
    {label:'Revenue (₹)',data:rData,backgroundColor:'rgba(13,148,136,.7)',borderRadius:4,yAxisID:'y'},
    {label:'Orders',data:oData,backgroundColor:'rgba(99,102,241,.5)',borderRadius:4,type:'line',yAxisID:'y1',tension:.4,fill:false,borderColor:'rgba(99,102,241,.8)'},
  ]},
  options:{responsive:true,interaction:{mode:'index'},plugins:{legend:{position:'top'}},scales:{
    y:{ticks:{callback:v=>'₹'+v.toLocaleString('en-IN')}},
    y1:{position:'right',grid:{drawOnChartArea:false}}
  }}
});
// Buyer donut
new Chart(document.getElementById('buyerChart'),{
  type:'doughnut',
  data:{labels:['One-time','2 orders','3+ orders'],
    datasets:[{data:[{{ $multiBuyers['1_order'] }},{{ $multiBuyers['2_orders'] }},{{ $multiBuyers['3_plus'] }}],
      backgroundColor:['#94a3b8','#60a5fa','#2dd4bf'],borderWidth:0}]},
  options:{responsive:true,cutout:'65%',plugins:{legend:{display:false}}}
});
</script>
@endpush
