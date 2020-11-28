
<style>
  .text-small3{
    font-size: 1.74em !important;
  }

  @media screen and (max-width: 600px){
    .text-small3{
      font-size: 1.3em !important;
    }
  }

  @media screen and (max-width: 400px){
    .text-small3{
      font-size: 0.8em !important;
    }
  }
  
</style>

<!-- BEGIN: Header-->
<nav class=" page-head header-navbar navbar-expand-lg navbar floating-nav navbar-with-menu navbar-light navbar-shadow bg-dark">
  <div class="navbar-wrapper">
            <div class=" page-head brand-logo2 " >

            <img src="{{asset('assets/imgLanding/logo3.png')}}" style="width: 190px; margin-right: 300px;" >
            
            <i class="white" style="margin-right: 20px;">Saldo disponible</i> 
            
            <strong class="white"> TOTAL: $1500</strong>
            
            <a class="btn btn-primary btn-inline m-2"> Retirar </a>

             <a class="btn btn-outline-primary btn-inline m-2"> Invertir </a>

                {{-- BEGIN Boton Logout --}}
           
               <a class="btn btn-outline-primary btn-inline m-auto" href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="feather icon-log-out"></i> Logout {{ Auth::user()->display_name }}
              </a>
            {{-- END Boton Logout --}}

            </div>
            
         </div> 

</nav>
       
<!-- END: Header-->