<div class="col-12">
    <div class="row">
        {{-- Link Referidos --}}
        <div class="col-12 col-md-4">
            <h5 class="text-white">BINARIO</h5>
            <div class="card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4>Lado Binario Selecionado:
                                {{(Auth::user()->ladoregistrar == 'I') ? 'Izquierda' : 'Derecha'}}
                            </h4>
                        </div>
                        <div class="col-12 text-center">
                            <h5 for="">Selecione para cambiar el lado a referir</h5>
                        </div>
                        <div class="col-12 text-center d-flex justify-content-center">

                            <div class="vs-radio-con m-2" onclick="updateSideBinary('I')">
                                <input type="radio" name="vueradio">
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">Izquierda</span>
                            </div>
                            <div class="vs-radio-con m-2" onclick="updateSideBinary('D')">
                                <input type="radio" name="vueradio">
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">Derecha</span>
                            </div>
                        </div>
                        <div class="col-12 mt-1 text-center">
                            <button onclick="copyToClipboard('copy')" class="btn btn-primary">
                                Link de Referido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Fin Link Referidos --}}
        {{-- Arbol Binario --}}
        <div class="col-12 col-md-4">
            <h5 class="text-white">ARBOLES</h5>
            <div class="card h-100">
                <div class="card-body">
                    <div class="row aling-items-center h-100">
                        <div class="col-12 d-flex aling-items-center justify-content-center" style="font-size: 3.5rem;">
                            <i class="feather icon-share-2" style="transform: rotate(90deg); display:block; margin:auto;"></i>
                        </div>
                        <div class="col-12 text-center d-flex justify-content-center">
                            <div class="col-12 col-md-6 mt-1 text-center">
                                <a href="{{route('referraltree', 'matriz')}}" class="btn btn-primary">
                                    Binario
                                </a>
                            </div>
                            <div class="col-12 col-md-6 mt-1 text-center">
                                <a href="{{route('referraltree', 'tree')}}" class="btn btn-primary">
                                    Unilevel
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- Fin Arbol Binario --}}
        {{-- Activacion Manuales --}}
        <div class="col-12 col-md-4">
            <h5 class="text-white">ACTIVACION MANUAL</h5>
            <div class="card h-100">
                <div class="card-body">
                    <div class="row aling-items-center h-100">
                        <div class="col-12 d-flex aling-items-center justify-content-center" style="font-size: 3.5rem;">
                            <i class="fa fa-hand-paper-o" style="display:block; margin:auto;"></i>
                        </div>
                        <div class="col-12 text-center d-flex justify-content-center">
                            <div class="col-12 mt-1 text-center">
                                <a href="{{route('tienda-solicitudes')}}" class="btn btn-primary">
                                    Activacion manual
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
