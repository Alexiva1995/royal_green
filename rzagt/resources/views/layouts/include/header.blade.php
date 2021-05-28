<style>
  .text-small3 {
    font-size: 1.74em !important;
  }

  @media screen and (max-width: 600px) {
    .text-small3 {
      font-size: 1.3em !important;
    }
  }

  @media screen and (max-width: 400px) {
    .text-small3 {
      font-size: 0.8em !important;
    }
  }

</style>

<!-- BEGIN: Header-->
<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow mt-0">
  <div class="navbar-wrapper">
    <div class="navbar-container content">
      <div class="navbar-collapse" id="navbar-mobile">
        <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
          <ul class="nav navbar-nav">
            <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                href="#"><i class="ficon feather icon-menu"></i></a></li>
          </ul>
          <ul class="nav navbar-nav bookmark-icons">
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link text-white" href="javascript():;">
                Saldo Disponible <strong>Total: ${{number_format(Auth::user()->wallet_amount, 2, ',', '.')}}</strong>
              </a>
            </li>
            <li class="nav-item d-none d-lg-block mr-1">
              <a class="nav-link btn btn-primary btn-inline" href="{{route('wallet')}}">
                Retirar
              </a>
            </li>
            <li class="nav-item d-none d-lg-block mr-1">
              <a class="nav-link btn btn-outline-primary btn-inline" href="{{route('tienda-index')}}">
                Invertir
              </a>
            </li>
            {{-- <li class="nav-item d-none d-lg-block">
              <a class="nav-link" href="app-calender.html" data-toggle="tooltip" data-placement="top" title="Calendar">
                <i>Ult. vez 14/14/20 16:45</i>
              </a>
            </li> --}}
          </ul>
        </div>
        <ul class="nav navbar-nav float-right">
          {{-- Notificaciones --}}
          {{-- @include('layouts.include.notifications') --}}
          {{-- Fin Notificaciones --}}
          <li class="dropdown dropdown-user nav-item">
            <a class="dropdown-toggle nav-link dropdown-user-link btn btn-outline-primary btn-inline"
              href="{{ route('logout') }}" data-toggle="dropdown"
              onclick="event.preventDefault();document.getElementById('logout-form').submit();">
              <div class="user-nav ">
                <span class="user-status">
                  <i class="feather icon-log-out"></i> 
                  Logout 
                  <span class="user-name text-bold-600">
                    {{Auth::user()->display_name}}
                  </span>
                </span>
              </div>
            </a>
          </li>
{{-- 
          <li class="dropdown dropdown-language nav-item">
            <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true"
              aria-expanded="false">
              <i class="flag-icon flag-icon-us"></i>
              <span class="selected-language">English</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdown-flag">
              <a class="dropdown-item" href="#" data-language="en">
                <i class="flag-icon flag-icon-us"></i> 
              </a>
              <a class="dropdown-item" href="#" data-language="fr">
                <i class="flag-icon flag-icon-fr"></i> 
              </a>
              <a class="dropdown-item" href="#" data-language="de">
                <i class="flag-icon flag-icon-de"></i> 
              </a>
              <a class="dropdown-item" href="#" data-language="pt">
                <i class="flag-icon flag-icon-pt"></i> 
              </a>
            </div>
          </li> --}}
        </ul>
      </div>
    </div>
  </div>
</nav>
<!-- END: Header-->