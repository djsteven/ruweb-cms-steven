<details class="group rounded-lg border border-white/[0.06] bg-[#101010]">
    <summary class="flex items-center justify-between cursor-pointer list-none px-4 py-3 select-none">
        <span class="text-sm font-medium text-gray-300 group-open:text-white transition-colors">{{ __('admin.brevo_instructions_title') }}</span>
        <svg class="w-4 h-4 text-gray-600 transition-transform group-open:rotate-180 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </summary>
    <div class="px-4 pb-4 pt-3 border-t border-white/[0.08] space-y-4">
        <p class="text-xs text-gray-500">{{ __('admin.brevo_instructions_subtitle') }}</p>
        <ol class="space-y-2 text-sm text-gray-400 list-decimal list-outside ml-4 marker:text-gray-600">
            <li>{!! __('admin.brevo_step_1') !!}</li>
            <li>{!! __('admin.brevo_step_2') !!}</li>
            <li>{!! __('admin.brevo_step_3') !!}</li>
            <li>{!! __('admin.brevo_step_4') !!}</li>
            <li>{!! __('admin.brevo_step_5') !!}</li>
        </ol>

        <div class="flex gap-3 rounded-lg border border-white/[0.06] bg-white/[0.02] p-3">
            <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
            <div class="text-xs text-gray-400 leading-relaxed">
                <strong class="font-semibold text-gray-300">{{ __('admin.brevo_sender_warning_title') }}</strong>
                <p class="mt-0.5">{!! __('admin.brevo_sender_warning') !!}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="https://app.brevo.com/signup" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition-colors">
                {{ __('admin.brevo_link_signup') }}
                <svg class="w-3 h-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="https://app.brevo.com/settings/keys/api" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition-colors">
                {{ __('admin.brevo_link_api_keys') }}
                <svg class="w-3 h-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="https://app.brevo.com/senders/list" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition-colors">
                {{ __('admin.brevo_link_senders') }}
                <svg class="w-3 h-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
        <p class="text-xs text-gray-600 border-t border-white/[0.04] pt-3">{{ __('admin.brevo_last_updated') }}</p>
    </div>
</details>
