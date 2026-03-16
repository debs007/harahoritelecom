<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user   = auth()->user()->load('orders');
        $orders = $user->orders()->latest()->take(5)->get();
        return view('frontend.profile.index', compact('user', 'orders'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:15',
            'email'  => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'avatar' => 'nullable|image|max:1024',
        ]);

        $data = $request->only('name', 'phone', 'email');

        if ($request->hasFile('avatar')) {
            $path         = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }

    // ── Addresses ──────────────────────────────────────────

    public function addresses()
    {
        $addresses = auth()->user()->addresses()->latest()->get();
        return view('frontend.profile.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'full_name'     => 'required|string|max:255',
            'phone'         => 'required|string|max:15',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'required|string|max:100',
            'pincode'       => 'required|string|max:10',
            'country'       => 'required|string|max:100',
            'is_default'    => 'boolean',
        ]);

        $data['user_id'] = auth()->id();

        if (! empty($data['is_default'])) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        // If this is the first address, make it default
        if (auth()->user()->addresses()->count() === 0) {
            $data['is_default'] = true;
        }

        Address::create($data);

        return back()->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'full_name'     => 'required|string|max:255',
            'phone'         => 'required|string|max:15',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'required|string|max:100',
            'pincode'       => 'required|string|max:10',
            'country'       => 'required|string|max:100',
        ]);

        $address->update($data);

        return back()->with('success', 'Address updated successfully.');
    }

    public function deleteAddress(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        $address->delete();
        return back()->with('success', 'Address deleted.');
    }

    public function setDefaultAddress(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);

        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated.');
    }
}
