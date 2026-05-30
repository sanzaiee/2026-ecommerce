@php
    $menuItems = $menuItems ?? [];
    $dismissOnNavigate = $dismissOnNavigate ?? false;
@endphp

<ul class="navbar-nav nav-menu mb-0">
    @foreach ($menuItems as $item)
        <li class="nav-item">
            <a class="nav-link nav-menu__link" href="{{ $item['href'] }}"
                @if ($dismissOnNavigate) data-bs-dismiss="offcanvas" @endif>{{ $item['label'] }}</a>
        </li>
    @endforeach
</ul>
