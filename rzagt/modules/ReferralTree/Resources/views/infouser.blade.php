<img title="{{ ucwords($data->display_name) }}" src="{{ $data->avatar }}" style="width:64px"
    onclick="nuevoreferido('{{base64_encode($data->ID)}}', '{{$type}}')">
<div class="inforuser">
    <div class="card mb-0" style="background:#66FFCC !important">
        <div class="card-header mx-auto">
            <div class="avatar avatar-xl">
                <img class="img-fluid" src="{{ $data->avatar }}" alt="img placeholder">
            </div>
        </div>
        <h4 class="text-white">{{ ucwords($data['nombre']) }}</h4>
        <div class="card-content" >
            <div class="card-body text-center" style="background:#11262c !important">
                {{-- <p class="">Backend Dev</p> --}}
                {{-- <div class="card-btns d-flex justify-content-between">
                <button class="btn gradient-light-primary">Follow</button>
                <button class="btn btn-outline-primary">Message</button>
            </div> --}}
                {{-- <hr class="my-2"> --}}
                <div class="d-flex justify-content-center">
                    <div>
                        <h6>
                            Fecha Ingreso: <strong>{{date('d-m-Y', strtotime($data->created_at))}}</strong>
                        </h6>
                        <h6>
                            Email: <strong>{{$data->user_email}}</strong>
                        </h6>
                        <h6>
                            Patrocinador: <strong>{{$data->patrocinador}}</strong>
                        </h6>
                        <h6>
                            Pto. Izq: <strong>{{$data->puntos->binario_izq}}</strong>
                        </h6>
                        <h6>
                            Pto. Der: <strong>{{$data->puntos->binario_der}}</strong>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>