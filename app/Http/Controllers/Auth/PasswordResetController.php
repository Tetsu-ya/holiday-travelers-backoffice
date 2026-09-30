<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

class PasswordResetController extends Controller
{
    /**
     * How long the emailed 6-digit code stays valid, in minutes.
     */
    private const CODE_TTL_MINUTES = 1;

    /**
     * Session key holding the pending email verification for a reset request.
     */
    private const CODE_SESSION_KEY = 'password_reset.code';

    /**
     * Password rules used both when validating and when resetting.
     *
     * @return array<int, mixed>
     */
    private function passwordRules(): array
    {
        return ['required', 'string', 'min:8', 'confirmed'];
    }

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 1: verify the account exists, then email a 6-digit code.
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->string('email')->toString()));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            $request->session()->forget(self::CODE_SESSION_KEY);

            return back()->withErrors([
                'email' => 'We could not find an account with that email address.',
            ])->onlyInput('email');
        }

        try {
            $code = $this->sendCodeTo($user);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'email' => $this->mailFailureMessage(),
            ])->onlyInput('email');
        }

        $this->storeVerification($request, $user, $code, $request->boolean('remember'));

        return redirect()->route('password.verify');
    }

    public function showVerifyForm(Request $request)
    {
        if (! $this->sessionVerification($request)) {
            return redirect()->route('password.request');
        }

        $verification = $this->sessionVerification($request);

        return view('auth.verify-reset-code', [
            'email' => $verification['email'] ?? User::find($verification['user_id'])?->email,
            'deliveryEmail' => $verification['delivery_email'] ?? config('mail.code_recipient'),
            'expiresAt' => (int) $verification['expires_at'],
        ]);
    }

    /**
     * Re-send a fresh code for the address currently pending verification.
     */
    public function resendCode(Request $request)
    {
        $verification = $this->sessionVerification($request);

        if (! $verification) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Your reset request expired. Please enter your email again.',
            ]);
        }

        $user = User::find($verification['user_id']);

        if (! $user) {
            $request->session()->forget(self::CODE_SESSION_KEY);

            return redirect()->route('password.request')->withErrors([
                'email' => 'Your reset request expired. Please enter your email again.',
            ]);
        }

        try {
            $code = $this->sendCodeTo($user);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'code' => $this->mailFailureMessage(),
            ]);
        }

        $this->storeVerification($request, $user, $code, $verification['remember']);

        return redirect()->route('password.verify')->with('status', 'We sent a new code to your email.');
    }

    /**
     * Step 2: confirm the emailed code, then hand the browser a signed reset link.
     */
    public function verifyCode(Request $request)
    {
        $verification = $this->sessionVerification($request);

        if (! $verification) {
            return redirect()->route('password.request')->withErrors([
                'code' => 'That code expired. Please request a new one.',
            ]);
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (! Hash::check($request->string('code')->toString(), $verification['code'])) {
            return back()->withErrors(['code' => 'That verification code is incorrect.']);
        }

        $user = User::find($verification['user_id']);

        if (! $user) {
            $request->session()->forget(self::CODE_SESSION_KEY);

            return redirect()->route('password.request')->withErrors([
                'email' => 'Your reset request expired. Please enter your email again.',
            ]);
        }

        // The one-time code already proves ownership, so we can issue a standard
        // broker token directly instead of routing the plaintext through the user.
        $broker = $this->broker();
        $broker->deleteToken($user);
        $token = $broker->createToken($user);

        $request->session()->forget(self::CODE_SESSION_KEY);

        $resetUrl = route('password.reset', ['token' => $token]).'?email='.urlencode($user->email);

        return redirect()->to($resetUrl)->with('status', 'Code confirmed. Choose a new password.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * Step 3: finally set the new password and revoke the used token.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => $this->passwordRules(),
        ]);

        $status = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        return redirect()->route('login')->with('status', __($status));
    }

    /**
     * @return array{user_id: int, remember: bool, code: string, expires_at: int}|null
     */
    private function sessionVerification(Request $request): ?array
    {
        $verification = $request->session()->get(self::CODE_SESSION_KEY);

        if (! $verification || now()->timestamp > ($verification['expires_at'] ?? 0)) {
            $request->session()->forget(self::CODE_SESSION_KEY);

            return null;
        }

        return $verification;
    }

    private function storeVerification(Request $request, User $user, string $code, bool $remember): void
    {
        $request->session()->put(self::CODE_SESSION_KEY, [
            'user_id' => $user->id,
            'email' => $user->email,
            'delivery_email' => config('mail.code_recipient') ?: $user->email,
            'remember' => $remember,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES)->timestamp,
        ]);
    }

    private function sendCodeTo(User $user): string
    {
        $code = (string) random_int(100000, 999999);
        $recipient = config('mail.code_recipient') ?: $user->email;

        Mail::mailer()->raw(
            "Your Holiday Travelers password reset code is: {$code}\n\nThis code expires in ".self::CODE_TTL_MINUTES.' minutes. If you did not request a password reset, you can ignore this email.',
            function ($message) use ($recipient) {
                $message->to($recipient)->subject('Your Holiday Travelers password reset code');
            },
        );

        return $code;
    }

    private function mailFailureMessage(): string
    {
        return 'We could not send the reset email. Please check the Gmail SMTP App Password.';
    }

    private function broker(): PasswordBroker
    {
        return Password::broker();
    }
}
