<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item m-auto">
                {{-- <a class="navbar-brand" href="../../../html/ltr/vertical-menu-template/index.html">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">Vuexy</h2>
                </a> --}}

                <a class="navbar-brand modern-nav-toggle m-0" href="" href="" data-toggle="collapse">
                    <div class="brand-logo2">
                        <img src="{{asset('assets/imgLanding/logo3.png')}}" style="width: 100%;" height="80">
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="main-menu-content">
        <div class=" img-circle text-center">
            <img src="https://royalgreen.company/mioficina/avatar/avatar.png" class="rounded-circle" alt=" ..."
                style="width: 150px; height: 150px; margin-bottom: 30px;">
        </div>
        <h2 class="brand-text mb-0 white text-center">{{ Auth::user()->display_name }}</h2>
        <p style="color: #999999" class="text-center">{{ Auth::user()->user_email}}</p>

        <div class="row d-lg-none">
            <div class="col-12 text-center text-white">
                <p>
                    Saldo Disponible <strong>Total:
                        ${{number_format(Auth::user()->wallet_amount, 2, ',', '.')}}</strong>
                </p>
            </div>
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-6 text center">
                        <a class="btn btn-primary btn-inline" href="{{route('wallet-index')}}">
                            Retirar
                        </a>
                    </div>
                    <div class="col-6 text center">
                        <a class="btn btn-outline-primary btn-inline" href="{{route('tienda-index')}}">
                            Invertir
                        </a>
                    </div>
                </div>
            </div>
            {{-- <div class="col-12 text-center mb-3">
                <i>Ult. vez 14/14/20 16:45</i>
            </div> --}}
        </div>

        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item">
                <a href="{{route('index')}}">
                    <span class="menu-title" data-i18n="Resumen">
                        Resumen
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('tienda-index')}}">
                    <span class="menu-title" data-i18n="Paquete de Inversion">
                        Paquete de Inversion
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('tienda-solicitudes')}}">
                    <span class="menu-title">Activacion Manual</span>
                </a>
            </li>
            <li class=" nav-item">
                <a href="javascripts:;">
                    <span class="menu-title" data-i18n="Link de Referidos">
                        Contabilidad
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item">
                        <a href="{{route('networkorders')}}">
                            <span class="menu-title" data-i18n="Historial de Ordenes">
                                Ordenes
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('wallet')}}">
                            <span class="menu-title" data-i18n="Historial de Ordenes">
                                Comisiones
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('rentabilidad.index')}}">
                            <span class="menu-title" data-i18n="Historial de Ordenes">
                                Rentabilidad
                            </span>
                        </a>
                    </li>
                    <li class=" nav-item">
                        <a href="{{route('networkrecords')}}">
                            <span class="menu-title" data-i18n="Historial de Referidos">
                                Referidos
                            </span>
                        </a>
                    </li>
                    <li class=" nav-item">
                        <a href="{{route('wallet.binario')}}">
                            <span class="menu-title" data-i18n="Historial de Referidos">
                                Historial de Puntos Binarios
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item">
                <a href="javascripts:;">
                    <span class="menu-title" data-i18n="Link de Referidos">
                        Pago
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item">
                        <a href="{{route('price-confirmar')}}">
                            <span class="menu-title" data-i18n="Historial de Ordenes">
                                Confirmar
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('price-historial')}}">
                            <span class="menu-title" data-i18n="Historial de Ordenes">
                                Historial
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item">
                <a href="javascripts:;">
                    <span class="menu-title" data-i18n="Link de Referidos">
                        Arboles
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item">
                        <a href="{{route('referraltree', 'tree')}}">
                            <span class="menu-title" data-i18n="Historial de Ordenes">
                                Unilevel
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('referraltree', 'matriz')}}">
                            <span class="menu-title" data-i18n="Historial de Ordenes">
                                Binario
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.userrecords')}}">
                    <span class="menu-title">Lista de Usuarios</span>
                </a>
            </li>
            {{-- <li class=" nav-item">
                <a href="{{route('wallet')}}">
                    <span class="menu-title" data-i18n="Billetera">
                        Billetera
                    </span>
                </a>
            </li> --}}
            <li class=" nav-item">
                <a href="javascripts:;">
                    <span class="menu-title" data-i18n="Link de Referidos">
                        Links de Referidos
                    </span>
                </a>
                <ul class="menu-content">
                    <li onclick="updateSideBinary('I')">
                        <a>
                            <span class="menu-item" data-i18n="Lado Izquierdo">Lado Izquierdo</span>
                        </a>
                    </li>
                    <li onclick="updateSideBinary('D')">
                        <a>
                            <span class="menu-item" data-i18n="Lado Derecho">Lado Derecho</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item">
                <a href="javascript:;" onclick="$('#modalRentabilidad').modal('show')">
                    <span class="menu-title" data-i18n="Link de Referidos">
                        Pagar Rentabilidad
                    </span>
                </a>
            </li>
            <li class=" nav-item">
                <a href="{{route('admin.user.edit')}}">
                    <span class="menu-title" data-i18n="Perfil">
                        Perfil
                    </span>
                </a>
            </li>
            <li class=" nav-item">
                <a href="{{route('todosticket')}}">
                    <span class="menu-title" data-i18n="Soporte">
                        Soporte
                    </span>
                </a>
            </li>
        </ul>
        <div class="text-center mt-2">
            <a class="btn btn-primary btn-inline" href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="feather icon-log-out"></i>
                Logout
            </a>
        </div>
    </div>
</div>