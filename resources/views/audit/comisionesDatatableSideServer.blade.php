{{-- para los css --}}
@push('page_css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/js/librerias/datatables/datatables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/app-assets/css/pages/custom-datatable.css')}}">
@endpush

{{-- para los js --}}
@push('page_vendor_js')
<script type="text/javascript" src="{{asset('assets/js/librerias/datatables/datatables.min.js')}}"></script>
@endpush
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
     $(function() {
        $('#comisiones-datatable').DataTable({
                processing: true,
                serverSide: true,
                data:[],
                responsive: true,
                order: [[ 0, "desc" ]],
                searching: true,
                bLengthChange: true,
                pageLength: 5,
                language: {
                    paginate: {
                        next:				">",
                        previous:			"<"
                    },
                },
                drawCallback: function( settings ) {
                    $('tbody tr').addClass("text-center text-white pl-2");
                    $('ul.pagination li.paginate_button.page-item.active').addClass("custom-pagination-li-active");
                    $('ul.pagination li.paginate_button.page-item.active a').addClass("custom-pagination-li-active-a");
                    $('ul.pagination li a').addClass("custom-pagination-li-a2");
                    $('ul.pagination li.previous, ul.pagination li.next').addClass("custom-pagination-li");
                    $('ul.pagination li.previous a, ul.pagination li.next a').addClass("custom-pagination-li-a");
                }
            });

            let comisionesId = document.querySelector("#comisionesId");
        url = 'api/data-comisiones/';
        comisionesId.addEventListener('change', function(){
            fetch(window.url_asset + url + comisionesId.value, {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": window.csrf_token
                    },
                    method: 'get',
                })
                .then(response => response.text())
                .then(resultText => (
                    data = JSON.parse(resultText),
                    $('#comisiones-datatable').DataTable({
                        data: data,
                    }),
                    console.log(data)
                ))
                .catch(function (error) {
                    console.log(error);
                });
        })

            
         });
    </script>