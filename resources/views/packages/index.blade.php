@extends('layouts.app')
@section('title', 'Tour Packages')

@section('content')
<div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
    <div class="flex flex-col gap-4 border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent p-5 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Tour inventory</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Tour packages</h2><p class="mt-2 text-sm text-gray-500">Manage destinations, pricing, and package availability.</p></div>
        <a href="{{ route('packages.create') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ Add package</a>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead class="bg-background text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
            <tr>
                <th class="px-5 py-3">Name</th>
                <th class="px-5 py-3">Destination</th>
                <th class="px-5 py-3">Type</th>
                <th class="px-5 py-3">Price</th>
                <th class="px-5 py-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-border">
            @forelse ($packages as $package)
                <tr class="transition hover:bg-background/70">
                    <td class="px-5 py-4 font-medium text-primary">{{ $package->name }}</td>
                    <td class="px-5 py-4">{{ $package->destination }}</td>
                    <td class="px-5 py-4 capitalize">{{ $package->type }}</td>
                    <td class="px-5 py-4">₱{{ number_format($package->price, 2) }}</td>
                    <td class="px-5 py-4"><span class="rounded-full bg-success/10 px-2.5 py-1 text-xs font-semibold capitalize text-success">{{ $package->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No tour packages yet.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    <div class="border-t border-border px-5 py-4">{{ $packages->links() }}</div>
</div>
@endsection