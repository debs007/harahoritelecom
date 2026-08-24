@extends('crm.layouts.crm')
@section('title','CRM Settings')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Settings</span>@endsection
@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <h1 class="text-2xl font-black text-slate-900">⚙️ CRM Settings</h1>
    <form method="POST" action="{{ route('crm.settings.reclassify') }}">@csrf
        <button class="crm-btn bg-amber-500 hover:bg-amber-600">
            🔄 Re-classify All Contacts
        </button>
    </form>
</div>

<form method="POST" action="{{ route('crm.settings.update') }}" class="space-y-6">
    @csrf

    {{-- Segment Price Ranges --}}
    <div class="card">
        <h3 class="font-bold text-slate-800 mb-1">🎯 Customer Segment Price Ranges (₹)</h3>
        <p class="text-xs text-slate-500 mb-5">
            Define the order value boundaries for each segment.
            After changing, click <strong>Re-classify All Contacts</strong> to apply to existing customers.
        </p>

        @php
        $segs = [
            ['key'=>'budget',    'label'=>'Budget',     'color'=>'gray',   'icon'=>'🪙'],
            ['key'=>'mid',       'label'=>'Mid-Range',  'color'=>'blue',   'icon'=>'📱'],
            ['key'=>'upper_mid', 'label'=>'Upper Mid',  'color'=>'indigo', 'icon'=>'💎'],
            ['key'=>'premium',   'label'=>'Premium',    'color'=>'purple', 'icon'=>'⭐'],
            ['key'=>'flagship',  'label'=>'Flagship',   'color'=>'yellow', 'icon'=>'👑'],
        ];
        @endphp

        <div class="space-y-3">
            @foreach($segs as $seg)
            @php
                $minKey = 'segment_'.$seg['key'].'_min';
                $maxKey = 'segment_'.$seg['key'].'_max';
                $minVal = $settings[$minKey]->value ?? '';
                $maxVal = $settings[$maxKey]->value ?? '';
            @endphp
            <div class="flex items-center gap-4 p-3 bg-{{ $seg['color'] }}-50 border border-{{ $seg['color'] }}-200 rounded-xl">
                <div class="w-32 flex items-center gap-2">
                    <span class="text-lg">{{ $seg['icon'] }}</span>
                    <span class="font-bold text-sm text-slate-800">{{ $seg['label'] }}</span>
                </div>
                <div class="flex items-center gap-3 flex-1">
                    <div class="flex-1">
                        <label class="label text-xs">Min (₹)</label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                            <input type="number" name="{{ $minKey }}" value="{{ $minVal }}"
                                   class="input pl-6 text-sm font-semibold" min="0" required>
                        </div>
                    </div>
                    <span class="text-slate-400 mt-5">—</span>
                    <div class="flex-1">
                        <label class="label text-xs">Max (₹)</label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                            <input type="number" name="{{ $maxKey }}" value="{{ $maxVal }}"
                                   class="input pl-6 text-sm font-semibold" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="w-32 text-right">
                    <p class="text-xs text-slate-500 font-semibold">Range</p>
                    <p class="text-sm font-black text-slate-700">
                        ₹{{ number_format($minVal) }} – ₹{{ number_format($maxVal) }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700">
            <strong>How classification works:</strong> When a customer places an order,
            their average order value is calculated and matched against these ranges.
            Their segment updates automatically. Orders below the Budget minimum are "Unclassified".
        </div>
    </div>

    {{-- Loyalty Settings --}}
    <div class="card">
        <h3 class="font-bold text-slate-800 mb-1">🌟 Loyalty Point Settings</h3>
        <p class="text-xs text-slate-500 mb-4">Changes apply immediately to all new purchases and notifications.</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="label">1 Point = ₹ (INR value) *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">₹</span>
                    <input type="number" name="loyalty_point_value"
                           value="{{ $settings['loyalty_point_value']->value ?? '0.25' }}"
                           step="0.01" min="0.01" class="input pl-7 font-bold" required>
                </div>
                <p class="text-xs text-slate-400 mt-1">e.g. 0.25 = 100 pts worth ₹25</p>
            </div>
            <div>
                <label class="label">Points earned per ₹100 spent *</label>
                <input type="number" name="loyalty_points_per_100"
                       value="{{ $settings['loyalty_points_per_100']->value ?? '1' }}"
                       step="0.1" min="0.1" class="input font-bold" required>
                <p class="text-xs text-slate-400 mt-1">e.g. 1 = 1 point per ₹100</p>
            </div>
            <div>
                <label class="label">Max redemption % of order *</label>
                <div class="relative">
                    <input type="number" name="loyalty_max_redemption"
                           value="{{ $settings['loyalty_max_redemption']->value ?? '10' }}"
                           step="1" min="1" max="100" class="input pr-7 font-bold" required>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Max % of order payable by points</p>
            </div>
        </div>
    </div>

    <button type="submit" class="crm-btn px-8">💾 Save All Settings</button>
</form>
@endsection
