@extends('layouts.app')
@section('title', 'Marketing Hub')

@section('content')
<div class="marketing-hub space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary">Marketing &amp; Promotions</p>
            <h2 class="mt-1 font-heading text-3xl font-semibold tracking-tight text-primary">Marketing Hub</h2>
            <p class="mt-2 max-w-2xl text-sm text-gray-500">Create promotional brochures, manage social ads, and connect marketing to your tour packages.</p>
        </div>
        <a href="{{ route('packages.index') }}" class="inline-flex items-center justify-center rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-primary shadow-sm transition hover:border-secondary hover:text-secondary">
            Package management
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-success/20 bg-success/10 px-4 py-3 text-sm font-medium text-success" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-error/20 bg-error/10 px-4 py-3 text-sm text-error" role="alert">
            <p class="font-semibold">Please review the following:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-border border-t-4 border-t-primary bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Social campaigns</p>
            <p class="mt-2 font-heading text-3xl font-semibold text-primary">{{ number_format($campaigns->total()) }}</p>
            <p class="mt-1 text-xs text-gray-400">Facebook, Instagram &amp; TikTok</p>
        </article>
        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Leads generated</p>
            <p class="mt-2 font-heading text-3xl font-semibold text-primary">{{ number_format($analytics['leads']) }}</p>
            <p class="mt-1 text-xs text-gray-400">Across social campaigns</p>
        </article>
        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Conversions</p>
            <p class="mt-2 font-heading text-3xl font-semibold text-primary">{{ number_format($analytics['conversions']) }}</p>
            <p class="mt-1 text-xs text-gray-400">From campaign leads</p>
        </article>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="border-b border-border px-4 py-3 sm:px-5">
            <div class="flex gap-2 overflow-x-auto pb-1" role="tablist" aria-label="Marketing hub sections">
                @foreach (['overview' => 'Overview', 'brochures' => 'Promotional brochures', 'social-ads' => 'Social ads', 'packages' => 'Tour packages'] as $key => $label)
                    <button type="button" role="tab" aria-selected="{{ $key === 'overview' ? 'true' : 'false' }}" data-tab="{{ $key }}" class="hub-tab shrink-0 rounded-lg px-3.5 py-2.5 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary {{ $key === 'overview' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-background hover:text-primary' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <section class="hub-panel space-y-5" data-panel="overview" role="tabpanel">
                <div>
                    <h3 class="font-heading text-xl font-semibold text-primary">Your marketing workspace</h3>
                    <p class="mt-1 text-sm text-gray-500">Choose an area to manage. Campaign and package changes stay connected to the existing back-office records.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <button type="button" data-open-tab="brochures" class="group rounded-2xl border border-border bg-card p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary/10 font-semibold text-secondary" aria-hidden="true">PDF</span>
                        <span class="mt-4 block font-semibold text-primary">Promotional brochures</span>
                        <span class="mt-1 block text-sm text-gray-500">Upload, download, and remove brochure files.</span>
                        <span class="mt-4 flex items-center justify-between border-t border-border pt-3 text-xs font-semibold text-secondary"><span>{{ $brochures->total() }} files</span><span class="transition-transform group-hover:translate-x-1">Open section &rarr;</span></span>
                    </button>
                    <button type="button" data-open-tab="social-ads" class="group rounded-2xl border border-border bg-card p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 font-semibold text-primary" aria-hidden="true">AD</span>
                        <span class="mt-4 block font-semibold text-primary">Social ads</span>
                        <span class="mt-1 block text-sm text-gray-500">Create and track campaigns on Facebook, Instagram, and TikTok.</span>
                        <span class="mt-4 flex items-center justify-between border-t border-border pt-3 text-xs font-semibold text-secondary"><span>{{ $campaigns->total() }} campaigns</span><span class="transition-transform group-hover:translate-x-1">Open section &rarr;</span></span>
                    </button>
                    <button type="button" data-open-tab="packages" class="group rounded-2xl border border-border bg-card p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-success/10 font-semibold text-success" aria-hidden="true">TR</span>
                        <span class="mt-4 block font-semibold text-primary">Tour packages</span>
                        <span class="mt-1 block text-sm text-gray-500">Promote your available destinations and manage package details.</span>
                        <span class="mt-4 flex items-center justify-between border-t border-border pt-3 text-xs font-semibold text-secondary"><span>{{ $packages->count() }} featured packages</span><span class="transition-transform group-hover:translate-x-1">Open section &rarr;</span></span>
                    </button>
                </div>
            </section>

            <section class="hub-panel hidden space-y-5" data-panel="brochures" role="tabpanel">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="font-heading text-xl font-semibold text-primary">Promotional brochures</h3>
                        <p class="mt-1 text-sm text-gray-500">Upload PDF brochures or image assets. Files are stored privately and downloaded through your authenticated account.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('marketing-brochures.store') }}" enctype="multipart/form-data" class="grid gap-4 rounded-2xl border border-border bg-card p-4 shadow-sm sm:grid-cols-2 sm:p-5">
                    @csrf
                    <div>
                        <label for="brochure-title" class="mb-1.5 block text-sm font-medium text-primary">Brochure title</label>
                        <input id="brochure-title" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full rounded-lg border border-border bg-card px-3 py-2.5 text-sm focus:border-secondary focus:outline-none focus:ring-2 focus:ring-secondary/20" placeholder="Summer island escapes">
                    </div>
                    <div>
                        <label for="brochure-file" class="mb-1.5 block text-sm font-medium text-primary">Photo or brochure <span class="font-normal text-gray-500">(PNG, JPG, JPEG, or PDF; up to 10 MB)</span></label>
                        <input id="brochure-file" type="file" name="brochure" accept=".pdf,.jpg,.jpeg,.png" required class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:font-medium file:text-primary">
                        @error('brochure')
                            <p class="mt-1.5 text-sm font-medium text-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="brochure-description" class="mb-1.5 block text-sm font-medium text-primary">Description <span class="font-normal text-gray-400">(optional)</span></label>
                        <textarea id="brochure-description" name="description" rows="2" maxlength="2000" class="w-full rounded-lg border border-border bg-card px-3 py-2.5 text-sm focus:border-secondary focus:outline-none focus:ring-2 focus:ring-secondary/20" placeholder="What is this brochure promoting?">{{ old('description') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2">Upload brochure</button>
                    </div>
                </form>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($brochures as $brochure)
                        <article class="flex flex-col rounded-2xl border border-border bg-card p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-xs font-bold uppercase text-secondary">{{ $brochure->mime_type === 'application/pdf' ? 'PDF' : 'IMG' }}</span>
                                    <div class="min-w-0">
                                        <h4 class="truncate font-semibold text-primary">{{ $brochure->title }}</h4>
                                        <p class="mt-0.5 truncate text-xs text-gray-500">{{ $brochure->file_name }}</p>
                                    </div>
                                </div>
                            </div>
                            @if ($brochure->description)
                                <p class="mt-3 line-clamp-2 text-sm text-gray-600">{{ $brochure->description }}</p>
                            @endif
                            <div class="mt-auto flex items-center justify-between gap-3 pt-4">
                                <span class="text-xs text-gray-400">{{ number_format($brochure->file_size / 1048576, 2) }} MB · {{ $brochure->created_at->format('d M Y') }}</span>
                                <div class="flex shrink-0 gap-3">
                                    <a href="{{ route('marketing-brochures.download', $brochure) }}" class="text-sm font-semibold text-secondary hover:text-primary">Download</a>
                                    <form method="POST" action="{{ route('marketing-brochures.destroy', $brochure) }}" onsubmit="return confirm('Delete this brochure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-error hover:underline">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-border bg-background p-8 text-center sm:col-span-2 xl:col-span-3">
                            <p class="font-medium text-primary">No brochures uploaded yet</p>
                            <p class="mt-1 text-sm text-gray-500">Add a PDF or image above to start your promotional library.</p>
                        </div>
                    @endforelse
                </div>
                <div>{{ $brochures->links() }}</div>
            </section>

            <section class="hub-panel hidden space-y-5" data-panel="social-ads" role="tabpanel">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="font-heading text-xl font-semibold text-primary">Social advertising campaigns</h3>
                        <p class="mt-1 text-sm text-gray-500">Track leads and conversions by platform. Ad delivery is managed on each social platform.</p>
                    </div>
                    <a href="{{ route('campaigns.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-secondary">Create social ad</a>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($analytics['byChannel'] as $channel)
                        <article class="rounded-2xl border border-border bg-card p-4 shadow-sm">
                            <p class="text-sm font-semibold capitalize text-primary">{{ $channel->channel === 'social_media' ? 'Legacy social media' : $channel->channel }}</p>
                            <div class="mt-3 text-sm">
                                <div><p class="text-xs text-gray-500">Conversions</p><p class="mt-1 font-semibold text-primary">{{ number_format($channel->conversions) }}</p></div>
                            </div>
                        </article>
                    @endforeach
                    @if ($analytics['byChannel']->isEmpty())
                        <div class="rounded-2xl border border-dashed border-border p-6 text-sm text-gray-500 sm:col-span-3">No social ad results yet. Create a campaign to begin tracking performance.</div>
                    @endif
                </div>

                <div class="overflow-x-auto rounded-2xl border border-border bg-card">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-background text-xs uppercase tracking-wide text-gray-500">
                            <tr><th class="px-4 py-3">Campaign</th><th class="px-4 py-3">Platform</th><th class="px-4 py-3">Brochure</th><th class="px-4 py-3">Dates</th><th class="px-4 py-3">Leads / Conversions</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Manage</th></tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($campaigns as $campaign)
                                <tr class="hover:bg-background">
                                    <td class="px-4 py-3 font-medium text-primary">{{ $campaign->name }}</td>
                                    <td class="px-4 py-3 capitalize text-gray-600">{{ $campaign->channel === 'social_media' ? 'Social media' : $campaign->channel }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @if ($campaign->brochure)
                                            <div class="flex items-center gap-3">
                                                @if (in_array($campaign->brochure->mime_type, ['image/jpeg', 'image/png'], true))
                                                    <a href="{{ route('marketing-brochures.preview', $campaign->brochure) }}" target="_blank" rel="noopener noreferrer" aria-label="Preview {{ $campaign->brochure->title }}">
                                                        <img src="{{ route('marketing-brochures.preview', $campaign->brochure) }}" alt="{{ $campaign->brochure->title }}" loading="lazy" class="h-12 w-12 rounded-lg border border-border object-cover">
                                                    </a>
                                                @else
                                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-xs font-bold text-primary">PDF</span>
                                                @endif
                                                <div class="min-w-0">
                                                    <p class="truncate font-medium text-primary">{{ $campaign->brochure->title }}</p>
                                                    <a href="{{ route('marketing-brochures.download', $campaign->brochure) }}" class="text-xs font-medium text-secondary hover:underline">{{ $campaign->brochure->mime_type === 'application/pdf' ? 'Download PDF' : 'Download image' }}</a>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $campaign->start_date->format('d M Y') }} – {{ $campaign->end_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ number_format($campaign->leads_generated) }} / {{ number_format($campaign->conversions) }}</td>
                                    <td class="px-4 py-3"><span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold capitalize text-primary">{{ $campaign->status }}</span></td>
                                    <td class="whitespace-nowrap px-4 py-3"><a href="{{ route('campaigns.show', $campaign) }}" class="font-medium text-secondary hover:underline">View</a><span class="px-1 text-gray-300">·</span><a href="{{ route('campaigns.edit', $campaign) }}" class="font-medium text-secondary hover:underline">Edit</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No social ad campaigns yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>{{ $campaigns->links() }}</div>
            </section>

            <section class="hub-panel hidden space-y-5" data-panel="packages" role="tabpanel">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="font-heading text-xl font-semibold text-primary">Tour packages</h3>
                        <p class="mt-1 text-sm text-gray-500">Choose packages to feature in brochures and social promotions.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('packages.index') }}" class="rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-primary transition hover:border-secondary hover:text-secondary">Package management</a>
                        <a href="{{ route('packages.create') }}" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-secondary">Add package</a>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($packages as $package)
                        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-secondary">{{ $package->destination }}</p>
                                    <h4 class="mt-1 font-semibold text-primary">{{ $package->name }}</h4>
                                </div>
                                <span class="rounded-full {{ $package->status === 'published' ? 'bg-success/10 text-success' : 'bg-background text-gray-600' }} px-2.5 py-1 text-xs font-semibold capitalize">{{ $package->status }}</span>
                            </div>
                            <p class="mt-3 text-sm capitalize text-gray-500">{{ $package->type }} · {{ $package->duration_days }} days</p>
                            <div class="mt-4 flex items-end justify-between">
                                <p class="font-heading text-xl font-semibold text-primary">₱{{ number_format($package->price, 2) }}<span class="ml-1 text-xs font-normal text-gray-500">/ traveler</span></p>
                                <a href="{{ route('packages.edit', $package) }}" class="text-sm font-semibold text-secondary hover:underline">Edit package</a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-border p-8 text-center sm:col-span-2 xl:col-span-3">
                            <p class="font-medium text-primary">No tour packages found</p>
                            <p class="mt-1 text-sm text-gray-500">Create a package to feature it in your marketing materials.</p>
                            <a href="{{ route('packages.create') }}" class="mt-4 inline-flex rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-secondary">Add a tour package</a>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hub = document.querySelector('.marketing-hub');
        if (!hub) return;

        const tabs = hub.querySelectorAll('.hub-tab');
        const panels = hub.querySelectorAll('.hub-panel');

        function activateTab(name) {
            const targetTab = Array.from(tabs).find((tab) => tab.dataset.tab === name);
            if (!targetTab) return;

            tabs.forEach((tab) => {
                const active = tab === targetTab;
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
                tab.classList.toggle('bg-primary', active);
                tab.classList.toggle('text-white', active);
                tab.classList.toggle('shadow-sm', active);
                tab.classList.toggle('text-gray-600', !active);
            });
            panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.panel !== name));
        }

        tabs.forEach((tab) => tab.addEventListener('click', () => activateTab(tab.dataset.tab)));
        hub.querySelectorAll('[data-open-tab]').forEach((button) => {
            button.addEventListener('click', () => activateTab(button.dataset.openTab));
        });

        const hasBrochureError = @json($errors->has('brochure'));
        activateTab(new URLSearchParams(window.location.search).get('tab') || (hasBrochureError ? 'brochures' : 'overview'));
    });
</script>
@endsection
