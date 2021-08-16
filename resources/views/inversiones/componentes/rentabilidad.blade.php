@if(auth()->user()->admin == 1)
    <div class="modal fade" id="modalRentabilidad" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content bg-lp" >
            <div class="modal-header bg-lp" >
            <h5 class="modal-title text-white" id="exampleModalLabel">Rentabilidad</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="background: linear-gradient(90deg, rgba(17,38,44,1) 0%, rgba(54,99,112,1) 94%)">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>

            <form action="{{route('updatePorcentajeGanancia')}}" method="POST"> 
                @csrf 
                @method('PUT')
                <div class="modal-body bg-lp" >
                    <label for="porcentaje_ganancia" class="text-white">Ingrese el nuevo porcentaje de ganancia</label>
                    <input type="number" step="any" name="porcentaje_ganancia" class="form-control" required style="background: #5f5f5f5f; color: white; border: 2px solid #66FFCC !important">
                </div>

                <div class="modal-body bg-lp" >
                    <label for="porcentaje_ganancia" class="text-white">Ingrese el nuevo porcentaje de ganancia</label>
                    <input type="number" step="any" name="porcentaje_ganancia" class="form-control" required style="background: #5f5f5f5f; color: white; border: 2px solid #66FFCC !important">
                </div>

                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary text-white">Guardar</button>
                </div>
            </form>

        </div>
        </div>
    </div>
@endif