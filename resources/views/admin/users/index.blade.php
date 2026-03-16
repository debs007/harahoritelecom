@extends('layouts.admin')
@section('title','Users')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-gray-700">Users</span>@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $users->total() }} total customers</p>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name, email, phone..."
               class="input flex-1 text-sm">
        <select name="status" class="input w-36 text-sm">
            <option value="">All Status</option>
            <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>Active</option>
            <option value="banned"  {{ request('status') === 'banned'  ? 'selected' : '' }}>Banned</option>
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">Search</button>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Customer</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Phone</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Orders</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Joined</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $user->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-800">{{ $user->orders_count }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $user->is_active ? 'Active' : 'Banned' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 text-xs font-semibold hover:underline">View</a>
                                <button onclick="toggleUser(this, {{ $user->id }})"
                                        class="text-xs font-semibold {{ $user->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }}">
                                    {{ $user->is_active ? 'Ban' : 'Unban' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-10 text-gray-400">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleUser(btn, userId) {
    if(!confirm('Are you sure?')) return;
    fetch(`/admin/users/${userId}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(data => {
        location.reload();
    });
}
</script>
@endpush
