    <div class="col-12 col-md-3 col-lg-3 text-center">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        @if(!Request::get('audit'))
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase">Puntos izquierda:
                                {{$binario['totali']}}</button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-lg-6">
        <div class="w-100 art" id="tarjeta">
            <div class="row pt-2 pl-3">
                <div class="col-3">
                    <div class="mb-3 d-flex justify-content-center">
                        <img id="imagen" class="rounded-circle" width="120px" height="120px">
                    </div>
                </div>
                
                <div class="col-5 mt-2">
                    <div class="text-white">
                        <p><b>Ingreso:</b> <span id="fecha_ingreso"></span></p>
                    </div>
                    <div class="text-white">
                        <p><b>Email:</b> <span id="email"></span></p>
                    </div>
                    <div class="text-white">
                        <p><b>Estado:</b> <span id="estado"></span></p>
                    </div>
                </div>
                @if ($type_tm == 2)
                <div class="col-4 text-white mt-5">
                    <a class="text-white btn-tree" id="ver_arbol"> Ver
                        Arbol</a>
                </div>
                @endif
            </div>
        </div>
    </div>


    <div class="col-12 col-md-3 col-lg-3 text-center">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        @if(!Request::get('audit'))
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase">Puntos Derecha:
                                {{$binario['totald']}}</button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>