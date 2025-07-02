<ul {{ $attributes->merge(["class" => "my-custom-menu"]) }} style="display:none;">
@foreach ($menus as $menu)
<li>
    @if(isset($menu['route_name']) && $menu['route_name'])
        <a class="sidebar-link my-custom-menu-link {{ $menu['is_active'] ? 'active' : '' }}"
            href="{{ isset($menu['route_name']) ? route($menu['route_name']) : '#' }}">
            <i class="{{ $menu['icon'] ?? "" }}"></i>
            <h6>{{ $menu['title'] }}</h6>
            @if (isset($menu['badge']))
                <span class="badge">{{ $menu['badge'] }}</span>
            @endif
        </a>
    @else
        <a class="submenu-title my-custom-menu-link justify-content-between" href="javascript:void(0);">
            <div class="d-flex justify-content-start">
                <i class="{{ $menu['icon'] ?? "" }}"></i>
                <h6 class="ml-2">{{ $menu['title'] }}</h6>
            </div>
            <i class="iconly-Arrow-Right-2 icli"></i>
        </a>
        @if (isset($menu['links']) && $menu['links'])
            <x-Backend.menu-render :menus="$menu['links']" class="according-submenu" />        
        @endif
    @endif
</li>
@endforeach
</ul>