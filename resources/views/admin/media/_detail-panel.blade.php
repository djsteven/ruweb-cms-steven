<div id="detail-panel" class="hidden fixed inset-y-0 right-0 z-50 w-full max-w-xs bg-[#111111] border-l border-white/[0.06] shadow-2xl overflow-y-auto">
    <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.06]">
        <h2 class="text-sm font-semibold text-white">{{ __('admin.details') }}</h2>
        <button id="detail-close" class="text-gray-600 hover:text-gray-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="p-5">
        <div id="detail-preview" class="mb-4 flex items-center justify-center bg-[#1a1a1a] rounded-lg overflow-hidden min-h-32"></div>

        <div class="mb-4">
            <p id="detail-filename" class="text-sm font-medium text-gray-300 break-all"></p>
            <p id="detail-size" class="text-xs text-gray-600 mt-0.5"></p>
        </div>

        <div class="mb-3">
            <label for="detail-alt" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_alt_text') }}</label>
            <input type="text" id="detail-alt"
                class="w-full px-3 py-2 bg-[#1a1a1a] border border-white/10 rounded-md text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
        </div>

        <div class="mb-5">
            <label for="detail-title" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_media_title') }}</label>
            <input type="text" id="detail-title"
                class="w-full px-3 py-2 bg-[#1a1a1a] border border-white/10 rounded-md text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
        </div>

        <div class="flex gap-2">
            <button id="detail-save" class="flex-1 py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
                {{ __('admin.btn_media_save') }}
            </button>
            <button id="detail-delete" class="py-2 px-3 bg-transparent hover:bg-red-500/10 text-red-500/70 hover:text-red-400 text-sm rounded-md border border-red-500/20 transition-colors">
                {{ __('admin.btn_delete') }}
            </button>
        </div>
    </div>
</div>
