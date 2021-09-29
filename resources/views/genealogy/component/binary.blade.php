    <div class="col-md-3 col-lg-3 d-none d-lg-block d-md-block text-center">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        @if(!request()->get('audit'))
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase">Puntos izquierda:
                                {{$binario['totali']}}</button>
                        </div>
                        @else
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase">Puntos izquierda:
                               <span id="puntosI"></span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-6 col-sm-12">
        <div class="w-100 art" id="tarjeta">
            <div class="row p-2">
                <div class="col-md-4 col-lg-4">
                    <div class="mb-3 d-flex justify-content-center">
                        <img id="imagen" class="rounded-circle" width="120px" height="120px">
                    </div>
                </div>
                
                <div class="col-md-8 col-lg-8mt-2">
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


    <div class="col-md-3 col-lg-3 col-sm-6 text-center">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        @if(!request()->get('audit'))
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase">Puntos Derecha:
                                {{$binario['totald']}}</button>
                        </div>
                        @else
                        <div class=" d-flex white mt-2">
                            <button class="btn-tree text-left text-uppercase">Puntos Derecha:
                                <span id="puntosD"></span>
                        </div>
                        
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>