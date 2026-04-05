@auth
<nav id="flaxt-admin-bar"
     class="fixed top-0 inset-x-0 z-[9999] h-9 bg-gray-900 flex items-center px-2 sm:px-4 shadow-sm">

    {{-- Left: actions --}}
    <div class="flex items-center min-w-0">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-1.5 px-2 py-1 rounded text-emerald-400 hover:text-emerald-300 hover:bg-white/5 transition-colors text-[11px] font-semibold tracking-wide flex-none"
           title="Ir al Dashboard">
            <svg class="w-3.5 h-3.5 flex-none" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7A1 1 0 003 11h1v6a1 1 0 001 1h4v-4h2v4h4a1 1 0 001-1v-6h1a1 1 0 00.707-1.707l-7-7z"/>
            </svg>
            <span class="hidden sm:inline">Flaxt CMS</span>
        </a>

        <span class="text-gray-700 mx-1 flex-none select-none">|</span>

        {{-- Edit current page --}}
        @isset($page)
        <a href="{{ route('admin.pages.edit', $page->id) }}"
           class="flex items-center gap-1.5 px-2 py-1 rounded text-gray-400 hover:text-white hover:bg-white/10 transition-colors text-[11px] flex-none"
           title="Editar esta página">
            <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span class="hidden sm:inline">Editar página</span>
        </a>
        @endisset

        {{-- Edit current post --}}
        @isset($post)
        <a href="{{ route('admin.posts.edit', $post->id) }}"
           class="flex items-center gap-1.5 px-2 py-1 rounded text-gray-400 hover:text-white hover:bg-white/10 transition-colors text-[11px] flex-none"
           title="Editar este post">
            <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span class="hidden sm:inline">Editar post</span>
        </a>
        @endisset

        <span class="text-gray-700 mx-1 flex-none select-none">|</span>

        {{-- Menus --}}
        <a href="{{ route('admin.menus.index') }}"
           class="flex items-center gap-1.5 px-2 py-1 rounded text-gray-400 hover:text-white hover:bg-white/10 transition-colors text-[11px] flex-none"
           title="Editar menús">
            <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            <span class="hidden sm:inline">Menús</span>
        </a>
    </div>

    {{-- Right: user info --}}
    <div class="ml-auto flex items-center gap-1.5 text-[11px] text-gray-400 flex-none pl-2">
        <svg class="w-3.5 h-3.5 flex-none" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
        </svg>
        <span class="hidden sm:inline truncate max-w-[120px]">{{ auth()->user()->name }}</span>
    </div>

</nav>
@endauth
