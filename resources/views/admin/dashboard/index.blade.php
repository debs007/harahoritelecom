@extends('layouts.admin')
@section('title','Dashboard')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->format('l, d F Y') }}</p>
        </div>
        <a href="{{ route('admin.analytics.export') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @php
            $cards = [
                ['label'=>'Total Orders',    'value'=>number_format($stats['total_orders']),                       'color'=>'from-violet-500 to-purple-600', 'icon'=>'🛍️'],
                ['label'=>'Pending Orders',  'value'=>number_format($stats['pending_orders']),                     'color'=>'from-orange-400 to-amber-500',  'icon'=>'⏳'],
                ['label'=>'Revenue',         'value'=>'₹'.number_format($stats['total_revenue']/100000,1).'L',    'color'=>'from-green-500 to-emerald-600', 'icon'=>'💰'],
                ['label'=>'Products',        'value'=>number_format($stats['total_products']),                     'color'=>'from-blue-500 to-cyan-600',     'icon'=>'📱'],
                ['label'=>'Customers',       'value'=>number_format($stats['total_users']),                        'color'=>'from-pink-500 to-rose-600',     'icon'=>'👥'],
                ['label'=>'Pending Reviews', 'value'=>number_format($stats['pending_reviews']),                    'color'=>'from-yellow-500 to-amber-600',  'icon'=>'⭐'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="bg-gradient-to-br {{ $card['color'] }} rounded-2xl p-4 text-white shadow-md">
            <div class="text-2xl mb-2">{{ $card['icon'] }}</div>
            <div class="text-2xl font-black">{{ $card['value'] }}</div>
            <div class="text-white/80 text-xs font-medium mt-0.5">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Monthly Revenue ({{ now()->year }})</h3>
            <canvas id="revenueChart" height="180"></canvas>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Orders by Status</h3>
            <canvas id="statusChart" height="180"></canvas>
        </div>
    </div>

    {{-- Bottom row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Recent orders --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 text-sm font-medium hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentOrders as $order)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-xs font-bold text-indigo-600 flex-shrink-0">
                            {{ strtoupper(substr($order->user->name,0,2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $order->order_number }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-3">
                        <p class="text-sm font-black text-gray-900">₹{{ number_format($order->total) }}</p>
                        @php $c = $order->getStatusBadgeColor() @endphp
                        <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 capitalize text-xs">{{ str_replace('_',' ',$order->status) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top products --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Top Selling Products</h3>
                <a href="{{ route('admin.products.index') }}" class="text-indigo-600 text-sm font-medium hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($topProducts as $i => $product)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition">
                    <span class="text-xl font-black text-gray-200 w-6 flex-shrink-0">{{ $i+1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $product->product_name }}</p>
                        <p class="text-xs text-gray-400">{{ $product->total_sold }} units sold</p>
                    </div>
                    <p class="text-sm font-black text-gray-900 flex-shrink-0">₹{{ number_format($product->revenue) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const monthData = @json($monthlySales);
const revenueData = months.map((_,i) => monthData[i+1]?.total ?? 0);

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{ label: 'Revenue (₹)', data: revenueData, backgroundColor: 'rgba(99,102,241,0.8)', borderRadius: 6, borderSkipped: false }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { callback: v => '₹'+Math.round(v/1000)+'K' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending','Confirmed','Processing','Shipped','Delivered','Cancelled'],
        datasets: [{
            data: [
                {{ \App\Models\Order::where('status','pending')->count() }},
                {{ \App\Models\Order::where('status','confirmed')->count() }},
                {{ \App\Models\Order::where('status','processing')->count() }},
                {{ \App\Models\Order::where('status','shipped')->count() }},
                {{ \App\Models\Order::where('status','delivered')->count() }},
                {{ \App\Models\Order::where('status','cancelled')->count() }},
            ],
            backgroundColor: ['#fbbf24','#3b82f6','#6366f1','#a855f7','#10b981','#ef4444'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } }
    }
});
</script>
@endpush
