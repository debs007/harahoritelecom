<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        $user->update($data);
        return response()->json(['message' => 'Profile updated.', 'user' => [
            'id' => $user->id, 'name' => $user->name,
            'email' => $user->email, 'phone' => $user->phone,
        ]]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function addresses()
    {
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();
        return AddressResource::collection($addresses);
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'full_name'    => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'address_line1'=> 'required|string|max:255',
            'address_line2'=> 'nullable|string|max:255',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'pincode'      => 'required|string|max:10',
            'country'      => 'nullable|string|max:100',
            'is_default'   => 'nullable|boolean',
        ]);
        $data['user_id'] = auth()->id();
        $data['country'] = $data['country'] ?? 'India';

        if ($request->boolean('is_default')) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        // Auto-default if first address
        if (auth()->user()->addresses()->count() === 0) {
            $data['is_default'] = true;
        }

        $address = Address::create($data);
        return new AddressResource($address);
    }

    public function updateAddress(Request $request, Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        $data = $request->validate([
            'full_name'    => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'address_line1'=> 'required|string|max:255',
            'address_line2'=> 'nullable|string|max:255',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'pincode'      => 'required|string|max:10',
        ]);
        $address->update($data);
        return new AddressResource($address);
    }

    public function deleteAddress(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        $address->delete();
        return response()->json(['message' => 'Address deleted.']);
    }

    public function setDefault(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return response()->json(['message' => 'Default address updated.']);
    }
}
