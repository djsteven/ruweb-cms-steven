@if (session('success'))
    <div class="mb-4 p-3.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 rounded-md text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-3.5 bg-red-500/10 border border-red-500/20 text-red-300 rounded-md text-sm">
        {{ session('error') }}
    </div>
@endif
