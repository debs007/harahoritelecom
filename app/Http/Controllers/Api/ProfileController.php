<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        // Phone is set once at registration and is intentionally not editable
        // here — it's used as a stable contact/identity field. Ignored even
        // if a caller sends one, rather than validated and rejected, so the
        // rest of a profile edit still goes through.
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

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => $this->saveImage($request->file('avatar'), 'avatars', 400, 400)]);

        return response()->json([
            'message' => 'Profile photo updated.',
            'avatar'  => url('storage/' . $user->avatar),
        ]);
    }

    /**
     * Delete (anonymise) the authenticated user's account.
     *
     * Data with no legal retention requirement — profile, addresses, cart,
     * wishlist, loyalty balance — is removed. Orders, invoices and warranty/
     * exchange records are kept (as described in the Privacy Policy / Delete
     * Account page: invoices for 8 years per Indian tax law, warranty tied
     * to device IMEI, dispute & fraud-prevention records) but are no longer
     * linked to any identifiable profile, and the account can no longer log
     * in. The row itself is kept rather than hard-deleted so those retained
     * records keep a valid reference.
     */
    public function destroy(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = auth()->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Incorrect password.'], 422);
        }

        $hasActiveOrders = $user->orders()
            ->whereNotIn('status', ['delivered', 'cancelled', 'refunded'])
            ->exists();

        if ($hasActiveOrders) {
            return response()->json([
                'message' => 'You have an order in progress. Please wait until it is delivered, cancelled, or refunded before deleting your account.',
            ], 422);
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->addresses()->delete();
        $user->cart()->delete();
        $user->wishlists()->delete();
        $user->tokens()->delete();

        $user->update([
            'name'           => 'Deleted User',
            'email'          => 'deleted_' . $user->id . '_' . time() . '@deleted.harahoritelecom.local',
            'phone'          => null,
            'avatar'         => null,
            'password'       => Hash::make(Str::random(40)),
            'is_active'      => false,
            'loyalty_points' => 0,
            'city'           => null,
            'state'          => null,
            'pincode'        => null,
            'crm_segment'    => null,
        ]);

        return response()->json(['message' => 'Account deleted.']);
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

    private function saveImage($file, string $folder, int $w, int $h): string
    {
        $path = "{$folder}/" . uniqid() . '_' . time() . '.webp';

        try {
            $img = \Intervention\Image\Facades\Image::make($file->getRealPath())
                ->fit($w, $h)
                ->encode('webp', 85);

            Storage::disk('public')->put($path, $img);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Avatar save failed: ' . $e->getMessage());
            $ext  = $file->getClientOriginalExtension() ?: 'jpg';
            $path = "{$folder}/" . uniqid() . '_' . time() . '.' . $ext;
            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
        }

        return $path;
    }
}
