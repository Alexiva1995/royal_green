{{-- <div class="col-12 text-right">
    <button type="button" class="btn btn-secondary" onclick="activarSocial();">Edit</button>
</div> --}}
<form action="{{ action($controler, ['data' => 'social']) }}" method="post">
    {{ method_field('PUT') }}
    {{ csrf_field() }}


    <legend>Puntos Binarios</legend>
    <input name="id" type="hidden" value="{{$data['segundo']->ID}}">

    <div class="form-group" style="margin-bottom: 15px;">
        <label>Puntos Derechos</label>

        <input name="binario_der" type="text" placeholder="{{$data['puntos']->binario_der}}" class="form-control social"
            value="{{$data['puntos']->binario_der}}" required>
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label>Puntos Izquierdos</label>

        <input name="binario_izq" type="text" placeholder="{{$data['puntos']->binario_izq}}" class="form-control social"
            value="{{$data['puntos']->binario_izq}}" required>
    </div>

    <div class="col-12" id="botom2">
        {{-- <button type="button" class="btn btn-danger" onclick="cancelarSocial();">Cancel</button> --}}
        <button type="submit" class="btn btn-success">Send</button>
    </div>

</form>

<form action="{{ action($controler, ['data' => 'puntos']) }}" method="post">
    {{ method_field('PUT') }}
    {{ csrf_field() }}


    <legend>Puntos Rangos</legend>
    <input name="id" type="hidden" value="{{$data['segundo']->ID}}">

    <div class="form-group" style="margin-bottom: 15px;">
        <label>Puntos Rangos</label>

        <input name="puntos_rank" type="text" placeholder="{{$data['principal']->puntos_rank}}" class="form-control social"
            value="{{$data['principal']->puntos_rank}}" required>
    </div>

    <div class="col-12" id="botom2">
        {{-- <button type="button" class="btn btn-danger" onclick="cancelarSocial();">Cancel</button> --}}
        <button type="submit" class="btn btn-success">Send</button>
    </div>

</form>