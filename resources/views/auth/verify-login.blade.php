<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Sign In · Holiday Travelers Inc.</title>
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

            <div id="code-countdown" class="mb-5 rounded-xl border border-accent/30 bg-accent/10 px-4 py-3 text-center text-sm font-semibold text-primary" data-expires-at="{{ $expiresAt * 1000 }}">
                Code expires in <span id="countdown-time">01:00</span>
            </div>

            <form id="verification-form" method="POST" action="{{ route('login.verify') }}" class="space-y-5">
            @csrf
            <input id="verification-code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" required autofocus
                   class="w-full rounded-xl border border-border bg-background px-4 py-4 text-center text-2xl font-semibold tracking-[0.5em] text-primary outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/15">
            <button id="verify-button" type="submit" class="w-full rounded-xl bg-secondary px-4 py-3 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5 hover:opacity-90">
                Verify and Sign In
            </button>
            </form>

            <a href="{{ route('login') }}" class="mt-6 block text-center text-sm font-medium text-primary transition hover:text-secondary">Back to login</a>
        </div>
    </main>
    </div>
    <script>
        (() => {
            const countdown = document.getElementById('code-countdown');
            const time = document.getElementById('countdown-time');
            const input = document.getElementById('verification-code');
            const button = document.getElementById('verify-button');
            const expiresAt = Number(countdown.dataset.expiresAt);

            const update = () => {
                const remaining = Math.max(0, expiresAt - Date.now());
                const seconds = Math.ceil(remaining / 1000);
                time.textContent = `${String(Math.floor(seconds / 60)).padStart(2, '0')}:${String(seconds % 60).padStart(2, '0')}`;

                if (remaining <= 0) {
                    countdown.classList.remove('border-accent/30', 'bg-accent/10');
                    countdown.classList.add('border-error/30', 'bg-error/10', 'text-error');
                    time.textContent = 'Expired';
                    input.disabled = true;
                    button.disabled = true;
                    button.classList.add('cursor-not-allowed', 'opacity-50');
                    clearInterval(timer);
                }
            };

            let timer;
            update();
            timer = setInterval(update, 1000);
        })();
    </script>
</body>
</html>
