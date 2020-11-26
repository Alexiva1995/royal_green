
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow bg-transparent" data-scroll-to-active="true">
   
<div class="navbar-header" >
    <a class="navbar-brand" href="" href="">
        <div class="brand-logo2" >
            <img src="{{asset('assets/imgLanding/logo-transparente.png')}}" style="width: 220px;">
        </div>
    </a>
</div>

 ​
<div class="main-menu-content">

    <div class=" img-circle text-center">
<img src="{{asset('assets/imgLanding/foto.png')}}" class="rounded-circle"" alt="..." style="width: 150px; height: 150px; margin-bottom: 30px;">
<h2 class="brand-text mb-0 white">{{ Auth::user()->display_name }}</h2>
<p style="color: #999999">{{ Auth::user()->user_email}}</p>
</div>
    

    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
        {{-- INICIO --}}
        <li class="nav-item">
            <a href="{{url('mioficina/admin')}}" class="nav-link nav-toggle">
                <span class="title">Resumen</span>
            </a>
        </li>
        {{-- RANKING --}}
        <li class="nav-item">
            <a href="{{url('mioficina/tienda')}}" class="nav-link nav-toggle">
                <span class="title">Paquetes  de inversion</span>
            </a>
        </li>
        {{--FIN RANKING --}}

        {{-- TRANSACCIONES --}}
        <li class="nav-item">
            <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title"> Historial de transaciones</span>
                <span class="arrow"></span>
            </a>
            
        </li>
        {{--FIN TRANSACCIONES --}}

        {{-- GEONOLOGIA --}}
        <li class="nav-item">
            <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Historial de referidos</span>
                <span class="arrow"></span>
            </a>
            
        </li>
        {{-- FIN GENEALOGIA --}}

        {{-- TRANSACCIONES --}}
        <li class="nav-item">
            <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Link referidos</span>
                <span class="arrow"></span>
            </a>
            
        </li>
        {{--FIN TRANSACCIONES --}}

        {{--INICIO BILLETERA --}}
        <li class="nav-item">
            <a href="{{url('mioficina/admin/wallet/')}}" class="nav-link nav-toggle">
                <span class="title"> Perfil </span>
            </a>
        </li>
        {{-- FIN BILLETERA --}}

        {{-- CERRAR SESIÓN --}}
        <li class="nav-item" style="margin-bottom: 30px;">
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link">
                <span class="title">Soporte</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
        {{-- FIN CERRAR SESIÓN --}}
    </ul>
</div>
</div>