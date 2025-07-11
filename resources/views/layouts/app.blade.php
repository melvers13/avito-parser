<!DOCTYPE html>
<html lang="ru" dir="ltr" class="layout-static">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} - @yield('title')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Global stylesheets -->
    <link href="{{ asset('assets/fonts/inter/inter.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/icons/phosphor/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/ltr/all.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('assets/js/configurator.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/notifications/noty.min.js') }}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/app_main.js') }}"></script>
    <script src="{{ asset('assets/js/jquery/jquery.min.js') }}"></script>

    <!-- /theme JS files -->
    @yield('header-js')
</head>

<body>

@include('layouts.navbar')

<!-- Page content -->
<div class="page-content">
    @yield('content')
</div>

@yield('footer-js')

<!-- Configurator -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="demo_config">
    <div class="position-absolute top-50 end-100">
        <button type="button" class="btn btn-primary btn-icon translate-middle-y rounded-end-0" data-bs-toggle="offcanvas" data-bs-target="#demo_config">
            <i class="ph-gear"></i>
        </button>
    </div>

    <div class="offcanvas-header border-bottom py-0">
        <h5 class="offcanvas-title py-3">Настройки</h5>
        <button type="button" class="btn btn-light btn-sm btn-icon border-transparent rounded-pill" data-bs-dismiss="offcanvas">
            <i class="ph-x"></i>
        </button>
    </div>

    <div class="offcanvas-body">
        <div class="fw-semibold mb-2">Цветовая схема</div>
        <div class="list-group mb-3">
            <label class="list-group-item list-group-item-action form-check border-width-1 rounded mb-2">
                <div class="d-flex flex-fill my-1">
                    <div class="form-check-label d-flex me-2">
                        <i class="ph-sun ph-lg me-3"></i>
                        <div>
                            <span class="fw-bold">Светлый</span>
                            <div class="fs-sm text-muted">Включить светлые тона</div>
                        </div>
                    </div>
                    <input type="radio" class="form-check-input cursor-pointer ms-auto" name="main-theme" value="light" checked>
                </div>
            </label>

            <label class="list-group-item list-group-item-action form-check border-width-1 rounded mb-2">
                <div class="d-flex flex-fill my-1">
                    <div class="form-check-label d-flex me-2">
                        <i class="ph-moon ph-lg me-3"></i>
                        <div>
                            <span class="fw-bold">Темный</span>
                            <div class="fs-sm text-muted">Включить темные тона</div>
                        </div>
                    </div>
                    <input type="radio" class="form-check-input cursor-pointer ms-auto" name="main-theme" value="dark">
                </div>
            </label>

            <label class="list-group-item list-group-item-action form-check border-width-1 rounded mb-0">
                <div class="d-flex flex-fill my-1">
                    <div class="form-check-label d-flex me-2">
                        <i class="ph-translate ph-lg me-3"></i>
                        <div>
                            <span class="fw-bold">Автоматически</span>
                            <div class="fs-sm text-muted">Использовать системную тему</div>
                        </div>
                    </div>
                    <input type="radio" class="form-check-input cursor-pointer ms-auto" name="main-theme" value="auto">
                </div>
            </label>
        </div>

    </div>

</div>
<!-- /configurator -->

@if(session('status'))
    <script>
        //
        // Notify.
        //
        document.addEventListener('DOMContentLoaded', function() {
            const status = @json(session('status'));

            new Noty({
                theme: 'limitless',
                text: status.message,
                type: status.status === 'error' ? 'error' : 'success',
                timeout: 1500,
                layout: 'bottomRight',
                progressBar: false,
            }).show();
        });
    </script>
@endif
</body>
</html>
