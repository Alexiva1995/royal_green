@extends('layouts.dashboard')

@section('content')
<div id="logs-list">
    <div class="col-12">

        <div class="row match-height">
            <div class="col-md-4 col-12 mt-2">
                <div class="card btn-secondary text-white text-center mx-2">
                    <p class="card-title  my-2">Ganancia Total</p>
                    <span id="gananciatotal" class="font-large-2 font-weight-bolder">{{number_format($ingreso - $comision,2,".",",")}}</span>
                </div>
            </div>

            <div class="col-md-4 col-12 mt-2">
                <div class="card btn-secondary text-white text-center mx-2">
                    <p class="card-title my-2">Ingreso</p>
                    <span id="comision" class="font-large-1 font-weight-bold">{{number_format($ingreso,2,".",",")}}</span>
                </div>
            </div>

            <div class="col-md-4 col-12 mt-2">
                <div class="card btn-secondary text-white text-center mx-2">
                    <p class="card-title my-2">Comisión</p>
                    <span id="ingresos" class="font-large-1 font-weight-bold">{{number_format($comision, 2, ".",",")}}</span>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-12 mx-auto card-content my-1">
                <div class="card-title text-center">
                    <h3 class="text-white">Seleccione el rango de fechas que quiere consultar</h3>
                </div>

                <div class="input-group input-group-lg">
                    <input type="text" id="fechaDatos" class="px-3 py-1 bg-dark rounded text-center flatpickr-basic flatpickr-input active border border-white mx-auto" placeholder="Seleccionar fechas">
                </div>
                {{-- <div class="card-body card-dashboard">
                    <div class="row justify-content-center">
                        <div class="px-2 align-items-center">
                            <input type="text" id="fechaDatos" class="form-control px-3 flatpickr-basic flatpickr-input active " placeholder="Seleccione la fecha final" readonly="readonly">
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    

        <div class="card bg-lp d-none">
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                      <h1 class="text-white">Beneficio Royal</h1>
                        <table class="table nowrap scroll-horizontal-vertical myTable table-striped">
                            <thead class="">

                                <tr class="text-center text-white bg-purple-alt2">                                
                                    <th>ID</th>
                                    <th>Tipo de Transacción</th>
                                    <th>Correo</th>
                                    <th>Monto</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($beneficios as $orden)
                                    <tr class="text-center text-white">
                                        <td>{{$orden->id}}</td>
                                        <td>
                                        @if($orden->tipo_transaction == 0)
                                            <strong>Comisión</strong>
                                            @else
                                            <strong>Retiro</strong>
                                        @endif
                                        </td>
                                        <td>{{$orden->getWalletUser->email}}</td>
                                        <td>{{$orden->monto}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('page_css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
@endpush

@push('page_js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function () {
        let gananciatotal = document.querySelector("#gananciatotal");
        let ingresos = document.querySelector("#ingresos");
        let comision = document.querySelector("#comision");
        flatpickr("#fechaDatos", {
            mode: "range",
            onClose: function(selectedDates, dateStr, instance){
                let fecha = dateStr;
                if(fecha.length >10){
                    from = fecha.substr(0,10);
                    to = fecha.substr(14);
                }else{
                    from = fecha;
                    to = fecha;
                }
                let url = `rangofecha/${from}/${to}`;
                let token = window.csrf_token;
                fetch(url, {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                        },
                    method: 'get',
                })
                .then( response => response.text() )
                .then( resultText => (
                    data = JSON.parse(resultText),
                    gananciatotal.innerHTML = number_format(data[0]-data[1],2,".",","),
                    ingresos.innerHTML = number_format( data[0],2,".",","),
                    comision.innerHTML = number_format( data[1],2,".",",")

                ))
                .catch(function(error) {
                    console.log(error);
                });
            }
        });

    });

    function number_format(number, decimals, dec_point, thousands_sep) {
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

</script>
@endpush


{{-- permite llamar a las opciones de las tablas --}}
@include('layouts.componenteDashboard.optionDatatable')


