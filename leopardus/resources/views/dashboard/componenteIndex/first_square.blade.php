<div class="col-md-6 col-12">
    {{-- Inversiones --}}
    <div class="col-12">
        <h5 class="text-white">PAQUETE DE INVERSION</h5>
        <div class="row">
            @for ($i = 1; $i < 4; $i++) 
            <div class="col-3 text-center">
                <div class="progress progress-bar-info rotate-progress m-0">
                    <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:{{ $i * 20}}%">
                        <div class="progress-circular">
                            <strong>{{ $i * 20}} %</strong>
                        </div>
                    </div>
                </div>
            </div>
        @endfor
        </div>
    </div>
    {{-- Inversion activa --}}
    <div class="col-12 mt-3">
        <div class="card card-green-alt">
            <div class="card-body">
                <h3 class="text-white">
                    <img src="{{asset('assets/imgLanding/logo-mini.png')}}" alt="" height="30">
                    <strong>- 50000</strong>
                </h3>
                <p>Ganacia Actual: $ {{number_format('70000', 2, ',', '.')}}</p>
                <div class="row">
                    <div class="col-10">
                            <div class="progress progress-bar-primary progress-xl m-0">
                                <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:50%"></div>
                            </div>
                    </div>
                    <div class="col-2">
                        <span class="text-white">
                            <strong>50%</strong>
                        </span>

                    </div>
                </div>
                <span>
                    <small>Activo 17/05/20</small>
                </span>
            </div>
        </div>
    </div>
    {{-- Transaciones --}}
    <div class="col-12">
        <h5 class="text-white">Ultimas Transaciones</h5>
        <div class="card card-green-alt">
            <div class="card-body">
                <table class="table table-index table-responsive">
                    {{-- <tbody> --}}
                        @for ($i = 1; $i < 9; $i++)
                        <tr>
                            <td>
                                @if (($i%2) == 0)
                                    <i class="feather icon-plus color-green-alt"></i>
                                @else
                                    <i class="feather icon-minus color-red-alt"></i>
                                @endif
                            </td>
                            <td>
                                0.00055555 BTC
                            </td>
                            <td>
                                En Proceso
                            </td>
                            <td>
                                17/11/20
                            </td>
                        </tr>
                        @endfor
                    {{-- </tbody> --}}
                </table>
            </div>
        </div>
    </div>
</div>