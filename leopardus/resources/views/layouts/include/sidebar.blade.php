<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">

    <div class="navbar-header" style="background-color: #06171B;">

        <ul class="nav navbar-nav flex-row">

            {{-- <li class="nav-item mr-auto"> --}}

            <a class="navbar-brand" href="" href="" style="width: 100%;margin: 0px; margin-top: 1rem;">
                <div class="brand-logo2" style="width: 100%;">
                    <img src="{{asset('assets/imgLanding/logo2.png')}}" style="width: 100%;">
                </div>
            </a>
        </ul>

    </div>

    <div class="shadow-bottom"></div>

    <div class="main-menu-content">

        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            {{-- <li class="nav-item d-flex justify-content-center">

                <div>

                    <div id="diseng" class="color-example"

                        style="background: url('{{ asset('avatar/'.Auth::user()->avatar) }}')">

                    </div>

                    <h5 class="text-center">Hola {{Auth::user()->user_nicename}}</h5>

                    <h6 class="text-center">{{Auth::user()->user_email}}</h6>

                </div>

            </li> --}}

            {{-- INICIO --}}
            <li class="nav-item">
                <a href="{{url('mioficina/admin')}}" class="nav-link nav-toggle">
                    <span class="title">Estadisticas</span>
                </a>
            </li>
            @if (Auth::user()->ID == 1)
            {{-- INICIO TIENDA INTERNA --}}
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <span class="title">E-commerce</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{url('mioficina/tienda')}}" class="nav-link">
                            <span class="title">Tienda</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('listProduct')}}" class="nav-link">
                            <span class="title">Productos</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{route('tienda-solicitudes')}}" class="nav-link">
                            <span class="title">Solicitudes</span>
                        </a>
                    </li> --}}
                </ul>
            </li>
            {{-- FIN TIENDA INTERNA --}}

            {{-- INICIO ARBOLES --}}

            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="feather "></i>
                    <span class="title">Arboles</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{route('referraltree', 'tree')}}" class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Arbol Unilever</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{route('referraltree', 'matriz')}}" class="nav-link">
                            <i class="feather icon-circle"></i>
                            <span class="title">Arbol Binario</span>
                        </a>
                    </li> --}}
                </ul>
            </li>
            {{-- FIN ARBOLES --}}

            {{-- RED DE USUARIO --}}
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <span class="title">Red de Usuarios</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID}}"
                            class="nav-link">
                            <span class="title">Nuevo Usuario</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/network/directrecords')}}" class="nav-link">
                            <span class="title">Lista de Directos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/network/networkrecords')}}" class="nav-link">
                            <span class="title">Usuarios en Red</span>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- FIN RED DE USUARIO --}}
            @endif

            {{-- TRANSACCIONES --}}
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <span class="title">Ordenes</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/transactions/networkorders')}}" class="nav-link">
                            <span class="title">Ordenes de Red</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/transactions/personalorders')}}" class="nav-link">
                            <span class="title">Ordenes Personales</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('mioficina/admin/price/historial')}}" class="nav-link">
                            <span class="title">Historial de Retiro</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{url('mioficina/admin/price/confirmar')}}" class="nav-link">
                            <span class="title">Confirmar Pagos</span>
                        </a>
                    </li> --}}
                </ul>
            </li>
            {{--FIN TRANSACCIONES --}}

            
        {{--INICIO BILLETERA --}}
        <li class="nav-item">
            <a href="{{url('mioficina/admin/wallet/')}}" class="nav-link nav-toggle">
                <span class="title">Billetera</span>
            </a>
        </li>
        {{-- FIN BILLETERA --}}

            {{-- LISTA DE USUARIOS--}}

            <li>

                <a href="{{url('mioficina/admin/userrecords')}}" class="nav-link nav-toggle">

                    <span class="title">Lista de Usuarios</span>
                </a>

            </li>

            {{-- <li>

                <a href="{{route('admin.userinactive')}}" class="nav-link nav-toggle">

                    <span class="title">Usuarios Inactivos</span>
                </a>

            </li> --}}

            {{-- FIN LISTA DE USUARIOS --}}

            @if (Auth::user()->ID == 1)

            {{-- INFORMES --}}

            <li>

                <a href="javascript:;" class="nav-link nav-toggle">

                   

                    <span class="title">Informes</span>

                    <span class="arrow"></span>

                </a>

                <ul class="sub-menu">

                    <li class="nav-item">

                        <a href="{{url('mioficina/admin/info/perfil')}}" class="nav-link">

                           

                            <span class="title">Perfil</span>

                        </a>

                    </li>

                    <li class="nav-item">

                        <a href="{{url('mioficina/admin/info/ventas')}}" class="nav-link">

                           

                            <span class="title">Ventas</span>

                        </a>

                    </li>

                    <li class="nav-item">

                        <a href="{{url('mioficina/admin/info/rango')}}" class="nav-link">

                            

                            <span class="title">Rangos</span>

                        </a>

                    </li>

                    <li class="nav-item">

                        <a href="{{url('mioficina/admin/info/pagos')}}" class="nav-link">

                            

                            <span class="title">Pagos</span>

                        </a>

                    </li>

                    <li class="nav-item">

                        <a href="{{url('mioficina/admin/info/feed')}}" class="nav-link">

                           

                            <span class="title">Descuentos</span>

                        </a>

                    </li>

                    <li class="nav-item">

                        <a href="{{url('mioficina/admin/info/comisiones')}}" class="nav-link">

                           

                            <span class="title">Comisiones</span>

                        </a>

                    </li>

                </ul>

            </li>

            {{-- FIN GESTION DE PERFILES --}}

            @endif

            {{-- INICIO TICKETS --}}

            {{-- <li class="nav-item">

                <a href="javascript:;" class="nav-link nav-toggle">

                   

                    <span class="title">Soporte</span>

                    <span class="arrow"></span>

                </a>

                <ul class="sub-menu">

                    <li class="nav-item">

                        <a href="{{url('mioficina/admin/ticket/todosticket')}}" class="nav-link">

                           

                            <span class="title">Todos los Tickets</span>

                        </a>

                    </li>

                </ul>

            </li> --}}

            {{-- FIN TICKETS --}}

            @if (Auth::user()->ID == 1)

            {{-- <li class="nav-item">

                <a href="{{ route('admin.user.edit') }}" class="nav-link nav-toggle">

            

            <span class="title">Editar Perfil</span>

            </a>

            </li> --}}

            {{-- LISTA DE USUARIOS--}}

            {{-- <li>

                <a href="{{route('setting-change-porcent')}}" class="nav-link nav-toggle">

                 

                    <span class="title">Configuracion de Porcentajes</span>

                </a>

            </li> --}}

            {{-- FIN LISTA DE USUARIOS --}}

            @endif

            {{-- CERRAR SESIÓN --}}

            <li class="nav-item">

                <a href="{{ route('logout') }}"

                    onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link" style="padding: 10px 15px 10px 10px;">

                    <i class="feather icon-log-out"></i>

                    <span class="title">Cerrar Sesión</span>

                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">

                    {{ csrf_field() }}

                </form>

            </li>

        </ul>

    </div>

</div>