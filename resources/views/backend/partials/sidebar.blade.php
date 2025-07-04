<aside class="page-sidebar">
    <div class="left-arrow" id="left-arrow">
        <i data-feather="arrow-left"></i>
    </div>
    <div class="main-sidebar" id="main-sidebar">
        <ul class="sidebar-menu" id="simple-bar">
            <li class="pin-title sidebar-main-title">
                <div>
                    <h5 class="sidebar-title f-w-700">Pinned</h5>
                </div>
            </li>
            <li class="sidebar-main-title">
                <div>
                    <h5 class="lan-1 f-w-700 sidebar-title">General</h5>
                </div>
            </li>
            <?php foreach ($menus as $menu): ?>
            <li class="sidebar-list">
                <i class="fa-solid fa-thumbtack"></i>
                <?php if(isset($menu['route_name']) && $menu['route_name']): ?>
                <a class="sidebar-link menu-a {{ $menu['is_active'] ? 'active' : '' }}"
                    href="{{ route($menu['route_name']) }}">
                    <i class="{{ $menu['icon'] }}"></i>
                    <h6>{{ $menu['title'] }}</h6>
                    @if (isset($menu['badge']))
                        <span class="badge">{{ $menu['badge'] }}</span>
                    @endif
                </a>
                <?php else: ?>
                <a class="sidebar-link" href="javascript:void(0);">
                    <i class="{{ $menu['icon'] }}"></i>
                    <h6>{{ $menu['title'] }}</h6>
                    @if (isset($menu['badge']))
                        <span class="badge">{{ $menu['badge'] }}</span>
                    @endif
                    <i class="iconly-Arrow-Right-2 icli"></i>
                </a>
                <?php if(isset($menu['links']) && $menu['links']): ?>
                    <x-Backend.menu-render :menus="$menu['links']" level="1" class="sidebar-submenu" />
                <?php endif; ?>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
</aside>

<script type="text/javascript">
    $(function() {
        console.log("Active menu a : " + $(".menu-a.active").length);
        $(".menu-a.active").parents("li").addClass("active");
        $(".menu-a.active").parents("ul").show();
        // $(".menu-a.active").closest("li.sidebar-list").find("> a.sidebar-link").addClass("active");
    });
</script>
