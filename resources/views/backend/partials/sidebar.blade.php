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
                <ul class="sidebar-submenu" style="display: none;">
                    <?php foreach($menu['links'] as $submenu): ?>
                    <li>
                        <?php if(isset($submenu['route_name']) && $submenu['route_name']): ?>
                        <a href="{{ route($submenu['route_name']) }}"
                            class="menu-a {{ $submenu['is_active'] ? 'active' : '' }}">
                            <i class="{{ $submenu['icon'] }}"></i>
                            <span>{{ $submenu['title'] }}</span>
                        </a>
                        <?php else: ?>
                        <a href="javascript:void(0);" class="submenu-title">
                            <i class="{{ $submenu['icon'] }}"></i>
                            <span>{{ $submenu['title'] }}</span>
                            <i class="iconly-Arrow-Right-2 icli"></i>
                        </a>
                        <?php if(isset($submenu['links']) && $submenu['links']): ?>
                        <ul class="according-submenu" style="display: none;">
                            <?php foreach($submenu['links'] as $nestedSubmenu): ?>
                            <li>
                                <?php if(isset($nestedSubmenu['route_name']) && $nestedSubmenu['route_name']): ?>
                                <a href="{{ route($nestedSubmenu['route_name']) }}"
                                    class="menu-a {{ $nestedSubmenu['is_active'] ? 'active' : '' }}">
                                    <i class="{{ $nestedSubmenu['icon'] }}"></i>
                                    <span>{{ $nestedSubmenu['title'] }}</span>
                                </a>
                                <?php else: ?>
                                <a href="javascript:void(0);" class="submenu-title-level-4">
                                    <i class="{{ $nestedSubmenu['icon'] }}"></i>
                                    <span>{{ $nestedSubmenu['title'] }}</span>
                                    <i class="iconly-Arrow-Right-2 icli"></i>
                                </a>
                                <?php if(isset($nestedSubmenu['links']) && $nestedSubmenu['links']): ?>
                                <ul class="submenu-level-4" style="display: none;">
                                    @foreach ($nestedSubmenu['links'] as $nestedSubmenu2)
                                        <li>
                                            <?php if(isset($nestedSubmenu2['route_name']) && $nestedSubmenu2['route_name']): ?>
                                            <a href="{{ route($nestedSubmenu2['route_name']) }}"
                                                class="menu-a {{ $nestedSubmenu2['is_active'] ? 'active' : '' }}">
                                                <i class="{{ $nestedSubmenu2['icon'] }}"></i>
                                                <span>{{ $nestedSubmenu2['title'] }}</span>
                                            </a>
                                            <?php else: ?>
                                            <a href="javascript:void(0);" class="">
                                                <i class="{{ $nestedSubmenu2['icon'] }}"></i>
                                                <span>{{ $nestedSubmenu2['title'] }}</span>
                                            </a>
                                            <?php endif; ?>
                                        </li>
                                        <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                                <?php endif; ?>

                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        <?php endif; ?>

                    </li>
                    <?php endforeach;?>
                </ul>
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
