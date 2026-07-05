<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        @foreach($menus as $menu)
            
            {{-- Cek apakah menu ini memiliki anak (sub-menu) --}}
            @if($menu->children && $menu->children->count() > 0)

                {{-- Cek apakah salah satu anak menu sedang aktif --}}
                @php
                    $isParentActive = false;
                    foreach($menu->children as $child) {
                        if(request()->is(trim($child->url, '/'))) {
                            $isParentActive = true;
                        }
                    }
                @endphp
                
                <!-- LEVEL 1: MENU DENGAN SUB-MENU (DROPDOWN) -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        {{-- Icon Dinamis dari Database --}}
                        <i class="nav-icon {{ $menu->icon_class ?? 'fas fa-folder' }}"></i>
                        <p>
                            {{ $menu->title }}
                            {{-- Panah indikator dropdown otomatis di kanan --}}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    
                    <!-- LEVEL 2: ANAK MENU (SUB-MENU) -->
                    <ul class="nav nav-treeview">
                        @foreach($menu->children as $child)
                            <li class="nav-item">
                                <a href="{{ $child->url }}" class="nav-link">
                                    {{-- Icon lingkaran kecil bawaan AdminLTE untuk sub-menu --}}
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ $child->title }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>

            @else
                
                <!-- LEVEL 1: MENU TUNGGAL (TANPA SUB-MENU) -->
                <li class="nav-item">
                    <a href="{{ $menu->url ?? '#' }}" class="nav-link">
                        <i class="nav-icon {{ $menu->icon_class ?? 'fas fa-link' }}"></i>
                        <p>{{ $menu->title }}</p>
                    </a>
                </li>

            @endif

        @endforeach

    </ul>
</nav>