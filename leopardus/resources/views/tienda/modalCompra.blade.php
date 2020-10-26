<!-- Modal -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                Detalles del Producto
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel1"></h4>
            </div>
            <div class="modal-body">
                <div class="card text-center bg-transparent">
                    <img id="img" src="" alt="" class="card-img-top">
                    <div class="card-content">
                        <div class="card-body">
                            <div id="title" class="card-title mt-2"></div>
                            <p id="content" class="card-text"></p>
                            <p id="price" class="card-text"></p>
                            <h6 class="text-center">
                                <form action="{{route('tienda-save-compra')}}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="idproducto" id="idproducto">
                                    <input type="hidden" id="code_coinbase" name="code_coinbase">
                                    <input type="hidden" id="id_coinbase" name="id_coinbase">
                                    <input type="hidden" id="title2" name="name">
                                    <input type="hidden" id="price2" name="precio">
                                    <input type="hidden" name="tipo" value="">
                                    <button type="submit" class="btn btn-info">Comprar</button>
                                </form>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>