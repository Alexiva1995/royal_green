<div class="modal fade" id="myModalValidacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabelR">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabelR">Código de Validación</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form action="{{route('waller.confimacion')}}" method="post">
            {{csrf_field()}}
            <div class="row" style="background:white;">
            <p class="p-2">
                Su Codigo de validacion, fue enviado a su correo: {{Auth::user()->user_email}}
                <br>
                Tiene 15 min para validar su retiro o pasara a ser anulado
            </p>
              <div class="form-group col-12">
                <label>Codigo de Validación</label>
                <input type="text" class="form-control" name="code" required/>
              </div>
  
              <div class="form-group col-12">
                <button type="submit" class="btn btn-success btn-block retirarbtn">Validar</button>
                <a class="btn btn-danger btn-block" href="{{route('wallet.anular')}}">Anular</a>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>