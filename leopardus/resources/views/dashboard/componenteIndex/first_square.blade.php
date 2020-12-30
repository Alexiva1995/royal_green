<div class="col-md-6 col-12">
    {{-- Inversiones --}}
    <div class="col-12">
        <h5 class="text-white">PAQUETE DE INVERSION</h5>
        <div class="carrusel_paquete">
            @foreach ($data['paquetes'] as $paquete)
            <div class="text-center ml-2 mr-2" onclick="updatePaqueteInfo('{{json_encode($paquete)}}')">
                <h6 class="text-center" style="color: #66ffcc">
                    <small>
                        <strong>
                        {{substr($paquete->detalles_producto->nombre, 8)}}
                        @if (Auth::user()->ID == 1)
                        <br>
                        ID User - {{$paquete->iduser}}
                        @endif
                        </strong>
                    </small>
                </h6>
                <div class="progress progress-bar-info rotate-progress m-auto">
                    <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:{{$paquete->progreso}}%">
                        <div class="progress-circular">
                            <small><strong>{{number_format($paquete->progreso * 2, 2, ',', '.')}} %</strong></small>
                        </div>
                    </div>
                </div>
                @if (count($data['paquetes']) > 0)
                <h6 class="text-center indicate" style="color: #66ffcc; {{ ($paquete->id != $data['paquetes'][0]->id) ? 'display:none;' : 'display:block;'}}" id="paquete{{$paquete->id}}"> 
                    <i class="feather icon-minus"></i> 
                </h6>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    {{-- Inversion activa --}}
    <div class="col-12 mt-3">
        <div class="card card-green-alt">
            <div class="card-body">
                <h3 class="text-white">
                    <img src="{{(count($data['paquetes']) > 0) ? $data['paquetes'][0]->detalles_producto->img : ''}}" alt="" height="50" id="imgpaquete">
                    @if (Auth::user()->ID == 1)
                    <small>
                        <strong>- ID user:  
                            <span id="userpaquete">{{(count($data['paquetes']) > 0) ? $data['paquetes'][0]->iduser : 0}}</span> 
                        </strong>
                    </small>
                    @endif
                </h3>
                <p>Ganacia Actual: $ <span id="ganaciaPaquete">{{(count($data['paquetes']) > 0) ? $data['paquetes'][0]->ganado : 0}}</span></p>
                <div class="row">
                    <div class="col-10">
                            <div class="progress progress-bar-primary progress-xl m-0">
                                <div id="pogrepaquete" class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:{{(count($data['paquetes']) > 0) ? $data['paquetes'][0]->progreso : 0}}%"></div>
                            </div>
                    </div>
                    <div class="col-2">
                        <span class="text-white">
                            <strong><span id="porcepaquete">{{(count($data['paquetes']) > 0) ? number_format($data['paquetes'][0]->progreso * 2, 2, ',', '.') : 0}}</span> %</strong>
                        </span>

                    </div>
                </div>
                <span>
                    <small>Activo <span id="activepaquete">{{(count($data['paquetes']) > 0) ? date('Y-m-d', strtotime($data['paquetes'][0]->created_at)) : ''}}</span></small>
                </span>
            </div>
        </div>
    </div>
    {{-- Transaciones --}}
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
</div>