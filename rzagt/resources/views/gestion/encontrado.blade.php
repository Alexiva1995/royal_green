@extends('layouts.dashboard')

@section('content')


{{-- alertas --}}
@include('dashboard.componentView.alert')

{{-- resumen --}}
@include('dashboard.formEdit.resumen')


<div class="wrapper-md" style="padding: 15px;">
    <div class="col-md-12 buq">

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item ">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
                    aria-selected="true">informacion Personal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                    aria-controls="profile" aria-selected="false">Informacion de contacto</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab"
                    aria-controls="contact" aria-selected="false">Perfil Social</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="bancaria-tab" data-toggle="tab" href="#bancaria" role="tab"
                    aria-controls="bancaria" aria-selected="false">Informacion bancaria</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pagos-tab" data-toggle="tab" href="#pagos" role="tab" aria-controls="pagos"
                    aria-selected="false">Pagos</a>
            </li>
        </ul>
        <!-- Aquí es informacion personal -->

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                @include('dashboard.formEdit.personal', ['controler' => $data['controler']])
            </div>
            <!-- termina informacion personal -->

            <!-- Empieza informacion de Contacto -->
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                @include('dashboard.formEdit.contacto', ['controler' => $data['controler']])
            </div>
            <!-- Termina informacion de Contacto -->

            <!-- Empieza PErfil Social -->
            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                @include('dashboard.formEdit.social', ['controler' => $data['controler']])
            </div>
            <!-- Termina Perfil Social -->

            <!-- Empieza Informaion Bancaria -->
            <div class="tab-pane fade" id="bancaria" role="tabpanel" aria-labelledby="bancaria-tab">
                @include('dashboard.formEdit.bancario', ['controler' => $data['controler']])
            </div>
            <!-- Termina Informaion Bancaria -->

            <!-- Empieza Pagos -->
            <div class="tab-pane fade" id="pagos" role="tabpanel" aria-labelledby="pagos-tab">
                @include('dashboard.formEdit.pago', ['controler' => $data['controler']])
            </div>
        </div>
        <!-- Termina Pago -->
    </div>
</div>

</div>

@endsection