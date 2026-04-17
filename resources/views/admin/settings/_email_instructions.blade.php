<div class="bg-emerald-500/[0.04] ring-1 ring-emerald-500/20 rounded-xl overflow-hidden">
    <details class="group">
        <summary class="flex items-center justify-between gap-3 p-4 cursor-pointer list-none select-none hover:bg-emerald-500/[0.06] transition-colors">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v4.5m0 3v.008H12v-.008Z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ __('admin.brevo_instructions_title') }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('admin.brevo_instructions_subtitle') }}</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </summary>
        <div class="px-4 pb-4 pt-1 border-t border-emerald-500/10 text-sm text-gray-300 space-y-4">
            <ol class="list-decimal list-outside space-y-2 pl-5 marker:text-emerald-400">
                <li>
                    {!! __('admin.brevo_step_1') !!}
                </li>
                <li>
                    {!! __('admin.brevo_step_2') !!}
                </li>
                <li>
                    {!! __('admin.brevo_step_3') !!}
                </li>
                <li>
                    {!! __('admin.brevo_step_4') !!}
                </li>
                <li>
                    {!! __('admin.brevo_step_5') !!}
                </li>
            </ol>

            <div class="bg-amber-500/[0.06] border border-amber-500/20 rounded-md p-3 text-xs text-amber-200/90">
                <strong class="font-semibold">{{ __('admin.brevo_sender_warning_title') }}</strong>
                <p class="mt-1">{!! __('admin.brevo_sender_warning') !!}</p>
            </div>

            <div class="flex flex-wrap gap-3 pt-1">
                <a href="https://app.brevo.com/signup" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-xs text-emerald-400 hover:text-emerald-300">
                    {{ __('admin.brevo_link_signup') }}
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
                <a href="https://app.brevo.com/settings/keys/api" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-xs text-emerald-400 hover:text-emerald-300">
                    {{ __('admin.brevo_link_api_keys') }}
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
                <a href="https://app.brevo.com/senders/list" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-xs text-emerald-400 hover:text-emerald-300">
                    {{ __('admin.brevo_link_senders') }}
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            </div>
        </div>
    </details>
</div>
