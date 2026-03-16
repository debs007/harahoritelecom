<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'banned') {
            $query->where('is_active', false);
        }

        $users = $query->withCount('orders')->latest()->paginate(25);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['orders.items', 'addresses', 'reviews']);
        $totalSpent = $user->orders()->where('payment_status', 'paid')->sum('total');
        return view('admin.users.show', compact('user', 'totalSpent'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:customer,admin']);
        $user->update(['role' => $request->role]);
        return back()->with('success', 'User role updated.');
    }

    public function toggle(User $user)
    {
        // Prevent banning yourself
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Cannot ban yourself.'], 422);
        }
        $user->update(['is_active' => ! $user->is_active]);
        return response()->json(['active' => $user->is_active]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }
        $user->delete();
        return back()->with('success', 'User deleted.');
    }
}
