<div class="col-md-6 col-12">
    <h5 class="text-white">PROXIMO RANGO</h5>
    <div class="card card-green-alt">
        <div class="row">
            {{-- carrusel --}}
            <div class="col-12 no_tocar">
                <input type="hidden" id="id_rango_carruse" value="{{Auth::user()->rol_id}}">
                <div class="carrusel_rango">
                    @foreach ($data['rangospoints']['rangos'] as $rango)
                    {{-- @if (Auth::user()->rol_id == $rango->id) --}}
                    <div class="text-center" style="background: #11262c;">
                        <img src="{{asset('assets/rango/'.$rango->imagen)}}" alt="" height="200" class="m-auto">
                        <h3 class="text-white mb-0">
                            <strong>{{$rango->name}}</strong>
                        </h3>
                        {{-- <span>17/11/20</span> --}}
                    </div>
                    {{-- @endif --}}
                    @endforeach
                </div>
            </div>
            {{-- Puntos Totales --}}
            <div class="col-12 mt-3">
                <div class="col-12">
                    <p>Total Puntos: 
                        <strong class="color-green-alt " style="font-size: 1.6em;">{{number_format($data['rangospoints']['puntos'], 0, ',', '.')}}</strong>
                    </p>
                    <div class="row">
                        <div class="col-10">
                                <div class="progress progress-bar-primary progress-xl m-0">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:{{$data['rangospoints']['progreso']}}%"></div>
                                </div>
                        </div>
                        <div class="col-2">
                            <span class="text-white">
                                <strong>{{$data['rangospoints']['progreso']}}%</strong>
                            </span>
                        </div>
                    </div>
                    <span>
                        <small>Proximo rango = {{$data['rangospoints']['total']}}</small>
                    </span>
                </div>
            </div>
            {{-- Grafica --}}
            <div class="col-12 mt-2">
                <div class="col-12">
                    <h5 class="text-white">Referidos - {{date('Y')}}</h5>
                    <div id="grafica_user"></div>
                </div>
            </div>
        </div>
    </div>
</div>