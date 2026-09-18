<div class="grid gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="tour_package_id" class="block text-sm font-medium mb-1">Tour package</label>
        <select id="tour_package_id" name="tour_package_id" class="w-full rounded-lg border-border" required>
            <option value="">Select a package</option>
            @foreach ($packages as $package)
                <option value="{{ $package->id }}" @selected((string) old('tour_package_id', $booking->tour_package_id ?? '') === (string) $package->id)>
                    {{ $package->name }} - {{ number_format($package->price, 2) }}
                </option>
            @endforeach
        </select>
        @error('tour_package_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="customer_name" class="block text-sm font-medium mb-1">Customer name</label>
        <input id="customer_name" name="customer_name" value="{{ old('customer_name', $booking->customer_name ?? '') }}" class="w-full rounded-lg border-border" required>
        @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="business_partner_id" class="block text-sm font-medium mb-1">Business partner</label>
        <select id="business_partner_id" name="business_partner_id" class="w-full rounded-lg border-border">
            <option value="">Direct booking</option>
            @foreach ($partners as $partner)
                <option value="{{ $partner->id }}" @selected((string) old('business_partner_id', $booking->business_partner_id ?? '') === (string) $partner->id)>{{ $partner->name }}</option>
            @endforeach
        </select>
        @error('business_partner_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="customer_email" class="block text-sm font-medium mb-1">Email</label>
        <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email', $booking->customer_email ?? '') }}" class="w-full rounded-lg border-border">
        @error('customer_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="customer_phone" class="block text-sm font-medium mb-1">Phone</label>
        <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $booking->customer_phone ?? '') }}" class="w-full rounded-lg border-border">
        @error('customer_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="pax" class="block text-sm font-medium mb-1">Passengers</label>
        <input id="pax" type="number" min="1" name="pax" value="{{ old('pax', $booking->pax ?? 1) }}" class="w-full rounded-lg border-border" required>
        @error('pax')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="travel_date" class="block text-sm font-medium mb-1">Travel date</label>
        <input id="travel_date" type="date" name="travel_date" value="{{ old('travel_date', isset($booking) ? $booking->travel_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-border" required>
        @error('travel_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="total_amount" class="block text-sm font-medium mb-1">Total amount</label>
        <input id="total_amount" type="number" min="0" step="0.01" name="total_amount" value="{{ old('total_amount', $booking->total_amount ?? '') }}" class="w-full rounded-lg border-border" required>
        @error('total_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
