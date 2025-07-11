@extends('layouts.app')

@section('title', 'Главная')

@section('header-js')

@endsection

@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Inner content -->
        <div class="content-inner">

            <!-- Content area -->
            <div class="content container">

                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="mb-0">Парсер</h5>
                    </div>

                    <form method="post" action="{{ route('home.parsing') }}" class="card-body d-sm-flex">
                        @csrf
                        <div class="form-control-feedback form-control-feedback-start flex-grow-1 mb-3 mb-sm-0">
                            <input type="text" name="q" class="form-control" placeholder="Что ищем?" required @if (!empty($status)) disabled @endif>
                            <div class="form-control-feedback-icon">
                                <i class="ph-magnifying-glass"></i>
                            </div>
                        </div>

                        <div class="ms-sm-3">
                            <button type="submit" class="btn btn-primary w-100 w-sm-auto" @if (!empty($status)) disabled @endif>Начать парсинг</button>
                        </div>
                    </form>
                </div>

                @if (!empty($status))
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="mb-0">Лог</h5>
                    </div>
                    <div class="card-body">
                        <div class="border rounded p-3">
                            <div id="parser-status"><i class="ph-spinner spinner"></i></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('footer-js')
<script>
//
// Проверка статуса парсинга.
//
setInterval(() => {
    fetch('{{ route('home.status', [], true) }}')
        .then(response => response.text())
        .then(html => {
            document.getElementById('parser-status').innerHTML = html;
        })
        .catch(error => {
            console.error('Ошибка при получении статуса:', error);
        });
}, 2000);
</script>
@endsection
