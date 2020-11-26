
@extends('layouts.dashboard')

@section('content')

<style type="text/css">
    
    .card-redonda{
box-shadow: 0px 0px 50px #00000040 !important;
border-radius: 20px;
opacity: 1;
background: #11262C;
}

.img-rb{
    margin-left: 28px;
    margin-top: 18px;
    width: 60px; 
    
}

</style>
{{-- alertas --}}
@include('dashboard.componentView.alert')


<div class="card bg-dark">
    
    <div class="card-header">
        <h1 class="white">R E S U M E N</h1>
        <h2 class="white">R E S U M E N</h2>
        <h3 class="white">R E S U M E N</h3>
        <h4 class="white">R E S U M E N</h4>


    </div>

    
    <div class="card-content">
        <div class="card-body">
            <div class="row">
           
           {{-- begin PAQUETES DE INVERSION--}} 
            <div class="col-6">
                <div class="card bg-transparent">
                    <h1 class="card-title white float-left">PAQUETES DE INVERSION</h1>
                   <div class="card-content">
                    <span class="font-weight-bolder p-50">Hola a todos</span> 
                        <h1 class="card-title white font-weight-bolder"> RG-50.000</h1>
                        
                        <h1 class="card-title white"> RG-50.000</h1>
                        
                        <div class="card card-redonda ">
                            <div class="card-title"> 
                                <img src="{{asset('assets/imgLanding/logo-mini.png')}}" class="img-rb" style="vertical-align: sub;">            
                                <strong class="white font-weight-bolder" style="font-size:40px"> -50.000 </strong>

                                <i style="font-variant:'Montserrat-Regular'; color: white">  Ganancia actual: $70.000</i> Ganancia actual: $70.000

                                <i style="font-variant:'Helvetica Neue' ; color: white">  Ganancia actual: $70.000</i> Ganancia actual: $70.000

                                
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
                        <div class="card card-redonda text-center">
                            <div class="card-title "> 
                                <div class="float-center">
                          <img src="{{asset('assets/imgLanding/esmeralda.png')}}" class="img-rb" style="width: 500px">      
                                </div>
                                           
                                <strong class="white font-weight-bolder" style="font-size:40px"> ESMERALDA </strong>
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