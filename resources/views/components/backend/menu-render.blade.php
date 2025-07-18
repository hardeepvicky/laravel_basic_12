<?php 
use Illuminate\Support\Facades\Route;
if (!isset($level))
{
    $level = 0;
}

?>
<ul {{ $attributes->merge() }} style="display: none;" data-level="{{ $level }}">
    <?php foreach($menus as $menu): ?>
    <li>
        <?php 
        if(isset($menu['route_name']) && $menu['route_name']): 
            $url = "#";
            if (Route::has($menu['route_name']))
            {
                $url = route($menu['route_name']);
            }
        ?>

            <a href="{{ $url }}" class="menu-a {{ $menu['is_active'] ? 'active' : '' }}">
                <i class="{{ $menu['icon'] }}"></i>
                <span>{{ $menu['title'] }}</span>
            </a>
        <?php else: ?>
        <a href="javascript:void(0);" class="accordian-title-level-{{ $level }}" data-level="{{ $level }}">
            <i class="{{ $menu['icon'] }}"></i>
            <span>{{ $menu['title'] }}</span>
            <?php if(isset($menu['links']) && $menu['links']): ?>
                <i class="iconly-Arrow-Right-2 icli"></i>
            <?php endif; ?>
        </a>
        <?php if(isset($menu['links']) && $menu['links']): ?>
            <x-Backend.menu-render :menus="$menu['links']" level="{{ $level + 1 }}" class="accordian-menu-level-{{ $level }}" />
        <?php endif; ?>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
