<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header" style="background-color: #00646d;">
        <ul class="nav navbar-nav flex-row">
            {{-- <li class="nav-item mr-auto">
                <a class="navbar-brand" href="">
                    <div class="brand-logo"
                        style="background: url('{{asset('assets/imgLanding/ethc_pagina_principal-12.svg')}}') no-repeat;">
                    </div>
                    <h2 class="brand-text mb-0">{{$settings->name}}</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
                        data-ticon="icon-disc"></i>
                </a>
            </li> --}}
            <a class="navbar-brand" href="" href="" style="width: 100%;margin: 0px;">
                <div class="brand-logo2" style="width: 100%;">
                    <img src="{{asset('assets/imgLanding/logo2.png')}}" style="width: 100%;">
                </div>
            </a>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item d-flex justify-content-center">

                <div>

                    <div id="diseng" class="color-example"

                        style="background: url('{{ asset('avatar/'.Auth::user()->avatar) }}')">

                    </div>

                    <h5 class="text-center">Hola {{Auth::user()->user_nicename}}</h5>

                    <h6 class="text-center">{{Auth::user()->user_email}}</h6>

                </div>

            </li>
            {{-- INICIO --}}
            <li class="nav-item">
                <a href="{{url('mioficina/admin')}}" class="nav-link nav-toggle">
                    <i class="feather icon-home"></i>
                    <span class="title">Balance General</span>
                </a>
            </li>
            {{-- RANKING --}}
            <li class="nav-item">
                <a href="{{url('mioficina/tienda')}}" class="nav-link nav-toggle">
                    <i class="feather icon-shopping-cart"></i>
                    <span class="title">E-commerce</span>
                </a>
            </li>
            {{--FIN RANKING --}}
            {{-- TRANSACCIONES --}}
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="feather icon-activity"></i>
                    <span class="title">Movimientos</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/transactions/networkorders')}}" class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Ordenes de Red</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/transactions/personalorders')}}" class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Ordenes Personales</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/wallet/cobros')}}" class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Retiros</span>
                        </a>
                    </li>
                </ul>
            </li>
            {{--FIN TRANSACCIONES --}}

            {{-- GEONOLOGIA --}}
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="feather icon-users"></i>
                    <span class="title">Red de Usuarios</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID}}"
                            class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Nuevo Usuario</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{url('mioficina/referraltree')}}?" class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Árbol de Usuarios</span>
                        </a>
                    </li> --}}
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/network/directrecords')}}" class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Registros Directos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/network/networkrecords')}}" class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Registros en Red</span>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- FIN GENEALOGIA --}}

            {{-- <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="feather icon-user-plus"></i>
                    <span class="title">Registrar</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID.'&lado=D'}}"
            class="nav-link">
            <span class="title">Nuevo Usuario Derecha</span>
            </a>
            </li>
            <li class="nav-item">
                <a href="{{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID.'&lado=I'}}"
                    class="nav-link">
                    <span class="title">Nuevo Usuario Izquierda</span>
                </a>
            </li>
        </ul>
        </li> --}}
        {{-- FIN INICIO --}}

        {{--INICIO BILLETERA --}}
        <li class="nav-item">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="feather icon-trending-up"></i>
                <span class="title">Billetera</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item">
                    <a href="{{url('mioficina/admin/wallet/')}}" class="nav-link">
                        <i class="feather icon-circle"></i>
                        <span class="title">Retiros</span>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{url('mioficina/admin/wallet/puntos')}}" class="nav-link">
                        <i class="feather icon-circle"></i>
                        <span class="title">Mi Billetera Puntos</span>
                    </a>
                </li> --}}
            </ul>
        </li>
        {{-- FIN BILLETERA --}}

        {{-- INFORMES --}}
        <li>
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="feather icon-file-text"></i>
                <span class="title">Informes</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item">
                    <a href="{{url('mioficina/admin/info/activacion')}}" class="nav-link">
                        <i class="feather icon-circle"></i>
                        <span class="title">Activacion</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{url('mioficina/admin/info/comisiones')}}" class="nav-link">
                        <i class="feather icon-circle"></i>
                        <span class="title">Comisiones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{url('mioficina/admin/info/liquidacion')}}" class="nav-link">
                        <i class="feather icon-circle"></i>
                        <span class="title">Liquidaciones</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- FIN GESTION DE PERFILES --}}
        {{-- INICIO TICKETS --}}
        <li class="nav-item">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="feather icon-message-square"></i>
                <span class="title">Soporte</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item">
                    <a href="{{url('mioficina/admin/ticket/ticket')}}" class="nav-link">
                        <i class="feather icon-circle"></i>
                        <span class="title">Generar Tickets</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{url('mioficina/admin/ticket/misticket')}}" class="nav-link">
                        <i class="feather icon-circle"></i>
                        <span class="title">Mis Tickets</span>
                    </a>
                </li>
            </ul>
        </li>
        {{-- FIN TICKETS --}}

        {{-- RANKING --}}
        {{-- <li class="nav-item">
                <a href="{{url('mioficina/admin/ranking')}}" class="nav-link nav-toggle">
        <i class="feather icon-award"></i>
        <span class="title">Ranking</span>
        </a>
        </li> --}}
        {{--FIN RANKING --}}

        {{-- CERRAR SESIÓN --}}
        <li class="nav-item">
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link">
                <i class="feather icon-log-out"></i>
                <span class="title">Cerrar Sesión</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
        {{-- FIN CERRAR SESIÓN --}}
        </ul>
    </div>
</div>