<?php
$false = false;
?>
<aside class="page-sidebar">
    <div class="left-arrow" id="left-arrow">
        <i data-feather="arrow-left"></i>
    </div>    
    <div class="main-sidebar" id="main-sidebar">        
        <div class="m-2">
            <span class="btn btn-pill btn-light btn-sm w-100 text-dark" id="search-menu-opener">
                Search Menu (F1)
                <i class="fa-solid fa-search"></i>
            </span>
        </div>
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

<div class="modal fade" id="modal-search-menu" tabindex="-1" aria-labelledby="modal-add-via-upload"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-5">Search Menu</h3>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <x-Inputs.drop-down id="search-menu" name="search_menu" :label="$false" :list="$header_menu_list" class="modal-search-select2"/>
            </div>            
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $(".menu-a.active").parents("li").addClass("active");
        $(".menu-a.active").parents("ul").show();
        $(".menu-a.active").closest("li.sidebar-list").find("> a.sidebar-link").addClass("active");

        $("#search-menu-opener").click(function()
        {
            $("#modal-search-menu").modal("show");
        });

        setTimeout(() => {
            $(".modal-search-select2").select2({
                placeHolder: "Please Select",
                theme: "bootstrap-5",
                dropdownParent: $("#modal-search-menu")
            });
        }, 500);

        $("#modal-search-menu").on('shown.bs.modal', function() {
            $("select#search-menu").val("");
            $("select#search-menu").select2('open');
        });

        $("#search-menu").change(function(){
            var v = $(this).val();

            if (v)
            {
                $.loader.init().setInfo("Redirecting....").show();
                window.location.href = v;
            }
        });

        $(document).on('keydown', function(event) {
            // event.which is deprecated, but widely supported.
            // event.key is more modern, but F-keys can be tricky with it.
            // event.code is also an option.
            if (event.which === 112) {
                event.preventDefault();
                $("#search-menu-opener").trigger("click");
            }
        });
    });
</script>