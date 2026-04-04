@if($items->isNotEmpty())
<ul {{ $attributes }}>
    @foreach($items as $item)
        @include('components.menu-item', ['item' => $item, 'depth' => 0])
    @endforeach
</ul>
@endif
