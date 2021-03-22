<div class="col-md-6 col-12">
    @if ($principal == 1)
    <h5 class="text-white">BINARIO</h5>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h4>Lado Binario Selecionado: {{(Auth::user()->ladoregistrar == 'I') ? 'Izquierda' : 'Derecha'}}
                    </h4>
                </div>
                <div class="col-12 text-center">
                    <h5 for="">Selecione para cambiar el lado a referir</h5>
                </div>
                <div class="col-12 text-center d-flex justify-content-center">

                    <div class="vs-radio-con m-2" onclick="updateSideBinary('I')">
                        <input type="radio" name="vueradio"
                            checked="{{(Auth::user()->ladoregistrar == 'I') ? 'true' : 'false'}}">
                        <span class="vs-radio">
                            <span class="vs-radio--border"></span>
                            <span class="vs-radio--circle"></span>
                        </span>
                        <span class="">Izquierda</span>
                    </div>
                    <div class="vs-radio-con m-2" onclick="updateSideBinary('D')">
                        <input type="radio" name="vueradio"
                            checked="{{(Auth::user()->ladoregistrar == 'I') ? 'false' : 'true'}}">
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
    @endif
    <h5 class="text-white">PROXIMO RANGO</h5>
    <div class="card card-green-alt">
        <div class="row">
            {{-- carrusel --}}
            <div class="col-12 no_tocar">
                <input type="hidden" id="id_rango_carruse"
                    value="{{(Auth::user()->rol_id == 0) ? 1 : Auth::user()->rol_id}}">
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
                        <strong class="color-green-alt "
                            style="font-size: 1.6em;">{{number_format($data['rangospoints']['puntos'], 0, ',', '.')}}</strong>
                    </p>
                    <div class="row">
                        <div class="col-10">
                            <div class="progress progress-bar-primary progress-xl m-0">
                                <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20"
                                    aria-valuemax="100" style="width:{{$data['rangospoints']['progreso']}}%"></div>
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
            {{-- Bono Bienvenidad --}}
            <div class="col-12 mt-3">
                <h5 class="pl-1">Bono de Bienvenida</h5>
                <div class="col-12">
                    <p>Bono Ganado:
                        <strong class="color-green-alt "
                            style="font-size: 1.6em;">{{number_format($data['bienvenida']['bono'], 0, ',', '.')}}</strong>
                    </p>
                    <div class="row">
                        <div class="col-10">
                            <div class="progress progress-bar-primary progress-xl m-0">
                                <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20"
                                    aria-valuemax="100" style="width:{{$data['bienvenida']['progreso']}}%"></div>
                            </div>
                        </div>
                        <div class="col-2">
                            <span class="text-white">
                                <strong>{{$data['bienvenida']['progreso']}}%</strong>
                            </span>
                        </div>
                    </div>
                    <span>
                        <small>Proximo Bono = {{$data['bienvenida']['requisito']}}</small>
                    </span>
                </div>
            </div>
            {{-- Grafica --}}
            @if ($principal == 1)
            <div class="col-12 mt-2">
                <div class="col-12">
                    <h5 class="text-white">Referidos - {{date('Y')}}</h5>
                    <div id="grafica_user"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>