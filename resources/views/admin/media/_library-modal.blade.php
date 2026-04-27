<div id="media-library-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-5xl bg-[#141414] ring-1 ring-white/[0.08] rounded-xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.06] gap-4">
            <div>
                <h2 class="text-sm font-semibold text-white">{{ __('admin.media') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('admin.choose_file') }}</p>
            </div>
            <button type="button" id="media-library-upload"
                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-md transition-colors">
                {{ __('admin.upload_file') }}
            </button>
            <button type="button" id="media-library-close" class="text-gray-600 hover:text-gray-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-5 py-4 border-b border-white/[0.06] flex flex-col sm:flex-row gap-3">
            <input type="text" id="media-library-search" placeholder="{{ __('admin.search_media_ph') }}" class="flex-1 bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
            <button type="button" id="media-library-refresh" class="px-3 py-2 bg-gray-800 border border-white/10 text-gray-400 text-sm rounded-md hover:bg-gray-700 transition-colors">
                {{ __('admin.search') }}
            </button>
        </div>

        <div id="media-library-status" class="px-5 py-3 text-sm text-gray-500 hidden"></div>

        <div id="media-library-grid" class="p-5 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 min-h-[200px] max-h-[60vh] overflow-y-auto"></div>

        <div id="media-library-pagination" class="hidden px-5 py-3 border-t border-white/[0.06] flex items-center justify-between gap-3 flex-wrap"></div>
    </div>
</div>
