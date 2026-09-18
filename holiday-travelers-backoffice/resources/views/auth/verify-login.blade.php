<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Sign In · Holiday Travelers Inc.</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#0b1f3a] px-4 py-8 font-body">
    <main class="w-full max-w-md overflow-hidden rounded-[2rem] border border-border bg-card shadow-2xl shadow-primary/10">
        <div class="bg-gradient-to-br from-primary via-[#12345f] to-[#0d2749] px-7 py-8 text-white sm:px-10">
            <div class="mb-6 flex h-12 w-12 items-center justify-center bg-white p-1 shadow-lg shadow-black/10">
                <img src="{{ asset('images/logo.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
            </div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Secure sign in</p>
            <h1 class="mt-2 font-heading text-2xl font-semibold">Check your email</h1>
            <p class="mt-2 text-sm leading-6 text-white/65">Enter the 6-digit code we sent to your email to finish signing in.</p>
        </div>
        <div class="p-7 sm:p-10">
            @if ($errors->any())
            <div class="mb-5 rounded-xl border border-error/30 bg-error/10 px-4 py-3 text-sm text-error">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.verify') }}" class="space-y-5">
            @csrf
            <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" required autofocus
                   class="w-full rounded-xl border border-border bg-background px-4 py-4 text-center text-2xl font-semibold tracking-[0.5em] text-primary outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/15">
            <button type="submit" class="w-full rounded-xl bg-secondary px-4 py-3 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5 hover:opacity-90">
                Verify and Sign In
            </button>
            </form>

            <a href="{{ route('login') }}" class="mt-6 block text-center text-sm font-medium text-primary transition hover:text-secondary">Back to login</a>
        </div>
    </main>
</body>
</html>
