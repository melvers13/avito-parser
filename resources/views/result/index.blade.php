@extends('layouts.app')

@section('title', 'Прокси')

@section('header-js')
    <script src="{{ asset('assets/js/vendor/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/responsive.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/select.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/fixed_columns.min.js') }}"></script>
    <script src="{{ asset('assets/demo/pages/datatables_extension_fixed_columns.js') }}"></script>
    <script src="{{ asset('assets/demo/pages/datatables_api.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/buttons.min.js') }}"></script>
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
                        <h5 class="mb-0">Результаты</h5>
                    </div>

                    @if (count($products))
                        <table class="table table-xs table-hover datatable-basic">
                            <thead>
                            <th>Наименование</th>
                            <th>Стоимость</th>
                            <th>Автор</th>
                            <th>Ссылка</th>
                            </thead>
                            <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->author }}</td>
                                    <td><a href="{{ $product->url }}" target="_blank">{{ $product->url }}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-body text-center p-3 text-muted">Результатов пока нет.</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('footer-js')
<script>
    //
    // Datatable.
    //
    const DatatableBasic = function() {

        const _componentDatatableBasic = function() {
            if (!$().DataTable) {
                console.warn('Warning - datatables.min.js is not loaded.');
                return;
            }

            // Setting datatable defaults
            $.extend( $.fn.dataTable.defaults, {
                scrollX: true,
                responsive: false,
                autoWidth: true,
                displayLength: 50,
                order: [[ 0, 'desc' ]],
                dom: '<"datatable-header justify-content-start"f<"ms-sm-auto"l><"ms-sm-3"B>><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span class="me-3">Поиск:</span> <div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
                    searchPlaceholder: 'Что ищем?',
                    lengthMenu: '<span class="me-3">Показывать:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': document.dir == "rtl" ? '&larr;' : '&rarr;', 'previous': document.dir == "rtl" ? '&rarr;' : '&larr;' },
                    processing: "Загрузка данных...",
                    zeroRecords: "Ничего не найдено",
                    emptyTable: "Нет шаблонов."
                },
                buttons: {
                    dom: {
                        button: {
                            className: 'btn btn-light'
                        }
                    },
                    buttons: [
                        {extend: 'copy'},
                        {extend: 'csv'},
                        {extend: 'excel'},
                        {extend: 'pdf'},
                        {extend: 'print'}
                    ]
                },
                columnDefs: [
                    { width: '5%', targets: 0 },
                    { width: '25%', targets: 0 }
                ]
            });

            // Basic datatable
            $('.datatable-basic').DataTable();
        };

        return {
            init: function() {
                _componentDatatableBasic();
            }
        }
    }();

    // Initialize module
    // ------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        DatatableBasic.init();
    });
</script>
@endsection
