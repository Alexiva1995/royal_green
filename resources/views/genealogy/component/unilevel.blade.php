    <div class="col-12 d-flex justify-content-center">
        <div class="art" id="tarjeta">
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
                @if (Auth::user()->admin == 1)
                <div class="col-12 d-flex justify-content-center text-white">
                    <a class="text-white btn-tree w-50" id="ver_arbol"> Ver
                        Arbol</a>
                </div>
                @endif
            </div>
        </div>
    </div>