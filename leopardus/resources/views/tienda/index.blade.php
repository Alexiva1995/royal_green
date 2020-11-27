@extends('layouts.dashboard')

@section('content')

<style type="text/css">
    
.card-redonda{
box-shadow: 0px 0px 50px #00000040 !important;
border-radius: 20px;
opacity: 1;
background: #11262C;
}

.margen{
 margin-left: 28px;
 margin-top: 18px;

}

.margen2{

        margin-top: 10px;
    margin-bottom: 10px;
    margin-left: 10px;
}
.img-rb{
    
    width: 60px;   
}

.degradado{

 background: transparent linear-gradient(90deg, #00E4FE 0%, #0CEADF 35%, #23F7AA 100%) 0% 0% no-repeat padding-box;
opacity: 1;
}

.degradado2{

background: transparent linear-gradient(90deg, #00E4FE 100%, #0CEADF 35%, #23F7AA 0%) 0% 0% no-repeat padding-box;
opacity: 0.55;
}

strong{

    font-size: 30px;
}
</style>


{{-- alertas --}}
@include('dashboard.componentView.alert')


<div class="card bg-dark">
    
    <div class="card-header">
        <h1 class="white">R E S U M E N</h1>
            </div>

    
    <div class="card-content">
        <div class="card-body">
            <div class="row">
           
           {{-- begin PAQUETES DE INVERSION--}} 
            <div class="col-6">
                <div class="card bg-transparent">
                    <h1 class="card-title white float-left">PAQUETES DE INVERSION</h1>
                   <div class="card-content">
                
                <div class="card card-redonda ">

                    <div class="row">
                        <div class="row">
                            <div class="col-4">
                              <strong class="font-weight-bolder turquesa" > RG -50.000 </strong>
                            </div>
                            <div class="col-4">
                                <strong class="font-weight-bolder turquesa" > RG -50.000 </strong>
                            </div>
                            <div class="col-4">
                              <strong class="font-weight-bolder turquesa" > RG -50.000 </strong>
                            </div>
                          </div>
                    </div>
                </div>
                        
                        <br>
                        
                        <div class="card card-redonda ">
                            <div class="card-title margen"> 
                                <img src="{{asset('assets/imgLanding/logo-mini.png')}}" class="img-rb" style="vertical-align: sub;">            
                                <strong class="white font-weight-bolder" style="font-size:30px"> -50.000 </strong> <br>

                                <p style="color: white;">  Ganancia actual: $70.000</p>

                                 <div class="row">
                                    <div class="col-md-10">
                                {{--Barra de progreso--}}
                                <div class="progress progress-bar-primary progress-xl degradado2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated degradado margen2" role="progressbar" style="width:54%;">
                                    </div>
                                </div>
                                {{--END Barra de progreso--}}
                                      </div>
                                    <div class="col-md-2">
                                    <strong class="white font-weight-bolder " style="font-size:30px"> 54% </strong> 

                                    </div>
                                  </div>

                                 



                               
                        
                        </div>
                            
                                <p style="color: #999999;"> Activo: 22-10-2001</p> 
                                
                            
                            
                        </div>

                        <div class="card bg-transparent">
                            <h1 class="card-title white float-left">PAQUETES DE INVERSION</h1>
                        </div>
                    
                    <div class="card card-redonda ">

                        <div class="row" style="font-size: 25px;">
                            <div class="col-3">
                              <p>0.0055555 BTC</p>
                            </div>
                            <div class="col-3">
                                <p>0.0055555 BTC</p>
                            </div>
                            <div class="col-3">
                              <p>0.0055555 BTC</p>
                            </div>
                            <div class="col-3">
                              <p>0.0055555 BTC</p>
                            </div>
                          </div>
                </div>


                    </div>
                    </div>
            
            </div>
        {{-- END PAQUETES DE INVERSION--}} 



         {{-- BEGIN NEGOCIO--}} 
            <div class="col-6 ">
                <div class="card bg-transparent">
                    <div class="card-content">
                        <h1 class="card-title white">NEGOCIO</h1>   
                        <div class="card card-redonda">
                            <div class="card-title "> 
                                <div class="float-center text-center">
                          <img src="{{asset('assets/imgLanding/esmeralda.png')}}" class="img-rb text-center" style="width: 500px">      
                                </div>
                                           
                                <div class="card bg-transparent">
                                <h3 class="white float-left  font-weight-bolder text-center" style="font-size: 70px;">ESMERALDA</h3>


                                <div class="row margen">
                                    <div class="col-md-10">
                                {{--Barra de progreso--}}
                                <div class="progress progress-bar-primary progress-xl degradado2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated degradado margen2" role="progressbar" style="width:54%;">
                                    </div>
                                </div>
                                {{--END Barra de progreso--}}

                                      </div>
                                    <div class="col-md-2">
                                    <strong class="white font-weight-bolder " style="font-size:30px"> 54% </strong> 

                                    </div>
                                    <p class="margen"> Proximo rango</p>
                                  </div>

<div class="card-content bg-transparent  margen">
    <h1 class="card-title white">REFERIDOS</h1>



</div>



                            
                        </div>


                            </div>
                            
                        </div>
                        </div>
                    </div>
                </div>
        {{-- END NEGOCIO--}} 

            </div>
        </div>
        </div>

</div>

{{-- modales --}}
@include('tienda.modalCompra')
{{-- @include('tienda.modalCupon') --}}

<script>
    function detalles(product, id, code) {
        $('#idproducto').val(product.ID)
        $('#img').attr('src',product.imagen)
        $('#title').html(product.post_title)
        $('#title2').val(product.post_title)
        $('#content').html(product.post_content)
        $('#price').html('$ '+product.meta_value)
        $('#price2').val(product.meta_value)
        $('#pagarcompra').click()
        // $('#myModal1').modal('show')
    }
</script>
@endsection