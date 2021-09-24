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
            $('#comisionesId').select2();
            let table = $('#comisiones-datatable').DataTable({
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
            let url = 'api/data-comisiones/';
            

            $('#comisionesId').on("change", function() {
                if ($('#comisionesId').val().length > 0) {
                    $('#comisiones-datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        bDestroy: true,
                        ajax: window.url_asset + url + $('#comisionesId').val(),
                        columns: [{
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'email',
                                name: 'email'
                            },
                            {
                                data: 'descripcion',
                                name: 'descripcion'
                            },
                            {
                                data: 'monto',
                                name: 'monto'
                            },
                            {
                                data: 'creacion',
                                name: 'creacion'
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
        
    $(document).on('click', '.eliminarComision', function() {
            let fila = $(this).parent().parent();
            var productId = $(this).data('eliminar');
            Swal.fire({
                title: "¿Seguro que deseas eliminar la comisión " + productId + "?",
                text: "Las inversiones del usuario se verán afectadas",
                icon: "warning",
                background: '#173138',
                confirmButtonColor: '#66ffccdb',
                showCloseButton: true,
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: 'Si, Eliminar',
                cancelButtonText: 'Cancelar'
            })
            .then((result) => {  
            /* Read more about isConfirmed, isDenied below */  
                if (result.isConfirmed) {    
                    peticionEliminar(productId, fila)
                }
            });
    });


    function peticionEliminar(productId, fila) {
        let url2 = 'api/eliminar-comision/';
        fetch(window.url_asset + url2 + productId, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": window.csrf_token
                },
                method: 'get',
            })
            .then(function(response) {
                if (response.status !== 200) {
                    Swal.fire({
                        title: 'Ocurrio un error!',
                        text: 'Fallo al eliminar comision, intente luego',
                        icon: 'warning',
                        background: '#173138'
                    })
                    console.log('Looks like there was a problem. Status Code: ' +
                        response.status);
                    return;
                } else {
                    Swal.fire({
                        title: 'Eliminado!',
                        text: 'La Comimsion se eliminó satisfactoriamente',
                        icon: 'success',
                        confirmButtonColor: '#66ffccdb',
                        background: '#173138'
                    })
                    fila.remove();
                }

            })
            .catch(function(error) {
                Swal.fire({
                    title: 'Ocurrio un error!',
                    text: 'Fallo al eliminar comision, intente luego',
                    type: 'error',
                    background: '#173138',
                    confirmButtonClass: 'btn btn-primary text-dark'
                })
                console.log(error);

            });

    
    }
</script>
