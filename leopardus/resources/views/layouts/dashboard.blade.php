<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    {{-- <meta name="description"
        content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities."> --}}
    {{-- <meta name="keywords"
        content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app"> --}}
    <meta name="author" content="VALDUSOFT">
    <title>{{$settings->name}}</title>
    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    @include('layouts.include.styles')


</head>


<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click"
    data-menu="vertical-menu-modern" data-col="2-columns">

    @include('layouts.include.header')


    <!-- BEGIN Navigation-->

    @include('layouts.include.sidebar2')
    <!-- END Navigation-->

    <!-- BEGIN Content-->
    <div class="app-content content ">
        <div class="content-wrapper">

            {{-- migaja de pan --}}
            @include('layouts.include.breadcrum')

            <div class="content-body bg-dark">
                @yield('content')
                {{-- Copiar Link --}}
                <p class="d-none" id="copy">
                    {{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID}}
                </p>
                {{-- Salir del sistema --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>

                {{-- modal para la rentabilizacion --}}
                <div class="modal fade" id="modalRentabilidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabelR">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabelR">Rentabilizar</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('wallet.pay.rentabilidad')}}" method="post">
                                    {{csrf_field()}}
                                    <div class="row" style="background:white;">
                                        <div class="form-group col-12">
                                            <label>Porcentaje a rentabilizar</label>
                                            <input class="form-control" type="number" name="porcentage" step="any"
                                                required />
                                            <small class="form-text text-muted">
                                                colocar el monto en valor entero, el sistema lo va procesar, ejemplo si
                                                coloca 5 en el sistema estara 0.05, para el calculo de lo rentibilizado
                                            </small>
                                        </div>
                                        <div class="form-group col-12">
                                            <button type="submit"
                                                class="btn btn-success btn-block retirarbtn">Rentabilizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- END Content-->


</body>
<!-- END: Content-->
<!-- END: Body-->

@include('layouts.include.scripts')

</html>