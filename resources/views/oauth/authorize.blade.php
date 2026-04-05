@extends('admin.layouts.guest')

@section('title', 'Authorize')

@section('content')
<div class="mb-8 text-center">
    <div class="inline-flex items-center gap-2 mb-6">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
        <span class="text-base font-semibold text-white tracking-tight">{{ $siteName }}</span>
    </div>
    <h1 class="text-xl font-semibold text-white">Connect claude.ai</h1>
    <p class="text-sm text-gray-500 mt-1">Sign in to authorize access to your CMS</p>
</div>

<div class="bg-[#141414] ring-1 ring-white/[0.08] rounded-xl p-6">
    <form method="POST" action="{{ route('oauth.authorize.submit') }}">
        @csrf

        {{-- OAuth hidden fields --}}
        <input type="hidden" name="response_type"         value="{{ $response_type }}">
        <input type="hidden" name="client_id"             value="{{ $client_id }}">
        <input type="hidden" name="redirect_uri"          value="{{ $redirect_uri }}">
        <input type="hidden" name="code_challenge"        value="{{ $code_challenge }}">
        <input type="hidden" name="code_challenge_method" value="{{ $code_challenge_method }}">
        <input type="hidden" name="state"                 value="{{ $state }}">

        <div class="mb-4">
            <label for="email" class="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full px-3 py-2 bg-[#1a1a1a] border border-white/10 rounded-md text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-colors"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-xs font-medium text-gray-400 mb-1.5">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="w-full px-3 py-2 bg-[#1a1a1a] border border-white/10 rounded-md text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-colors"
            >
            @error('password')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:ring-offset-2 focus:ring-offset-[#141414]"
        >
            Authorize
        </button>
    </form>
</div>
@endsection
