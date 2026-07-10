<?php

namespace App\Service\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Twilio\Rest\Client as TwilioClient;

class ForgotPasswordService
{
    /**
     * STEP 1: Check phone + send OTP via SMS
     */
    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $this->normalizePhone($request->phone);

        $throttleKey = "forgot_otp_throttle_{$phone}";
        if (Cache::has($throttleKey)) {
            return response()->json([
                'message' => 'Please wait before requesting another code.',
            ], 429);
        }

        $user = User::where('phone', $request->phone)->first();

        // Always respond the same way whether or not the phone exists,
        // so we don't leak which numbers are registered.
        if ($user) {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            Cache::put("forgot_otp_{$phone}", $otp, now()->addMinutes(10));
            Cache::put("forgot_otp_attempts_{$phone}", 0, now()->addMinutes(10));
            Cache::put($throttleKey, true, now()->addSeconds(60));

            $this->sendOtpSms($phone, $otp);
        }

        return response()->json([
            'message' => 'If that phone number is registered, a code has been sent.',
        ]);
    }

    /**
     * Send OTP via Twilio SMS. Expects an already-normalized E.164 number.
     */
    protected function sendOtpSms(string $phone, string $otp): void
    {
        try {
            $client = new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.auth_token')
            );

            $client->messages->create($phone, [
                'from' => config('services.twilio.from'),
                'body' => "Your password reset code is: {$otp} (valid 10 minutes)",
            ]);
        } catch (\Exception $e) {
            // Don't leak delivery failures to the client response (avoids
            // confirming/denying account details); log for ops visibility.
            Log::error('Twilio OTP SMS failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Normalize a stored phone number into E.164 for Twilio.
     * Adjust the default country code to match your user base.
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^\d+]/', '', $phone); // strip spaces/dashes

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // Example: Cambodia numbers stored as 0XXXXXXXX -> +855XXXXXXXX
        // Replace this rule with whatever matches your actual user data.
        if (str_starts_with($phone, '0')) {
            return '+855' . substr($phone, 1);
        }

        return '+' . $phone;
    }

    /**
     * STEP 2: Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|string|size:6',
        ]);

        $phone = $this->normalizePhone($request->phone);

        $attemptsKey = "forgot_otp_attempts_{$phone}";
        $attempts = Cache::get($attemptsKey, 0);

        if ($attempts >= 5) {
            Cache::forget("forgot_otp_{$phone}");
            return response()->json([
                'message' => 'Too many attempts. Please request a new code.',
            ], 429);
        }

        $cachedOtp = Cache::get("forgot_otp_{$phone}");

        if (! $cachedOtp || ! hash_equals($cachedOtp, $request->otp)) {
            Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(10));
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        Cache::forget("forgot_otp_{$phone}");
        Cache::forget($attemptsKey);

        $resetToken = Str::random(64);

        Cache::put("forgot_reset_{$resetToken}", $request->phone, now()->addMinutes(15));

        return response()->json([
            'reset_token' => $resetToken,
        ]);
    }

    /**
     * STEP 3: Reset password
     */
    public function resetPasswordByPhone(Request $request)
    {
        $request->validate([
            'phone'       => 'required|string',
            'reset_token' => 'required|string',
            'password'    => ['required', 'string', Password::min(8)],
        ]);

        $phone = Cache::get("forgot_reset_{$request->reset_token}");

        if (! $phone || $phone !== $request->phone) {
            return response()->json(['message' => 'Reset token expired or invalid'], 422);
        }

        $user = User::where('phone', $phone)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Cache::forget("forgot_reset_{$request->reset_token}");

        return response()->json([
            'message' => 'Password reset successfully',
        ]);
    }
}