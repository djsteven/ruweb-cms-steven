@extends('admin.layouts.app')

@section('title', __('admin.claude_mcp'))

@section('content')
<div class="mb-6">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.claude_mcp') }}</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.claude_mcp_subtitle') }}</p>
</div>

<div class="max-w-3xl space-y-4">
    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">

        {{-- Step 1 --}}
        <div class="flex gap-3 mb-5">
            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-sky-500/10 text-sky-400 text-xs font-semibold flex items-center justify-center mt-0.5">1</span>
            <div>
                <p class="text-sm text-gray-300">{{ __('admin.claude_mcp_step1') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.claude_mcp_step1_path') }}</p>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="flex gap-3 mb-5">
            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-sky-500/10 text-sky-400 text-xs font-semibold flex items-center justify-center mt-0.5">2</span>
            <div class="w-full">
                <p class="text-sm text-gray-300 mb-3">{{ __('admin.claude_mcp_step2') }}</p>

                {{-- MCP Server URL --}}
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">{{ __('admin.claude_mcp_server_url') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="{{ $mcpUrl }}" id="mcp-url"
                               class="flex-1 bg-[#1a1a1a] border border-white/10 text-gray-300 text-xs rounded-md px-3 py-2 focus:outline-none select-all">
                        <button type="button" onclick="copyToClipboard('mcp-url', this)"
                                class="shrink-0 p-2 text-gray-500 hover:text-gray-300 transition-colors" title="{{ __('admin.copy') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- OAuth Client ID --}}
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">{{ __('admin.claude_mcp_client_id') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="{{ $clientId }}" id="mcp-client-id"
                               class="flex-1 bg-[#1a1a1a] border border-white/10 text-gray-300 text-xs rounded-md px-3 py-2 focus:outline-none select-all">
                        <button type="button" onclick="copyToClipboard('mcp-client-id', this)"
                                class="shrink-0 p-2 text-gray-500 hover:text-gray-300 transition-colors" title="{{ __('admin.copy') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- OAuth Secret --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ __('admin.claude_mcp_client_secret') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="password" readonly value="{{ $secret }}" id="mcp-secret"
                               class="flex-1 bg-[#1a1a1a] border border-white/10 text-gray-300 text-xs rounded-md px-3 py-2 focus:outline-none select-all">
                        <button type="button" onclick="toggleSecret(this)"
                                class="shrink-0 p-2 text-gray-500 hover:text-gray-300 transition-colors" title="{{ __('admin.show') }}"
                                data-target="mcp-secret">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <button type="button" onclick="copyToClipboard('mcp-secret', this)"
                                class="shrink-0 p-2 text-gray-500 hover:text-gray-300 transition-colors" title="{{ __('admin.copy') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="flex gap-3">
            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-sky-500/10 text-sky-400 text-xs font-semibold flex items-center justify-center mt-0.5">3</span>
            <div>
                <p class="text-sm text-gray-300">{{ __('admin.claude_mcp_step3') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.claude_mcp_step3_hint') }}</p>
            </div>
        </div>

    </div>

    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-sm font-semibold text-white">{{ __('admin.mcp_api_key') }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.mcp_api_key_hint') }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full {{ $user->hasMcpApiKey() ? 'bg-sky-500/10 text-sky-400' : 'bg-gray-500/10 text-gray-400' }}">
                {{ $user->hasMcpApiKey() ? __('admin.mcp_api_key_active') : __('admin.mcp_api_key_missing') }}
            </span>
        </div>

        @if ($newApiKey)
            <div class="mb-4 rounded-md border border-sky-500/20 bg-sky-500/5 p-3">
                <p class="text-xs text-sky-300 mb-2">{{ __('admin.mcp_api_key_generated_once') }}</p>
                <code class="block w-full overflow-x-auto rounded bg-[#0f0f0f] border border-white/10 px-3 py-2 text-xs text-sky-300">{{ $newApiKey }}</code>
            </div>
        @endif

        <div class="space-y-1 text-xs text-gray-500 mb-4">
            <p>{{ __('admin.mcp_api_key_generated_at') }}: {{ $user->mcp_api_key_generated_at?->format('Y-m-d H:i') ?? '—' }}</p>
            <p>{{ __('admin.mcp_api_key_last_used_at') }}: {{ $user->mcp_api_key_last_used_at?->format('Y-m-d H:i') ?? '—' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('admin.claude-mcp.api-key.generate') }}">
                @csrf
                <button type="submit" class="px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors">
                    {{ $user->hasMcpApiKey() ? __('admin.mcp_api_key_regenerate') : __('admin.mcp_api_key_generate') }}
                </button>
            </form>

            @if ($user->hasMcpApiKey())
                <form method="POST" action="{{ route('admin.claude-mcp.api-key.revoke') }}" onsubmit="return confirm('{{ __('admin.mcp_api_key_revoke_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-transparent border border-red-500/30 hover:border-red-500/50 text-red-300 text-sm font-medium rounded-md transition-colors">
                        {{ __('admin.mcp_api_key_revoke') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(inputId, btn) {
    const input = document.getElementById(inputId);
    navigator.clipboard.writeText(input.value).then(() => {
        const svg = btn.querySelector('svg');
        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
        setTimeout(() => {
            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>';
        }, 1500);
    });
}

function toggleSecret(btn) {
    const input = document.getElementById(btn.dataset.target);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
}
</script>
@endpush
