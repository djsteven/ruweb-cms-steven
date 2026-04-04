<nav class="fixed top-0 z-50 w-full bg-gray-900 border-b border-gray-800">
    <div class="px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button id="sidebar-toggle" class="lg:hidden text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-white">
                {{ $siteName }}
            </a>
        </div>

        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-400">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-white transition-colors">
                    {{ __('admin.logout') }}
                </button>
            </form>
        </div>
    </div>
</nav>
