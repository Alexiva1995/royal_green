@if($user->inversionMasAlta() == null)
<div class="modal fade" id="ModalActivacion{{$user->id}}" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-white" id="exampleModalLabel">Activar inversion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('inversiones.activaciones') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" value="{{$user->id}}">
                    ¿Desea crear una orden?
                    <br>
                    <br>
                    <label>Seleccione el paquete</label>
                    <select name="paquete" required class="form-control">
                        <option value="">Seleccione un paquete</option>
                        @foreach ($paquetes as $paquete)
                        <option value="{{$paquete->id}}">{{$paquete->name}}</option>
                        @endforeach
                    </select>

                    <div class="vs-checkbox-con vs-checkbox-success ">
                        <input type="checkbox" name="comision" id="comision">
                        <span class="vs-checkbox ">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span>Click para generar comision</span>
                    </div>

                    <div class="vs-checkbox-con vs-checkbox-success ">
                        <input type="checkbox" name="rentabilidad" id="rentabilidad">
                        <span class="vs-checkbox ">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span>Click para generar rentabilidad</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger"
                        data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endif