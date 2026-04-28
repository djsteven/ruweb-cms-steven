@if (session('success'))
    <div class="mb-4 p-3.5 bg-sky-500/10 border border-sky-500/20 text-sky-300 rounded-md text-sm flex items-start gap-3">
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button"
                onclick="this.parentElement.remove()"
                class="text-sky-300/70 hover:text-sky-200 transition-colors -mr-1 -mt-0.5"
                aria-label="Close">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-3.5 bg-red-500/10 border border-red-500/20 text-red-300 rounded-md text-sm flex items-start gap-3">
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button"
                onclick="this.parentElement.remove()"
                class="text-red-300/70 hover:text-red-200 transition-colors -mr-1 -mt-0.5"
                aria-label="Close">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
@endif
