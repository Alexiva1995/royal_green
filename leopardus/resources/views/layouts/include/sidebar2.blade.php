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
            <img src="https://royalgreen.company/avatar/avatar.png" class="rounded-circle" alt=" ..."
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
                <a href="{{route('personalorders')}}">
                    <span class="menu-title" data-i18n="Historial de Transaciones">
                        Historial de Transaciones
                    </span>
                </a>
            </li>
            <li class=" nav-item">
                <a href="{{route('networkrecords')}}">
                    <span class="menu-title" data-i18n="Historial de Referidos">
                        Historial de Referidos
                    </span>
                </a>
            </li>
            <li class=" nav-item">
                <a href="{{route('referraltree', 'tree')}}">
                    <span class="menu-title" data-i18n="Arbol">
                        Arbol Unilevel
                    </span>
                </a>
            </li>
            <li class=" nav-item">
                <a href="{{route('referraltree', 'matriz')}}">
                    <span class="menu-title" data-i18n="Arbol">
                        Arbol Binario
                    </span>
                </a>
            </li>
            <li class=" nav-item">
                <a href="{{route('wallet')}}">
                    <span class="menu-title" data-i18n="Billetera">
                        Billetera
                    </span>
                </a>
            <li class=" nav-item">
                <a href="javascript:;" onclick="copyToClipboard('copy')">
                    <span class="menu-title" data-i18n="Link de Referidos">
                        Link de Referidos
                    </span>
                </a>
            </li>
            </li>
            <li class=" nav-item">
                <a href="{{route('admin.user.edit')}}">
                    <span class="menu-title" data-i18n="Perfil">
                        Perfil
                    </span>
                </a>
            </li>
            <li class=" nav-item">
                <a href="{{route('ticket')}}">
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