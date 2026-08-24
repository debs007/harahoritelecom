<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'role'     => 'customer',
        ]);
        $token = $user->createToken('mobile-app')->plainTextToken;
        return response()->json([
            'message' => 'Registration successful!',
            'token'   => $token,
            'user'    => $this->userArray($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }
        if (!$user->is_active) {
            return response()->json(['message' => 'Your account has been suspended.'], 403);
        }
        $user->tokens()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;
        return response()->json([
            'message' => 'Login successful!',
            'token'   => $token,
            'user'    => $this->userArray($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $this->userArray($request->user())]);
    }

    // ── Step 1: Send OTP ─────────────────────────────────────────────────────
    public function forgotSendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // Don't reveal if email exists
            return response()->json(['message' => 'OTP sent if account exists.']);
        }
        $otp = rand(100000, 999999);
        Cache::put('pwd_otp_' . $user->id, $otp, now()->addMinutes(10));
        // TODO: Send OTP via SMS/email in production
        // Example: Mail::to($user->email)->send(new OtpMail($otp));
        \Illuminate\Support\Facades\Log::info("Password reset OTP for {$user->email}: {$otp}");
        return response()->json([
            'message' => 'OTP sent.',
            'user_id' => $user->id,
        ]);
    }

    // ── Step 2: Verify OTP ───────────────────────────────────────────────────
    public function forgotVerifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'otp'     => 'required|string',
        ]);
        $cached = Cache::get('pwd_otp_' . $request->user_id);
        if (!$cached || (string)$cached !== (string)$request->otp) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }
        $token = Str::random(40);
        Cache::put('pwd_reset_' . $request->user_id, $token, now()->addMinutes(15));
        Cache::forget('pwd_otp_' . $request->user_id);
        return response()->json([
            'message'     => 'OTP verified.',
            'reset_token' => $token,
            'user_id'     => $request->user_id,
        ]);
    }

    // ── Step 3: Reset Password ───────────────────────────────────────────────
    public function forgotReset(Request $request)
    {
        $request->validate([
            'user_id'              => 'required|integer',
            'reset_token'          => 'required|string',
            'password'             => 'required|string|min:8|confirmed',
        ]);
        $cached = Cache::get('pwd_reset_' . $request->user_id);
        if (!$cached || $cached !== $request->reset_token) {
            return response()->json(['message' => 'Invalid or expired reset token.'], 422);
        }
        $user = User::findOrFail($request->user_id);
        $user->update(['password' => Hash::make($request->password)]);
        Cache::forget('pwd_reset_' . $request->user_id);
        return response()->json(['message' => 'Password reset successfully.']);
    }

    // ── Helper ───────────────────────────────────────────────────────────────
    private function userArray(User $user): array
    {
        return [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'role'           => $user->role,
            'avatar'         => null,
            'loyalty_points' => (int) ($user->loyalty_points ?? 0),
        ];
    }
}