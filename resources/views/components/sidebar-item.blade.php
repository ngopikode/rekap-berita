@props([
    'route',        // route name
    'icon',         // icon class
    'label',        // text label
    'activeRoute'   // current active route
])

<a href="{{ route($route) }}" wire:navigate
   class="list-group-item list-group-item-action {{ $activeRoute === $route ? 'active' : '' }}">
    <i class="{{ $icon }}"></i> {!! $label !!}
</a>
