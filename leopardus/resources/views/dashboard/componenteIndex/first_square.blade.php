<div class="col-12">
    <div class="row">
        {{-- primer cuadro --}}
        <div class="col-12 col-md-3 mt-2">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="card-header" style="padding-left: 15px !important; padding-top:0rem;">
                                <h4 class="card-title">Link Referido</h4>
                            </div>
                            <div class="col-12 mt-2 text-center" onclick="copyToClipboard('copy')"
                                style="margin-top: 1rem !important;">
                                <button type="button" class="btn bg-orange-alt text-white">
                                    <i class="fa fa-link font-medium-3"></i>
                                    Copiar Enlace
                                </button>
                                <p class="font-small-3 mb-0">*Click Para copiar link</p>
                                <img src="https://comunidadlevelup.com//assets/imgLanding/imagen-referidos-.png"
                                    style="width: 78%;">
                                <p style="display:none;" id="copy">
                                    {{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- segundo cuadro --}}
        <div class="col-12 col-md-7 mt-2 row">
            <div class="col-12 col-md-12 mt-2 " style="margin-top: 0px !important;padding: 0px !important;">
                <div class="card" style="margin-bottom: 1.2rem;">
                    <div class="card-header">
                        <h4 class="card-title">Ganacia por Fondos de Beneficios:</h4>
                    </div>
                    <div class="card-content h-100 d-flex align-items-center">
                        <div class="card-body">
                            <div class="progress progress-bar-primary progress-xl mb-0"
                                style="height: 2.5em;border-radius: 5px;">
                                <div class="progress-bar" role="progressbar" aria-valuenow="{{$rentabilidad}}"
                                    aria-valuemin="0" aria-valuemax="100" style="width:{{$rentabilidad}}%">
                                    {{$rentabilidad}}%</div>
                            </div>
                            <p class="font-small-3 mb-0"> *Meses Trascurridos y pagos realizados</p>
                            <p class="font-small-3 mb-0"> Paquete Actual: <strong>{{$namePack}}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Tercer cuadro --}}
            <div class="col-6 col-md-3 mt-2" style="margin-top: 0px !important;padding-left: 0px; padding-right: 6px;">
                <div class="card h-75">
                    <div class="card-header" style="padding-bottom: 9px !important;">
                        <div class="col-12" style="padding-left: 0px;">
                            <h4 class="card-title" style="color: #0078bc;">Nivel 1</h4>
                            <p class="text-bold-700"> Miembros</p>
                        </div>
                        <div class="col-12 mb-2">
                            <h2 class="text-bold-700 text-alt-blue mt-1 mb-0" style="margin-top: 0px !important;" id="nivel1"></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mt-2" style="margin-top: 0px !important;padding-left: 3px; padding-right: 6px;">
                <div class="card h-75">
                    <div class="card-header" style="padding-bottom: 9px !important;">
                        <div class="col-12" style="padding-left: 0px;">
                            <h4 class="card-title" style="color: #00646d;">Nivel 2</h4>
                            <p class="text-bold-700"> Miembros</p>
                        </div>
                        <div class="col-12 mb-2">
                            <h2 class="text-bold-700 text-alt-blue mt-1 mb-0"
                                style="margin-top: 0px !important;color: #00646d !important;" id="nivel2"></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mt-2" style="margin-top: 0px !important;padding-left: 3px; padding-right: 6px;">
                <div class="card h-75">
                    <div class="card-header" style="padding-bottom: 9px !important;">
                        <div class="col-12" style="padding-left: 0px;">
                            <h4 style="color: #0078bc;" class="card-title">Nivel 3</h4>
                            <p class="text-bold-700"> Miembros</p>
                        </div>
                        <div class="col-12 mb-2">
                            <h2 class="text-bold-700 text-alt-blue mt-1 mb-0" style="margin-top: 0px !important;" id="nivel3"></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mt-2" style="margin-top: 0px !important;padding-left: 3px; padding-right: 0px;">
                <div class="card h-75">
                    <div class="card-header" style="padding-bottom: 9px !important;">
                        <div class="col-12" style="padding-left: 0px;">
                            <h4 class="card-title" style="color: #00646d;">Nivel 4</h4>
                            <p class="text-bold-700"> Miembros</p>
                        </div>
                        <div class="col-12 mb-2">
                            <h2 class="text-bold-700 text-alt-blue mt-1 mb-0"
                                style="margin-top: 0px !important;color:#00646d !important;" id="nivel4"></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- cuarto cuadro --}}
        <div class="col-12 col-md-2 mt-2">
            <div class="col-12 col-md-12 mt-2 " style="margin-top: 0px !important;padding: 0px !important;">
                <div class="card h-100" style="margin-bottom: 1.4rem;">
                    <div class="card-header d-flex flex-column align-items-start "
                        style="padding-bottom: 3.9rem !important;">
                        <h4 class="card-title">Billetera:</h4>
                        <h2 class="text-bold-700 text-alt-blue mt-1 " style="font-size: 21px;">
                            {{number_format(Auth::user()->wallet_amount, 2, ',', '.')}} USD
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 mt-2" style="margin-top: 0px !important;padding: 0px !important;">
                <div class="card h-100">
                    <div class="card-header d-flex flex-column align-items-start"
                        style="padding-bottom: 1.5rem !important;">
                        <h4 class="card-title">Ganancia Totales:</h4>
                        <h2 class="text-bold-700 text-alt-blue mt-1 mb-2"
                            style="margin: 15px 0px 10px 0px !important; font-size: 21px;">
                            {{number_format($ganancias, 2, ',', '.')}} USD</h2>
                    </div>
                    {{-- <div class="card-content">
                    <div id="line-area-chart-1"></div>
                </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>