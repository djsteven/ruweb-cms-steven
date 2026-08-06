@php
    $children = $item->children ?? collect();
    $hasChildren = $children->isNotEmpty();
    $isMegaMenu = $depth === 0 && str($item->label)->lower()->contains('retreat');
@endphp

<li class="{{ trim(($hasChildren ? 'has-children ' : '').($isMegaMenu ? 'has-mega-menu ' : '')) }}">
    <a href="{{ $item->resolveUrl() }}"
       @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
        {{ $item->label }}
    </a>

    @if($hasChildren)
        @if($isMegaMenu)
            <div class="ama-mega-menu" aria-label="{{ $item->label }} submenu">
                <div class="ama-mega-panel">
                    @foreach($children as $column)
                        <section class="ama-mega-column">
                            <a href="{{ $column->resolveUrl() }}" class="ama-mega-heading"
                               @if($column->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
                                {{ $column->label }}
                            </a>

                            @if($column->children->isNotEmpty())
                                <ul class="ama-mega-links">
                                    @foreach($column->children as $child)
                                        @include('components.menu-item', ['item' => $child, 'depth' => $depth + 2])
                                    @endforeach
                                </ul>
                            @endif
                        </section>
                    @endforeach
                </div>
            </div>
        @else
            <ul class="sub-menu ama-dropdown-menu">
                @foreach($children as $child)
                    @include('components.menu-item', ['item' => $child, 'depth' => $depth + 1])
                @endforeach
            </ul>
        @endif
    @endif
</li>
