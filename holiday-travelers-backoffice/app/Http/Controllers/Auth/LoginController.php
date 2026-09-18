<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::validate($credentials)) {
            $user = User::where('email', $credentials['email'])->firstOrFail();
            $code = (string) random_int(100000, 999999);

            $request->session()->put([
                'login_verification.user_id' => $user->id,
                'login_verification.remember' => $request->boolean('remember'),
                'login_verification.code' => Hash::make($code),
                'login_verification.expires_at' => now()->addMinutes(10)->timestamp,
            ]);

            try {
                Mail::raw("Your Holiday Travelers verification code is: {$code}\n\nThis code expires in 10 minutes.", function ($message) use ($user) {
                    $message->to($user->email)->subject('Your Holiday Travelers verification code');
                });
            } catch (Throwable $exception) {
                report($exception);
                $request->session()->forget('login_verification');

                return back()->withErrors([
                    'email' => 'We could not send the verification email. Please check the Gmail SMTP App Password.',
                ])->onlyInput('email');
            }

            return redirect()->route('login.verify');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showVerificationForm(Request $request)
    {
        if (! $request->session()->has('login_verification.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.verify-login');
    }

    public function verifyLogin(Request $request)
    {
        $verification = $request->session()->get('login_verification');

        if (! $verification || now()->timestamp > $verification['expires_at']) {
            $request->session()->forget('login_verification');

            return redirect()->route('login')->withErrors([
                'email' => 'That verification code expired. Please sign in again.',
            ]);
        }

        $request->validate(['code' => ['required', 'digits:6']]);

        if (! Hash::check($request->string('code')->toString(), $verification['code'])) {
            return back()->withErrors(['code' => 'That verification code is incorrect.']);
        }

        $user = User::find($verification['user_id']);
        $request->session()->forget('login_verification');

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'email' => 'Your account could not be found. Please sign in again.',
            ]);
        }

        Auth::login($user, $verification['remember']);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->withErrors([
                'email' => 'Unable to sign in with Google. Please try again.',
            ]);
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google did not provide an email address for this account.',
            ]);
        }

        $allowedDomains = config('services.google.allowed_domains', []);
        $allowedEmails = config('services.google.allowed_emails', []);
        $domain = Str::after($email, '@');

        if (($allowedDomains || $allowedEmails)
            && ! in_array($domain, $allowedDomains, true)
            && ! in_array($email, $allowedEmails, true)) {
            return redirect()->route('login')->withErrors([
                'email' => 'This Google account is not authorized to access the back office.',
            ]);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
            ],
        );

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}