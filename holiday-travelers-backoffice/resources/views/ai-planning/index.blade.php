@extends('layouts.app')
@section('title', 'AI Resource Planning')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Decision support</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">AI resource planning</h2><p class="mt-2 text-sm text-gray-500">Turn booking, supplier, and campaign signals into practical next steps.</p></div><span class="flex items-center gap-2 rounded-full bg-accent/10 px-3 py-1.5 text-xs font-semibold text-accent"><span class="h-1.5 w-1.5 rounded-full bg-accent"></span>Live planning engine</span></div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <form method="POST" action="{{ route('ai-planning.forecast-demand') }}" class="group bg-card rounded-2xl border border-border border-t-4 border-t-secondary p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
            @csrf
            <h3 class="font-heading font-semibold text-primary mb-2">Demand forecast</h3>
            <p class="text-sm text-gray-500 mb-3">Predict upcoming package demand based on current booking trends.</p>
            <select name="package_id" class="w-full mb-3 px-3 py-2 rounded-lg border border-border text-sm text-gray-600">
                @foreach ($packages as $package)
                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                @endforeach
            </select>
            <button class="w-full px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium">Generate forecast</button>
        </form>

        <form method="POST" action="{{ route('ai-planning.recommend-supplier') }}" class="group bg-card rounded-2xl border border-border border-t-4 border-t-accent p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
            @csrf
            <h3 class="font-heading font-semibold text-primary mb-2">Supplier recommendation</h3>
            <p class="text-sm text-gray-500 mb-3">Find the best suppliers by cost, reputational score, and reliability.</p>
            <select name="category" class="w-full mb-2 px-3 py-2 rounded-lg border border-border text-sm text-gray-600">
                <option value="hotel">Hotel</option>
                <option value="airline">Airline</option>
                <option value="transport">Transport</option>
                <option value="tour_guide">Tour Guide</option>
            </select>
            <input type="text" name="location" placeholder="Location (optional)" class="w-full mb-3 px-3 py-2 rounded-lg border border-border text-sm text-gray-600">
            <button class="w-full px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium">Recommend supplier</button>
        </form>

        <form method="POST" action="{{ route('ai-planning.recommend-budget') }}" class="group bg-card rounded-2xl border border-border border-t-4 border-t-success p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
            @csrf
            <h3 class="font-heading font-semibold text-primary mb-2">Marketing budget</h3>
            <p class="text-sm text-gray-500 mb-3">Suggest which channels deserve more investment based on conversion data.</p>
            <div class="h-[42px] mb-3 rounded-lg bg-gray-50 border border-dashed border-border"></div>
            <button class="w-full px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium">Recommend allocation</button>
        </form>
    </div>

    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Activity log</p><h2 class="mt-1 font-heading font-semibold text-primary">Planning history</h2>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-background text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
                <tr>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Subject</th>
                    <th class="px-5 py-3">Summary</th>
                    <th class="px-5 py-3">Confidence</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Generated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($plans as $plan)
                    <tr class="transition hover:bg-background/70">
                        <td class="px-5 py-3 font-medium text-primary">{{ str($plan->plan_type)->headline() }}</td>
                        <td class="px-5 py-3">{{ $plan->subject ?? 'AI recommendation' }}</td>
                        <td class="px-5 py-3 text-gray-600 max-w-md">{{ $plan->summary }}</td>
                        <td class="px-5 py-4"><span class="rounded-full bg-accent/10 px-2.5 py-1 text-xs font-semibold text-accent">{{ $plan->confidence_score }}%</span></td>
                        <td class="px-5 py-4"><span class="rounded-full bg-success/10 px-2.5 py-1 text-xs font-semibold capitalize text-success">{{ $plan->status }}</span></td>
                        <td class="px-5 py-3 text-gray-400">{{ $plan->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No AI recommendations generated yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-5 py-4 border-t border-border">{{ $plans->links() }}</div>
    </div>
</div>
@endsection
