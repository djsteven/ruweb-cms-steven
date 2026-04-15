<div class="flex-none px-5 py-3 border-t border-white/[0.06] bg-[#111111]">
    @if ($model->isPublished())
        <button type="button" id="update-btn" disabled
                class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-medium rounded-md transition-colors">
            {{ __('admin.btn_save_changes') }}
        </button>
    @else
        <div class="flex gap-2">
            <button type="button" id="save-draft-btn" disabled
                    class="flex-1 px-3 py-2 bg-white/5 hover:bg-white/10 disabled:opacity-40 disabled:cursor-not-allowed text-gray-300 text-sm font-medium rounded-md transition-colors">
                {{ __('admin.btn_save_draft') }}
            </button>
            <button type="button" id="publish-btn"
                    class="flex-1 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
                {{ __('admin.btn_publish') }}
            </button>
        </div>
    @endif
</div>
