<div class="row">
    <div class="col-lg-6 col-md-12 col-12 mt-1">
        <div class="card bg-analytics bg-blue-2 text-white h-100">
            <div class="card-content">
                <div class="card-body text-center">
                    <div class="avatar avatar-xl bg-green-2 shadow m-0 mb-1">
                        <img src="{{asset('assets/img/sistema/usuario.png')}}" alt="card-img-left">
                        {{-- <div class="avatar-content">
                         <i class="feather icon-award white font-large-1"></i> 
                        </div> --}}
                    </div>
                    <div class="text-center">
                        <h1 class="mb-2 text-white">Bienvenido</h1>
                        <h3 class="mb-2 text-white">{{$data['nombreuser']}}</h3>
                        <h3 class="mb-2 text-white">Paquete - {{$data['paquete']}}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 col-12 mt-1">
        <div class="card text-white bg-gradient-danger bg-red-alt h-100">
            <div class="card-content d-flex justify-contents-start align-items-center">
                <div class="card-body pb-0 pt-1">
                    <img src="{{asset('assets/img/sistema/card-img.svg')}}" alt="element 03" width="250" height="250"
                        class="float-right px-1">
                    <p class="card-text mt-3">Invita a tus amigos <br> y gana una comision</p>
                    <h4 class="card-title text-white">¡Todo es mejor con <br> amigos!</h4>
                    <a href="javascript:;" onclick="copyToClipboard('copy')"
                        class="btn btn-primary padding-button-short bg-white mt-1 waves-effect waves-light">
                        LINK REFERIDO
                    </a>
                    <p class="d-none" id="copy">
                        {{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID}}
                    </p>
                    <h6>
                        <small class="text-white">Lado activo de registro binario</small>
                    </h6>
                    <ul class="list-unstyled mb-0 d-flex">
                        <li class="d-inline-block mr-2">
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="customRadio"
                                        id="customRadio1" @if (Auth::user()->ladoregistrar == 'D') checked
                                    @endif onclick="updateSideBinary('D')">
                                    <label class="custom-control-label text-white"
                                        for="customRadio1">Derecha</label>
                                </div>
                            </fieldset>
                        </li>
                        <li class="d-inline-block mr-2">
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="customRadio"
                                        id="customRadio2" @if (Auth::user()->ladoregistrar == 'I') checked
                                    @endif onclick="updateSideBinary('I')">
                                    <label class="custom-control-label text-white"
                                        for="customRadio2">Izquierda</label>
                                </div>
                            </fieldset>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
