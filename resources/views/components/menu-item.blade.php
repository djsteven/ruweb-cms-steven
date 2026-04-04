<li class="{{ $item->children->isNotEmpty() ? 'has-children' : '' }}">
    <a href="{{ $item->resolveUrl() }}"
       @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
        {{ $item->label }}
    </a>
    @if($item->children->isNotEmpty())
        <ul class="sub-menu">
            @foreach($item->children as $child)
                @include('components.menu-item', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>
