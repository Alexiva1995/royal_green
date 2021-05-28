<div class="col-md-12">

    <button type="button" class="btn green padding_both_small" onclick="activarBanco();"
        style="margin-top:5px; float: right !important;">Edit</button>

</div>

<form action="{{ action($controler, ['data' => 'auspiciado']) }}" method="post" id="form-aus">

    {{ method_field('PUT') }}

    {{ csrf_field() }}



    <input name="id" type="hidden" value="{{$data['segundo']->ID}}">

    <div class="form-group">
        <div class="row">
            <div class="col-12">
                @if (Auth::user()->ID == 1)
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>ID del Auspiciador</label>
                    <input name="id_referred" type="number" placeholder="patrocinador" class="form-control personal"
                        value="{{$data['principal']->referred_id}}" required>
                </div>
                {{-- <div class="form-group" style="margin-bottom: 15px;">
                    <label>ID de la Posicion</label>
                    <input name="id_position" type="number" placeholder="posicionamiento" class="form-control personal"
                        value="{{$data['principal']->position_id}}" required>
                </div> --}}
            @endif
            </div>
        </div>
    </div>

    <div class="col-md-12" id="botom3" >

        {{-- <button type="button" class="btn btn-danger" onclick="cancelarBanco();"
            style="margin-top:5px; float: left !important; font-size: 12px;">Cancel</button> --}}

        <button type="button" onclick="modalPatrocinado()" class="btn btn-success padding_both_small"
            style="margin-top:5px; margin-left:10px;">Send</button>

    </div>



</form>