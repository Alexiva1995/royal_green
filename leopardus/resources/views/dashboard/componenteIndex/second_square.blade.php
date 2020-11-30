<div class="col-md-6 col-12">
    <h5 class="text-white">NEGOCIO</h5>
    <div class="card card-green-alt">
        <div class="row">
            {{-- carrusel --}}
            <div class="col-12">
                <div class="carrusel_rango">
                    @for ($i = 1; $i < 5; $i++)
                    <div class="text-center">
                        <img src="{{asset('assets/imgLanding/esmeralda.png')}}" alt="">
                        <h3 class="text-white mb-0">
                            <strong>ESMERALDA {{$i}}</strong>
                        </h3>
                        <span>17/11/20</span>
                    </div>
                    @endfor
                </div>
            </div>
            {{-- Puntos Totales --}}
            <div class="col-12 mt-3">
                <div class="col-12">
                    <p>Total Puntos: 
                        <strong class="color-green-alt " style="font-size: 1.6em;">{{number_format('90000', 0, ',', '.')}}</strong>
                    </p>
                    <div class="row">
                        <div class="col-10">
                                <div class="progress progress-bar-primary progress-xl m-0">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:70%"></div>
                                </div>
                        </div>
                        <div class="col-2">
                            <span class="text-white">
                                <strong>70%</strong>
                            </span>
                        </div>
                    </div>
                    <span>
                        <small>Proximo rango</small>
                    </span>
                </div>
            </div>
            {{-- Grafica --}}
            <div class="col-12 mt-2">
                <div class="col-12">
                    <h5 class="text-white">Referidos</h5>
                    <div id="grafica_user"></div>
                </div>
            </div>
        </div>
    </div>
</div>