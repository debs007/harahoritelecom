@extends('crm.layouts.crm')
@section('title','Loyalty Points')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Loyalty Points</span>@endsection
@section('content')
<h1 class="text-2xl font-black text-slate-900 mb-5">🌟 Loyalty Points</h1>

{{-- Summary cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  @php $cards = [
    ['🌟','Outstanding Points',number_format($totals['total_points_outstanding']),'Unredeemed','yellow'],
    ['✅','Total Earned',number_format($totals['total_points_earned']),'All time','teal'],
    ['🎁','Total Redeemed',number_format($totals['total_points_redeemed']),'All time','purple'],
    ['👥','Users with Points',$totals['total_users_with_points'],'Active holders','blue'],
  ]; @endphp
  @foreach($cards as [$icon,$label,$val,$sub,$color])
  <div class="card">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-{{ $color }}-100 flex items-center justify-center text-xl">{{ $icon }}</div>
      <div><p class="text-xs text-slate-500">{{ $label }}</p><p class="text-xl font-black text-slate-900">{{ $val }}</p><p class="text-xs text-slate-400">{{ $sub }}</p></div>
    </div>
  </div>
  @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2">

    {{-- Search --}}
    <form method="GET" class="card mb-4 flex gap-3">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or phone…" class="input">
      <button class="crm-btn">Search</button>
    </form>

    {{-- Users table --}}
    <div class="card overflow-hidden p-0">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="text-left px-4 py-3 font-semibold text-slate-600">Customer</th>
              <th class="text-left px-4 py-3 font-semibold text-slate-600">Phone</th>
              <th class="text-right px-4 py-3 font-semibold text-slate-600">Points</th>
              <th class="text-right px-4 py-3 font-semibold text-slate-600">Value (₹)</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($users as $user)
            <tr class="hover:bg-slate-50 group" x-data="{adjust:false,notify:false}">
              <td class="px-4 py-3">
                <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                <p class="text-xs text-slate-400">{{ $user->email }}</p>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ $user->phone ?? '—' }}</td>
              <td class="px-4 py-3 text-right">
                <span class="text-lg font-black text-yellow-600">{{ number_format($user->loyalty_points) }}</span>
              </td>
              <td class="px-4 py-3 text-right font-semibold text-teal-600">₹{{ number_format($user->loyalty_points * 0.25) }}</td>
              <td class="px-4 py-3">
                <div class="flex gap-2 opacity-0 group-hover:opacity-100">
                  <button @click="adjust=true" class="text-xs text-indigo-600 font-semibold">Adjust</button>
                  <button @click="notify=true" class="text-xs text-teal-600 font-semibold">Notify</button>
                </div>

                {{-- Adjust Points Modal --}}
                <div x-show="adjust" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display:none" @click.self="adjust=false">
                  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
                    <h3 class="font-bold mb-1">Adjust Points</h3>
                    <p class="text-sm text-slate-500 mb-4">{{ $user->name }} — Current: <strong>{{ number_format($user->loyalty_points) }} pts</strong></p>
                    <form method="POST" action="{{ route('crm.loyalty.adjust',$user) }}" class="space-y-3">
                      @csrf
                      <div><label class="label text-xs">Points (use negative to deduct)</label>
                        <input type="number" name="points" class="input" required placeholder="e.g. 100 or -50">
                      </div>
                      <div><label class="label text-xs">Reason *</label>
                        <input type="text" name="description" class="input" required placeholder="e.g. Bonus for referral">
                      </div>
                      <div class="flex gap-3 pt-2">
                        <button type="button" @click="adjust=false" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm">Cancel</button>
                        <button type="submit" class="flex-1 crm-btn justify-center text-sm">Apply</button>
                      </div>
                    </form>
                  </div>
                </div>

                {{-- Notify Modal --}}
                <div x-show="notify" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display:none" @click.self="notify=false">
                  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
                    <h3 class="font-bold mb-1">Send Loyalty Reminder</h3>
                    <p class="text-sm text-slate-500 mb-4">{{ $user->name }} — {{ number_format($user->loyalty_points) }} pts = ₹{{ number_format($user->loyalty_points * 0.25) }}</p>
                    <div class="flex gap-3">
                      <a href="{{ route('crm.loyalty.notify',$user) }}?channel=whatsapp" target="_blank"
                         class="flex-1 text-center bg-green-500 text-white font-bold py-3 rounded-xl hover:bg-green-600 transition">💬 WhatsApp</a>
                      <a href="{{ route('crm.loyalty.notify',$user) }}?channel=sms" target="_blank"
                         class="flex-1 text-center bg-blue-500 text-white font-bold py-3 rounded-xl hover:bg-blue-600 transition">📱 SMS</a>
                    </div>
                    <button @click="notify=false" class="w-full mt-3 text-sm text-slate-400 hover:text-slate-600">Close</button>
                  </div>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-10 text-slate-400">No users with loyalty points yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($users->hasPages())<div class="px-4 py-3 border-t">{{ $users->links() }}</div>@endif
    </div>
  </div>

  {{-- Recent transactions --}}
  <div class="card">
    <h3 class="font-bold text-slate-800 mb-4">Recent Transactions</h3>
    <div class="space-y-3">
      @foreach($recent as $tx)
      <div class="flex items-start gap-2.5 pb-3 border-b border-slate-100 last:border-0 last:pb-0">
        <span class="text-base mt-0.5">{{ match($tx->type){'earned'=>'⬆️','redeemed'=>'⬇️','bonus'=>'🎁','adjusted'=>'✏️',default=>'🔄'} }}</span>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-slate-700 truncate">{{ $tx->user?->name }}</p>
          <p class="text-xs text-slate-400">{{ $tx->description }}</p>
          <p class="text-xs text-slate-300">{{ $tx->created_at->diffForHumans() }}</p>
        </div>
        <span class="text-sm font-black {{ $tx->points > 0 ? 'text-teal-600' : 'text-red-500' }}">
          {{ $tx->points > 0 ? '+' : '' }}{{ $tx->points }}
        </span>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
