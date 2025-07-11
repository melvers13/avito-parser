@php(
    $current_route_name = Route::currentRouteName()
)

<!-- Main navbar -->
<div class="navbar navbar-expand-xl navbar-static shadow">
    <div class="container px-sm-3 pt-1">
        <div class="flex-1">
            <a href="{{ route('home.index') }}" class="text-body">
                <h1 class="mb-0">{{ config('app.name') }}</h1>
            </a>
        </div>

        <div class="d-flex w-100 w-xl-auto overflow-auto overflow-xl-visible scrollbar-hidden border-top border-top-xl-0 order-1 order-xl-0 pt-2 pt-xl-0 mt-2 mt-xl-0">
            <ul class="nav gap-1 justify-content-center flex-nowrap flex-xl-wrap mx-auto">
                <li class="nav-item">
                    <a href="{{ route('home.index') }}" class="navbar-nav-link rounded @if(str_contains($current_route_name, 'home.')) active @endif">
                        <i class="ph-house me-2"></i>
                        Главная
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('proxy.index') }}" class="navbar-nav-link rounded @if(str_contains($current_route_name, 'proxy.')) active @endif">
                        <i class="ph-list me-2"></i>
                        Прокси
                    </a>
                </li>
            </ul>
        </div>

        <ul class="nav gap-1 flex-xl-1 justify-content-end order-0 order-xl-1">

            <li class="nav-item nav-item-dropdown-xl dropdown">
                <a href="#" class="navbar-nav-link align-items-center rounded p-1" data-bs-toggle="dropdown">
                    <div class="status-indicator-container">
                        <img src="../../../assets/images/demo/users/face1.jpg" class="w-32px h-32px rounded-circle" alt="">
                        <span class="status-indicator bg-success"></span>
                    </div>
                    <span class="d-none d-md-inline-block mx-md-2">Admin</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- /main navbar -->
