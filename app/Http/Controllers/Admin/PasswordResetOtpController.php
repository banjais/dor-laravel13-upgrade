<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordResetOtpController extends Controller
{
    private const ALLOWED_USER_TYPES = [2, 12, 13];
    private const OTP_EXPIRY_MINUTES = 5;
    private const MAX_OTP_ATTEMPTS = 5;

    public function showForgotPassword(Request $request)
    {
        if ($request->boolean('restart')) {
            $request->session()->forget('password_reset_user_id');
        }

        return view('users.forgot_password');
    }

    public function sendOtp(Request $request)
    {
        logger()->info('Password reset OTP endpoint reached.');
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:254'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = strtolower(trim($request->input('email')));

        $user = DB::table('users')
            ->whereRaw('LOWER(email_address) = ?', [$email])
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->whereIn('user_type_id', self::ALLOWED_USER_TYPES)
            ->first();

        $failureMessage = 'We could not send the OTP. Please check your registered email address or contact the administrator.';

        if (
            !$user ||
            empty($user->email_address) ||
            filter_var($user->email_address, FILTER_VALIDATE_EMAIL) === false
        ) {
            \Illuminate\Support\Facades\Log::warning(
                'Password reset OTP request rejected before mail delivery.',
                [
                    'reason' => !$user ? 'eligible_account_not_found' : 'invalid_registered_email',
                ]
            );

            return back()->with('error', $failureMessage);
        }

        // In email test mode, fail closed unless a valid test recipient is configured.
        $deliveryEmail = $user->email_address;

        if (config('general.email.is_test_mode')) {
            $testRecipient = trim((string) config('general.email.test_email_to'));

            if (filter_var($testRecipient, FILTER_VALIDATE_EMAIL) === false) {
                \Illuminate\Support\Facades\Log::warning(
                    'Password reset OTP delivery blocked because the test recipient is missing or invalid.'
                );

                return back()->with('error', $failureMessage);
            }

            $deliveryEmail = $testRecipient;
        }

        $recipientDomain = strtolower(substr(strrchr($deliveryEmail, '@') ?: '', 1));
        $mailer = null;
        $senderEmailOverride = null;

        if ($recipientDomain === 'gmail.com') {
            $mailer = 'gmail';
            $senderEmailOverride = config('mail.mailers.gmail.username');
            $gmailPassword = config('mail.mailers.gmail.password');

            if (
                empty($senderEmailOverride) ||
                empty($gmailPassword) ||
                filter_var($senderEmailOverride, FILTER_VALIDATE_EMAIL) === false
            ) {
                \Illuminate\Support\Facades\Log::warning(
                    'Password reset OTP request rejected because Gmail SMTP configuration is incomplete.'
                );

                return back()->with('error', $failureMessage);
            }
        }

        $otp = (string) random_int(100000, 999999);

        // Keep the previous OTP usable until the new OTP email is sent successfully.
        $otpId = DB::table('password_reset_otps')->insertGetId([
            'user_id' => $user->id,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'attempts' => 0,
            'used_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subject = 'Department of Roads - Password Reset OTP';

        $content = '
            <p>A password reset request was received for your Department of Roads account.</p>
            <p>Your One-Time Password (OTP) is:</p>
            <h2 style="letter-spacing:5px;">' . e($otp) . '</h2>
            <p>This OTP is valid for 5 minutes and can only be used once.</p>
            <p>If you did not request this, please ignore this email.</p>
        ';

        try {
            $sent = \Helper::sendEmail(
                $deliveryEmail,
                $subject,
                $content,
                [],
                "",
                [],
                null,
                null,
                $mailer,
                $senderEmailOverride
            );
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::warning(
                'Password reset OTP email delivery failed.',
                [
                    'mailer' => $mailer ?: config('mail.default'),
                    'recipient_domain' => $recipientDomain,
                    'exception' => get_class($exception),
                ]
            );

            $sent = false;
        }

        if (!$sent) {
            DB::table('password_reset_otps')->where('id', $otpId)->delete();

            return back()->with('error', $failureMessage);
        }

        // Only a successfully delivered new OTP replaces previous unused OTPs.
        DB::table('password_reset_otps')
            ->where('user_id', $user->id)
            ->where('id', '<>', $otpId)
            ->whereNull('used_at')
            ->update(['used_at' => now(), 'updated_at' => now()]);

        $request->session()->put('password_reset_user_id', $user->id);

        return redirect()
            ->route('admin.password.request')
            ->with('status', 'An OTP has been sent to your registered email address. Please check your inbox.');
    }

    public function showResetForm(Request $request)
    {
        return redirect()->route('admin.password.request');
    }

    public function resetPassword(Request $request)
    {
        if (!$request->session()->has('password_reset_user_id')) {
            return redirect()->route('admin.password.request');
        }

        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $userId = (int) $request->session()->get('password_reset_user_id');

        $result = DB::transaction(function () use ($userId, $request) {
            $user = DB::table('users')
                ->where('id', $userId)
                ->where('is_active', '1')
                ->whereNull('deleted_at')
                ->whereIn('user_type_id', self::ALLOWED_USER_TYPES)
                ->lockForUpdate()
                ->first();

            if (!$user) {
                return 'invalid';
            }

            $otpRecord = DB::table('password_reset_otps')
                ->where('user_id', $userId)
                ->whereNull('used_at')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (!$otpRecord) {
                return 'invalid';
            }

            if (now()->greaterThan($otpRecord->expires_at)) {
                DB::table('password_reset_otps')
                    ->where('id', $otpRecord->id)
                    ->update(['used_at' => now(), 'updated_at' => now()]);

                return 'expired';
            }

            if ($otpRecord->attempts >= self::MAX_OTP_ATTEMPTS) {
                DB::table('password_reset_otps')
                    ->where('id', $otpRecord->id)
                    ->update(['used_at' => now(), 'updated_at' => now()]);

                return 'locked';
            }

            if (!Hash::check($request->input('otp'), $otpRecord->otp_hash)) {
                $newAttempts = $otpRecord->attempts + 1;

                DB::table('password_reset_otps')
                    ->where('id', $otpRecord->id)
                    ->update([
                        'attempts' => $newAttempts,
                        'used_at' => $newAttempts >= self::MAX_OTP_ATTEMPTS ? now() : null,
                        'updated_at' => now(),
                    ]);

                return 'invalid';
            }

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'password' => Hash::make($request->input('password')),
                    'password_changed_on' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('password_reset_otps')
                ->where('user_id', $userId)
                ->whereNull('used_at')
                ->update(['used_at' => now(), 'updated_at' => now()]);

            return 'success';
        });

        if ($result === 'success') {
            $request->session()->forget('password_reset_user_id');

            return redirect()
                ->route('admin.login')
                ->with('status', 'Your password has been reset successfully. Please log in with your new password.');
        }

        $message = match ($result) {
            'expired' => 'Your OTP has expired. Please request a new OTP.',
            'locked' => 'Too many attempts. Please request a new OTP.',
            default => 'The OTP is invalid or no longer available. Please check it or request a new OTP.',
        };

        return back()->withErrors(['otp' => $message]);
    }
}
