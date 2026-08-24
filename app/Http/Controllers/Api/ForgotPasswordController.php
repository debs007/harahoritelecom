<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // ── Step 1: Send OTP to email ─────────────────────────────────────────────
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Always return success even if email not found (security: don't leak)
        if (!$user) {
            return response()->json([
                'message' => 'If this email is registered, you will receive an OTP shortly.',
            ]);
        }

        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store in password_reset_tokens table (Laravel's built-in table)
        // We store the OTP as the token, hashed
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($otp),
            'created_at' => now(),
        ]);

        // Send OTP via email
        Mail::send([], [], function ($message) use ($request, $otp, $user) {
            $message->to($request->email)
                ->subject('Your Harahori Telecom Password Reset OTP')
                ->html("
                    <div style='font-family: Arial, sans-serif; max-width: 480px; margin: 0 auto;'>
                        <h2 style='color: #7C3AED;'>Harahori Telecom</h2>
                        <p>Hi {$user->name},</p>
                        <p>Your OTP to reset your password is:</p>
                        <div style='font-size: 36px; font-weight: bold; letter-spacing: 10px;
                                    color: #7C3AED; text-align: center; padding: 20px;
                                    background: #EDE9FE; border-radius: 12px; margin: 20px 0;'>
                            {$otp}
                        </div>
                        <p style='color: #6B7280; font-size: 14px;'>
                            This OTP is valid for <strong>10 minutes</strong>.<br>
                            If you did not request a password reset, please ignore this email.
                        </p>
                    </div>
                ");
        });

        return response()->json([
            'message' => 'If this email is registered, you will receive an OTP shortly.',
        ]);
    }

    // ── Step 2: Verify OTP ────────────────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        // Check expiry (10 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'OTP has expired. Please request a new one.'], 422);
        }

        if (!Hash::check($request->otp, $record->token)) {
            return response()->json(['message' => 'Incorrect OTP. Please try again.'], 422);
        }

        // OTP is valid — issue a short-lived reset token the client will send in step 3
        $resetToken = Str::random(64);
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update(['token' => Hash::make($resetToken)]);

        return response()->json([
            'message'      => 'OTP verified successfully.',
            'reset_token'  => $resetToken,
        ]);
    }

    // ── Step 3: Reset password using reset_token from step 2 ─────────────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'reset_token'           => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid reset session. Please start over.'], 422);
        }

        // Token is valid for 15 minutes from creation
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'Reset session expired. Please start over.'], 422);
        }

        if (!Hash::check($request->reset_token, $record->token)) {
            return response()->json(['message' => 'Invalid reset token. Please start over.'], 422);
        }

        // Update password
        $user = User::where('email', $request->email)->firstOrFail();
        $user->update(['password' => Hash::make($request->password)]);

        // Clean up token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Revoke all existing tokens so old sessions are invalidated
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Password reset successfully. Please log in with your new password.',
        ]);
    }
}
