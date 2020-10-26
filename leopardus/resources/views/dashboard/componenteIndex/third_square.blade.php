@php

    use Carbon\Carbon;

    $ano = Carbon::now()->format('Y')

@endphp

<div class="col-12">

    {{-- linea 1 --}}

    <div class="row">

        {{-- grafica de comisiones e ingresos --}}

        <div class="col-12 col-md-12 mt-2">

            <div class="card h-100">

                <div class="card-header">

                    <h4 class="card-title">Comisión - {{$ano}}</h4>

                </div>

                <div class="card-content">

                    <div class="card-body">

                        <div id="ingresocomision">

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- {{-- grafica de pagos --}}

        <div class="col-12 col-md-4 mt-2">

            <div class="card h-100">

                <div class="card-header">

                    <h4 class="card-title">Resumen de Pagos</h4>

                </div>

                <div class="card-content">

                    <div class="card-body">

                        <div id="pagos">

                        </div>

                    </div>

                </div>

            </div>

        </div>-->

    </div>

    {{-- linea 2 --}}

    <div class="row">

        {{-- grafica de usuarios --}}

        <div class="col-12 col-md-8 mt-2">

            <div class="card h-100">

                <div class="card-header">

                    <h4 class="card-title">Crecimiento Anual - {{$ano}}</h4>

                </div>

                <div class="card-content">

                    <div class="card-body">

                        <div id="usuarios">

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- listado de nuevos miembros --}}

        <div class="col-12 col-md-4 mt-2">

            <div class="card h-100">

                <div class="card-header">

                    <h4 class="card-title">Nuevos Miembros</h4>

                </div>

                <div class="card-content">

                    <div class="card-body">

                        @foreach ($new_member as $member)

                        <div class="d-flex justify-content-start align-items-center mb-1">

                            <div class="avatar mr-50">

                                <img src="{{asset('avatar/'.$member['avatar'])}}" alt="avtar img holder" height="35" width="35">

                            </div>

                            <div class="user-page-info">

                                <h6 class="mb-0">{{$member['nombre']}}</h6>

                                <span class="font-small-2">{{date('d-m-Y', strtotime($member['fecha']))}}</span>

                            </div>

                            {{-- <button type="button" class="btn btn-primary btn-icon ml-auto"><i class="feather icon-user-plus"></i></button> --}}

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>