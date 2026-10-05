<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password · Holiday Travelers Inc.</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-[#0b1f3a] font-body">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(111,169,230,0.35),_transparent_31rem)]"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.035)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.035)_1px,transparent_1px)] bg-[size:42px_42px]"></div>
        <div class="absolute -right-24 top-16 h-72 w-72 rounded-full bg-accent/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-20 h-80 w-80 rounded-full bg-[#174b83] blur-3xl"></div>

        <main class="relative w-full max-w-md overflow-hidden rounded-[2rem] border border-white/70 bg-card/95 shadow-2xl shadow-primary/15 backdrop-blur-sm">
            <div class="bg-gradient-to-br from-primary via-[#12345f] to-[#0d2749] px-7 py-8 text-white sm:px-10">
                <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-white p-1 shadow-lg shadow-black/10">
                    <img src="{{ asset('images/logo-transparent.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
                </div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Account recovery</p>
                <h1 class="mt-2 font-heading text-2xl font-semibold">Forgot your password?</h1>
                <p class="mt-2 text-sm leading-6 text-white/65">Enter your staff email and we’ll send you a verification code.</p>
            </div>

            <div class="p-7 sm:p-10">
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-error/30 bg-error/10 px-4 py-3 text-sm text-error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-primary">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="name@company.com"
                               class="w-full rounded-xl border border-border bg-background px-4 py-3.5 text-sm text-primary outline-none transition placeholder:text-gray-400 focus:border-accent focus:bg-white focus:ring-4 focus:ring-accent/15">
                    </div>
                    <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-secondary to-[#ef8d39] px-4 py-3.5 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/25 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-secondary/30 focus:outline-none focus-visible:ring-4 focus-visible:ring-secondary/30">
                        <span>Send verification code</span>
                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </button>
                </form>

                <a href="{{ route('login') }}" class="mt-6 block text-center text-sm font-medium text-primary transition hover:text-secondary">Back to login</a>
            </div>
        </main>
    </div>
</body>
</html>
