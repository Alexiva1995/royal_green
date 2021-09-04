    <div class="col-3 text-center">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase" style="width: 247px;">Puntos izquierda:
                                {{$binario['totali']}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase" style="width: 247px;">Total red Izquierdos:
                                {{$red_i}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="w-100 art" id="tarjeta">
            <div class="row p-2">
                <div class="col-12 mb-3 d-flex justify-content-center">
                    <img id="imagen" class="rounded-circle img-fluid" width="110px" height="110px">
                </div>
                <div class="col-12">
                    <div class="text-white">
                        <p><b>Fecha de Ingreso:</b> <span id="fecha_ingreso"></span></p>
                    </div>
                    <div class="text-white">
                        <p><b>Email:</b> <span id="email"></span></p>
                    </div>
                    <div class="text-white">
                        <p><b>Estado:</b> <span id="estado"></span></p>
                    </div>
                </div>
                @if ($type_tm == 2)
                <div class="col-12 d-flex justify-content-center text-white">
                    <a class="text-white btn-tree w-50" id="ver_arbol"> Ver
                        Arbol</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-3 text-center">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase" style="width: 247px;">Puntos Derecha:
                                {{$binario['totald']}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase" style="width: 247px;">Total red Derechos:
                                {{$red_d}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>