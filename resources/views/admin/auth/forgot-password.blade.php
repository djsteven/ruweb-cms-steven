@extends('admin.layouts.guest')

@section('title', __('admin.forgot_password'))

@section('content')
<div class="mb-8 text-center">
    <div class="inline-flex items-center gap-2 mb-6">
        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
        <span class="text-base font-semibold text-white tracking-tight">{{ $siteName }}</span>
    </div>
    <h1 class="text-xl font-semibold text-white">{{ __('admin.forgot_password') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('admin.forgot_password_subtitle') }}</p>
</div>

<div class="bg-[#141414] ring-1 ring-white/[0.08] rounded-xl p-6">
    @include('admin.partials.alerts')

    <form method="POST" action="{{ route('admin.password.email') }}">
        @csrf

        <div class="mb-5">
            <label for="email" class="block text-xs font-medium text-gray-400 mb-1.5">{{ __('admin.email') }}</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full px-3 py-2 bg-[#1a1a1a] border border-white/10 rounded-md text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500/50 transition-colors"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full py-2 px-4 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition-colors"
        >
            {{ __('admin.send_reset_link') }}
        </button>

        <p class="mt-4 text-center">
            <a href="{{ route('admin.login') }}" class="text-xs text-gray-400 hover:text-sky-400">
                ← {{ __('admin.back_to_login') }}
            </a>
        </p>
    </form>
</div>
@endsection
