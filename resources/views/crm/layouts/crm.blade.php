<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','CRM') — Harahori CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <style>
        body{font-family:'Inter',sans-serif;}
        .input{width:100%;border:1px solid #cbd5e1;border-radius:.5rem;padding:.5rem .75rem;font-size:.875rem;outline:none;transition:all .15s;}
        .input:focus{border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,.15);}
        .label{display:block;font-size:.875rem;font-weight:600;color:#374151;margin-bottom:.375rem;}
        .badge{display:inline-flex;align-items:center;padding:.15rem .55rem;border-radius:9999px;font-size:.7rem;font-weight:700;letter-spacing:.02em;}
        .badge-teal{background:#ccfbef;color:#065f46;} .badge-green{background:#d1fae5;color:#065f46;}
        .badge-red{background:#fee2e2;color:#991b1b;} .badge-yellow{background:#fef3c7;color:#92400e;}
        .badge-blue{background:#dbeafe;color:#1e40af;} .badge-purple{background:#ede9fe;color:#5b21b6;}
        .badge-orange{background:#ffedd5;color:#9a3412;} .badge-indigo{background:#e0e7ff;color:#3730a3;}
        .badge-gray{background:#f1f5f9;color:#475569;} .badge-slate{background:#f1f5f9;color:#475569;}
        .card{background:white;border-radius:1rem;border:1px solid #e2e8f0;padding:1.25rem;}
        .crm-btn{display:inline-flex;align-items:center;gap:.375rem;background:#0d9488;color:white;padding:.5rem 1rem;border-radius:.5rem;font-size:.875rem;font-weight:600;transition:background .15s;cursor:pointer;border:none;}
        .crm-btn:hover{background:#0f766e;}
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 antialiased" x-data="{sidebar:false}">
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-200 lg:translate-x-0"
       :class="sidebar?'translate-x-0':'-translate-x-full lg:translate-x-0'">
    <div class="flex items-center gap-3 px-5 h-16 border-b border-slate-800">
        <div class="w-8 h-8 bg-gradient-to-br from-teal-500 to-cyan-400 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div><p class="font-black text-sm leading-tight">Harahori CRM</p><p class="text-slate-500 text-xs">Customer Intelligence</p></div>
    </div>
    <nav class="px-3 py-4 overflow-y-auto" style="height:calc(100vh - 4rem)">
        <div class="space-y-0.5 mb-6">
            @php $nav=[['crm.dashboard','Dashboard','📊'],['crm.contacts.index','Contacts','👥'],['crm.leads.index','Sales Pipeline','💼'],['crm.campaigns.index','Campaigns','📣'],['crm.loyalty.index','Loyalty Points','🌟'],['crm.tickets.index','Support','🎫'],['crm.tally.index','Tally Import','📁'],['crm.analytics','Analytics','📈'],['crm.settings.index','Settings','⚙️']]; @endphp
            @foreach($nav as [$route,$label,$icon])
            <a href="{{ route($route) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs($route.'*')?'bg-teal-600 text-white shadow':'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <span>{{ $icon }}</span>{{ $label }}
                @if($label==='Support')@php $ov=\App\Models\CrmTicket::whereNotIn('status',['resolved','closed'])->where('sla_due_at','<',now())->count(); @endphp
                    @if($ov>0)<span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5">{{ $ov }}</span>@endif
                @endif
            </a>
            @endforeach
        </div>
        <div class="border-t border-slate-800 pt-4 space-y-0.5">
            <a href="{{ route('admin.dashboard') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-500 hover:bg-slate-800 hover:text-white transition">🔧 Admin Panel</a>
            <form method="POST" action="{{ route('crm.logout') }}">@csrf
                <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-900/20 hover:text-red-300 transition">🚪 Lock CRM</button>
            </form>
        </div>
    </nav>
</aside>
<div class="lg:pl-64 min-h-screen flex flex-col">
    <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-40 shadow-sm">
        <button @click="sidebar=!sidebar" class="lg:hidden text-slate-500 p-1"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <div class="hidden sm:flex items-center gap-1 text-sm text-slate-400"><span>CRM</span>@yield('breadcrumb')</div>
        <span class="hidden sm:inline-flex items-center gap-1.5 text-xs bg-teal-50 text-teal-700 border border-teal-200 px-3 py-1.5 rounded-full font-semibold">🔐 Verified Session</span>
    </header>
    @foreach(['success'=>'teal','error'=>'red'] as $type=>$color)
    @if(session($type))
    <div class="mx-4 sm:mx-6 mt-4 bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-800 rounded-xl px-4 py-3 text-sm flex justify-between" x-data x-init="setTimeout(()=>$el.remove(),4000)">
        <span>{{ session($type) }}</span><button @click="$el.remove()" class="font-bold ml-4">✕</button>
    </div>
    @endif
    @endforeach
    <main class="flex-1 p-4 sm:p-6">@yield('content')</main>
</div>
<div x-show="sidebar" @click="sidebar=false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition></div>
@stack('scripts')
</body>
</html>
