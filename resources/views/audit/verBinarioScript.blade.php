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
            $('#binarioId').select2();
            $('#binarioId').on("change", function() {
                let idUser = $('#binarioId').val();
                let idUserCode = btoa(idUser);
                console.log(idUserCode);
                if (idUser.length > 0) {
                   let url = '{{route('genealogy_type_id', ['matriz', 'temp'])}}?audit='+idUserCode;
                    url = url.replace('temp', idUserCode);
                    location.href = url;
                }
            });

    });
</script>
