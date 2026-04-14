@extends('admin.layouts.app')

@section('title', __('admin.profile'))

@section('content')
<div class="mb-6">
    <h1 class="text-lg font-semibold text-white">{{ __('admin.profile') }}</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ __('admin.profile_subtitle') }}</p>
</div>

<div class="space-y-4 max-w-3xl">
    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
            <h2 class="text-sm font-semibold text-white mb-4">{{ __('admin.profile_identity') }}</h2>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.field_name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
                    @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.field_email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
                    @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.field_role') }}</label>
                    @php
                        $roleKey = 'admin.role_' . $user->role;
                        $roleLabel = __($roleKey) !== $roleKey ? __($roleKey) : ucfirst($user->role);
                    @endphp
                    <p class="text-sm text-gray-400 capitalize bg-[#1a1a1a] border border-white/10 rounded-md px-3 py-2">{{ $roleLabel }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5 mt-4">
            <h2 class="text-sm font-semibold text-white mb-1">{{ __('admin.profile_change_password') }}</h2>
            <p class="text-xs text-gray-500 mb-4">{{ __('admin.profile_change_password_hint') }}</p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.field_current_password') }}</label>
                    <input type="password" id="current_password" name="current_password"
                           class="w-full sm:w-1/2 bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
                    @error('current_password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.field_new_password') }}</label>
                    <input type="password" id="password" name="password"
                           class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
                    @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">{{ __('admin.field_confirm_password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="w-full bg-[#1a1a1a] border border-white/10 text-white text-sm rounded-md px-3 py-2 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
                {{ __('admin.btn_save_changes') }}
            </button>
        </div>
    </form>

    <div class="bg-[#141414] ring-1 ring-white/[0.06] rounded-xl p-5">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-sm font-semibold text-white">{{ __('admin.mcp_api_key') }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin.mcp_api_key_hint') }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full {{ $user->hasMcpApiKey() ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-500/10 text-gray-400' }}">
                {{ $user->hasMcpApiKey() ? __('admin.mcp_api_key_active') : __('admin.mcp_api_key_missing') }}
            </span>
        </div>

        @if ($newApiKey)
            <div class="mb-4 rounded-md border border-emerald-500/20 bg-emerald-500/5 p-3">
                <p class="text-xs text-emerald-300 mb-2">{{ __('admin.mcp_api_key_generated_once') }}</p>
                <code class="block w-full overflow-x-auto rounded bg-[#0f0f0f] border border-white/10 px-3 py-2 text-xs text-emerald-300">{{ $newApiKey }}</code>
            </div>
        @endif

        <div class="space-y-1 text-xs text-gray-500 mb-4">
            <p>{{ __('admin.mcp_api_key_generated_at') }}: {{ $user->mcp_api_key_generated_at?->format('Y-m-d H:i') ?? '—' }}</p>
            <p>{{ __('admin.mcp_api_key_last_used_at') }}: {{ $user->mcp_api_key_last_used_at?->format('Y-m-d H:i') ?? '—' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('admin.profile.mcp-api-key.generate') }}">
                @csrf
                <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
                    {{ $user->hasMcpApiKey() ? __('admin.mcp_api_key_regenerate') : __('admin.mcp_api_key_generate') }}
                </button>
            </form>

            @if ($user->hasMcpApiKey())
                <form method="POST" action="{{ route('admin.profile.mcp-api-key.revoke') }}" onsubmit="return confirm('{{ __('admin.mcp_api_key_revoke_confirm') }}')">
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
