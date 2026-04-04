<div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5 space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('admin.menu_name') }}</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $menu?->name) }}"
            class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
            required
        >
        @error('name')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('admin.menu_slug') }}</label>
        <input
            type="text"
            id="slug"
            name="slug"
            value="{{ old('slug', $menu?->slug) }}"
            class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
            required
        >
        @error('slug')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="location" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('admin.menu_location') }}</label>
        <select
            id="location"
            name="location"
            class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50"
        >
            <option value="">{{ __('admin.menu_location_none') }}</option>
            @foreach(config('cms.menu_locations', []) as $value => $label)
                <option value="{{ $value }}" {{ old('location', $menu?->location) === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('location')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
