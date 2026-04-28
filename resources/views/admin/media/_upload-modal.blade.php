<div id="upload-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#141414] ring-1 ring-white/[0.08] rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.06]">
            <h2 class="text-sm font-semibold text-white">{{ __('admin.upload_file') }}</h2>
            <button id="upload-close" class="text-gray-600 hover:text-gray-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="upload-form" class="p-5">
            <div id="drop-zone" class="border-2 border-dashed border-white/[0.08] rounded-lg p-8 text-center cursor-pointer hover:border-white/20 transition-colors mb-4">
                <div id="upload-empty-state">
                    <svg class="w-8 h-8 mx-auto text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">{{ __('admin.drag_and_drop') }} <span class="text-sky-500">{{ __('admin.browse') }}</span></p>
                    <p id="file-name" class="text-xs text-sky-400 mt-2 min-h-4"></p>
                </div>

                <div id="upload-preview-wrap" class="hidden relative w-full max-w-[220px] mx-auto">
                    <img id="upload-preview-image" src="" alt="" class="w-full h-40 object-contain rounded-md border border-white/10">
                    <button type="button" id="upload-remove-file" class="absolute top-2 right-2 w-7 h-7 rounded-full bg-black/70 text-white hover:bg-black transition-colors flex items-center justify-center" aria-label="{{ __('admin.clear') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <input type="file" id="file-input" name="files[]" multiple class="hidden">
            </div>

            <div class="mb-3">
                <label for="upload-alt" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_alt_text') }}</label>
                <input type="text" id="upload-alt" name="alt"
                    class="w-full px-3 py-2 bg-[#1a1a1a] border border-white/10 rounded-md text-white text-sm placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
            </div>

            <div class="mb-5">
                <label for="upload-title" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin.field_media_title') }}</label>
                <input type="text" id="upload-title" name="title"
                    class="w-full px-3 py-2 bg-[#1a1a1a] border border-white/10 rounded-md text-white text-sm placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50">
            </div>

            <div id="upload-progress" class="hidden mb-4">
                <div class="w-full bg-[#1a1a1a] rounded-full h-1">
                    <div class="bg-sky-500 h-1 rounded-full animate-pulse w-full"></div>
                </div>
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
                {{ __('admin.upload') }}
            </button>
        </form>
    </div>
</div>
