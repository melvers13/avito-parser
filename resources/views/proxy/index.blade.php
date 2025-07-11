@extends('layouts.app')

@section('title', 'Прокси')

@section('header-js')

@endsection

@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Inner content -->
        <div class="content-inner">

            <!-- Content area -->
            <div class="content container">

                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="ph-warning-circle me-2"></i>
                    <span class="fw-semibold">Внимание!</span> Avito защищен от парсинга и нужно использовать мобильные прокси, которые каждые 2 минуты <span class="fw-semibold">автоматически</span> меняют IP адреса.<br/>
                    Обычные прокси - работать <span class="fw-semibold">не будут</span>. Рекомендуется покупать их здесь - <a href="https://mobileproxy.space/" target="_blank">mobileproxy.space</a> или <a href="https://proxys.io/ru/buy-mobile-proxies?type=personal" target="_blank">proxys.io</a>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="mb-0">Загрузка прокси</h5>
                    </div>
                    <form method="post" action="{{ route('proxy.create') }}">
                        @csrf
                        <div class="card-body">
                            <div class="mb-3">
                                <textarea rows="15" cols="3" name="list" class="form-control" placeholder="host:port:user:pass" required></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-success">Загрузить прокси</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="mb-0">Список прокси</h5>
                    </div>

                    @if (count($proxies))
                    <table class="table table-hover table-xs">
                        <thead>
                        <th>IP:Port</th>
                        <th>Логин</th>
                        <th>Пароль</th>
                        </thead>
                        <tbody>
                        @foreach ($proxies as $proxy)
                            <tr>
                                <td>{{ $proxy->ip }}:{{ $proxy->port }}</td>
                                <td>{{ $proxy->login }}</td>
                                <td>{{ $proxy->password }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="text-body text-center p-3 text-muted">Прокси пока нет.</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('footer-js')

@endsection
