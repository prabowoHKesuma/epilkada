<nav>
    <ul>
        @foreach($menus as $menu)
            <li>
                <a href="{{ $menu->url ?? '#' }}">
                    <i class="{{ $menu->icon_class }}"></i> {{ $menu->title }}
                </a>
                @if($menu->children->count())
                    <ul>
                        @foreach($menu->children as $child)
                            <li><a href="{{ $child->url }}">{{ $child->title }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</nav>