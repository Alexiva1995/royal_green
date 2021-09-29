{{-- para los css --}}
@push('page_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/js/librerias/datatables/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/app-assets/css/pages/custom-datatable.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/app-assets/vendors/css/forms/select/select2.css') }}">
    <style>
        .select2-search {
            background-color: #173138;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #66ffcccd;
        }

        .select2-search input {
            background-color: #66ffcc;
        }

        .select2-results {
            background-color: #173138;
        }

        .select2-container--default .select2-selection--single {
            background-color: #173138;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white;
        }

    </style>
@endpush

{{-- para los js --}}
@push('page_vendor_js')
    <script type="text/javascript" src="{{ asset('assets/js/librerias/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/app-assets/vendors/js/forms/select/select2.full.js') }}" defer></script>
    {{-- <script src="{{ asset('assets/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script> --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
        document.addEventListener('DOMContentLoaded', function(){
            $('#puntosId').select2();
            let table = $('#puntos-datatable').DataTable({
                processing: true,
                responsive: true,
                bDestroy: true,
                order: [
                    [0, "desc"]
                ],
                searching: true,
                bLengthChange: true,
                pageLength: 5,
                language: {
                    paginate: {
                        next: ">",
                        previous: "<"
                    },
                },
                drawCallback: function(settings) {
                    $('tbody tr').addClass("text-center text-white pl-2");
                    $('ul.pagination li.paginate_button.page-item.active').addClass(
                        "custom-pagination-li-active");
                    $('ul.pagination li.paginate_button.page-item.active a').addClass(
                        "custom-pagination-li-active-a");
                    $('ul.pagination li a').addClass("custom-pagination-li-a2");
                    $('ul.pagination li.previous, ul.pagination li.next').addClass(
                        "custom-pagination-li");
                    $('ul.pagination li.previous a, ul.pagination li.next a').addClass(
                        "custom-pagination-li-a");
                }
            });            

            $('#puntosId').on("change", function() {
                if ($('#puntosId').val().length > 0) {
                    $('#puntos-datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        bDestroy: true,
                        ajax: '{{route('audit.data.puntos')}}?id=' + $('#puntosId').val(),                        
                        columns: [{
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'referido',
                                name: 'referido'
                            },
                            {
                                data: 'puntos_derecha',
                                name: 'puntos_derecha'
                            },
                            {
                                data: 'puntos_izquierda',
                                name: 'puntos_izquierda'
                            },
                            {
                                data: 'lado',
                                name: 'lado'
                            },
                            {
                                data: 'estado',
                                name: 'estado'
                            }, 
                            {
                                data: 'id',
                                name: 'action',
                                class: 'clases_para_el_td',
                                render: function(data, type,
                                    row) { // con row obtienes la información por fila
                                    return '<button data-eliminar="' + data +
                                        '" class="btn btn-info rounded text-white eliminarComision" title="Cancelar Comision"><i class="fa fa-trash"></i></button>';
                                }
                            }
                        ],
                        responsive: true,
                        order: [
                            [0, "desc"]
                        ],
                        searching: true,
                        bLengthChange: true,
                        pageLength: 5,
                        language: {
                            paginate: {
                                next: ">",
                                previous: "<"
                            },
                        },
                        drawCallback: function(settings) {
                            $('tbody tr').addClass("text-center text-white pl-2");
                            $('ul.pagination li.paginate_button.page-item.active').addClass(
                                "custom-pagination-li-active");
                            $('ul.pagination li.paginate_button.page-item.active a').addClass(
                                "custom-pagination-li-active-a");
                            $('ul.pagination li a').addClass("custom-pagination-li-a2");
                            $('ul.pagination li.previous, ul.pagination li.next').addClass(
                                "custom-pagination-li");
                            $('ul.pagination li.previous a, ul.pagination li.next a').addClass(
                                "custom-pagination-li-a");
                        }
                    });
                }
            });

    });
</script>