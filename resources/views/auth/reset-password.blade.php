<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password · Holiday Travelers Inc.</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-[#0b1f3a] font-body">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(111,169,230,0.35),_transparent_31rem)]"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.035)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.035)_1px,transparent_1px)] bg-[size:42px_42px]"></div>
        <main class="relative w-full max-w-md overflow-hidden rounded-[2rem] border border-white/70 bg-card/95 shadow-2xl shadow-primary/15 backdrop-blur-sm">
            <div class="bg-gradient-to-br from-primary via-[#12345f] to-[#0d2749] px-7 py-8 text-white sm:px-10">
                <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-white p-1 shadow-lg shadow-black/10">
                    <img src="{{ asset('images/logo-transparent.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
                </div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Account recovery</p>
                <h1 class="mt-2 font-heading text-2xl font-semibold">Create a new password</h1>
                <p class="mt-2 text-sm leading-6 text-white/65">Choose a secure password for your back-office account.</p>
            </div>
            <div class="p-7 sm:p-10">
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-error/30 bg-error/10 px-4 py-3 text-sm text-error">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-primary">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email"
                               class="w-full rounded-xl border border-border bg-background px-4 py-3.5 text-sm text-primary outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/15">
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-primary">New password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" minlength="8"
                               class="w-full rounded-xl border border-border bg-background px-4 py-3.5 text-sm text-primary outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/15">
                        <p class="mt-2 text-xs text-gray-500">Use at least 8 characters.</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-primary">Confirm new password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full rounded-xl border border-border bg-background px-4 py-3.5 text-sm text-primary outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/15">
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-secondary px-4 py-3.5 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5 hover:opacity-90 focus:outline-none focus-visible:ring-4 focus-visible:ring-secondary/30">Update password</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
