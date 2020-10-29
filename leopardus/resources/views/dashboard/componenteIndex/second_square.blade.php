<div class="row">
    {{-- Seccion Grafico --}}
    <div class="col-12 ">
        <div class="row justify-content-center">
            <div class="col-sm-6 col-md-4 col-12 mt-3">
                <div class="card h-100 justify-content-center">
                    <div class="card-header d-flex align-items-center text-right pb-0 pt-0">
                        <div class="avatar bg-rgba-success p-50 m-0">
                            <div class="avatar-content">
                                <i class="fa fa-money text-success font-medium-5"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-bold-700 mt-1">$ {{number_format($data['comisiones'], '2', ',', '.')}}</h2>
                            <p class="mb-0">Comisiones totales</p>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="line-area-chart-2"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-12 mt-3">
                <div class="card h-100 justify-content-center">
                    <div class="card-header d-flex align-items-center text-right pb-0 pt-0">
                        <div class="avatar bg-rgba-warning p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-shopping-cart text-warning font-medium-5"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-bold-700 mt-1">{{$data['ordenes']}}</h2>
                            <p class="mb-0">Todas las ordenes</p>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="line-area-chart-4"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-12 mt-3">
                <div class="card h-100 justify-content-center">
                    <div class="card-header d-flex align-items-center text-right pb-0 pt-0">
                        <div class="avatar bg-rgba-danger p-50 m-0">
                            <div class="avatar-content">
                                <i class="fa fa-ticket text-danger font-medium-5"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-bold-700 mt-1">
                                {{number_format($data['wallet'], '2', ',', '.')}} $
                            </h2>
                            <p class="mb-0">Wallet</p>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="line-area-chart-3"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12 mt-3">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-2">
                        <div>
                            <h2 class="text-bold-700">{{$data['directo']}}</h2>
                            <p class="mb-0">Referidos directos</p>
                        </div>
                        <div class="avatar bg-rgba-primary p-50">
                            <div class="avatar-content">
                                <i class="feather icon-users text-primary font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card-content">
                        <div id="line-area-chart-5"></div>
                    </div> --}}
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12 mt-3">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-2">
                        <div>
                            <h2 class="text-bold-700">{{$data['indirecto']}}</h2>
                            <p class="mb-0">Referidos en red</p>
                        </div>
                        <div class="avatar bg-rgba-danger p-50">
                            <div class="avatar-content">
                                <i class="fa fa-users text-danger font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card-content">
                        <div id="line-area-chart-6"></div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
