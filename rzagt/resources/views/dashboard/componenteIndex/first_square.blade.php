<div class="col-md-6 col-12">
    {{-- Binario  --}}
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
    @endif
    {{-- Binario  --}}
    {{-- Inversiones --}}
    <div class="col-12">
        <h5 class="text-white">PAQUETE DE INVERSION</h5>
        <div class="carrusel_paquete">
            <div class="text-center ml-2 mr-2">
               @if (!empty($data['paquetes']))
               <h6 class="text-center" style="color: #66ffcc">
                <small>
                    <strong>
                    {{substr($data['paquetes']->detalles_producto->nombre, 8)}}
                    @if (Auth::user()->ID == 1)
                    <br>
                    ID User - {{$data['paquetes']->iduser}}
                    @endif
                    </strong>
                </small>
            </h6>
            <div class="progress progress-bar-info rotate-progress m-auto">
                <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:{{$data['paquetes']->progreso}}%">
                    <div class="progress-circular">
                        <small><strong>{{number_format($data['paquetes']->progreso * 2, 2, ',', '.')}} %</strong></small>
                    </div>
                </div>
            </div>
            <h6 class="text-center indicate" style="color: #66ffcc;display:block;"> 
                <i class="feather icon-minus"></i> 
            </h6>
               @else
                   Por Favor Realize una compra primero, para mostrar esta seccion
               @endif
            </div>
        </div>
    </div>
    {{-- Inversion activa --}}
    <div class="col-12 mt-3">
        <div class="card card-green-alt">
            <div class="card-body">
                @if (!empty($data['paquetes']))
                <h3 class="text-white">
                    <img src="{{$data['paquetes']->img}}" alt="" height="50" id="imgpaquete">
                    @if (Auth::user()->ID == 1)
                    <small>
                        <strong>- ID user:  
                            <span id="userpaquete">{{ $data['paquetes']->iduser }}</span> 
                        </strong>
                    </small>
                    @endif
                </h3>
                <p>Ganacia Actual: $ <span id="ganaciaPaquete">{{$data['paquetes']->ganado}}</span></p>
                <div class="row">
                    <div class="col-10">
                            <div class="progress progress-bar-primary progress-xl m-0">
                                <div id="pogrepaquete" class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:{{$data['paquetes']->progreso }}%"></div>
                            </div>
                    </div>
                    <div class="col-2">
                        <span class="text-white">
                            <strong><span id="porcepaquete">{{number_format($data['paquetes']->progreso * 2, 2, ',', '.')}}</span> %</strong>
                        </span>

                    </div>
                </div>
                <span>
                    <small>Activo <span id="activepaquete">{{date('Y-m-d', strtotime($data['paquetes']->created_at))}}</span></small>
                </span>
                @endif
            </div>
        </div>
    </div>
    {{-- Transaciones --}}
    {{-- @if ($principal == 1)
    <div class="col-12">
        <h5 class="text-white">Ultimas Transaciones</h5>
        <div class="card card-green-alt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-index table-striped">
                        <tbody>
                            @foreach ($data['wallets'] as $wallet)
                            <tr class="text-center">
                                <td>
                                    @if ($wallet['signo'] == 0)
                                        <i class="feather icon-plus color-green-alt"></i>
                                    @else
                                        <i class="feather icon-minus color-red-alt"></i>
                                    @endif
                                </td>
                                <td>
                                    {{number_format($wallet['monto'], 2, ',', '.')}} $
                                </td>
                                <td>
                                    {{$wallet['tipo']}}
                                </td>
                                <td>
                                    {{$wallet['fecha']}}
                                </td>
                            </tr>
                            @endforeach    
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif --}}
</div>