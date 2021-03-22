{{-- Copiar Link --}}
<p class="d-none" id="copy">
    {{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID}}
</p>
{{-- Salir del sistema --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>

{{-- modal para la rentabilizacion --}}
<div class="modal fade" id="modalRentabilidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabelR">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #11262C">
                <h4 class="modal-title text-white" id="myModalLabelR">Rentabilizar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="background: #11262C">
                <form action="{{route('wallet.pay.rentabilidad')}}" method="post">
                    {{csrf_field()}}
                    <div class="row" >
                        <div class="form-group col-12">
                            <label class="text-white">Porcentaje a rentabilizar</label>
                            <input class="form-control" type="number" name="porcentage" step="any"
                                required />
                            <small class="form-text text-white">
                                colocar el monto en valor entero, el sistema lo va procesar, ejemplo si
                                coloca 5 en el sistema estara 0.05, para el calculo de lo rentibilizado
                            </small>
                        </div>
                        <div class="form-group col-12">
                            <button type="submit"
                                class="btn btn-success btn-block retirarbtn">Rentabilizar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer text-white" style="background: #11262C">
                <button type="button" class="btn btn-default text-white" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>