<form action="{{ route('profile.update',$user->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <h2 class="font-weight-bold text-white">Foto de perfil</h2>
            </div>
        </div>
        <div class="media col-12">
            <div class="custom-file">
                <label class="custom-file-label  border border-primary rounded" for="photoDB"
                    style="background: #173138 ;color: white;">Seleccione su
                    Foto <b>(Se permiten JPG o PNG.
                        Tamaño máximo de 800kB)</b></label>
                <input type="file" id="photoDB" class="custom-file-input rounded" name="photoDB"
                    onchange="previewFile(this, 'photo_preview')" accept="image/*">
            </div>
        </div>
        <div class="row mb-4 mt-4 d-none col-12" id="photo_preview_wrapper">
            <div class="col"></div>
            <div class="col-auto">
                <img id="photo_preview" class="img-fluid rounded" />
            </div>
            <div class="col"></div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <h2 class="font-weight-bold text-white">Datos Personales</h2>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label for="fullname">Nombre Completo</label>
                <input type="text" class="form-control border border-primary rounded" name="fullname"
                    value="{{ $user->fullname }}">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control border border-primary rounded" name="email"
                    value="{{ $user->email }}">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label for="whatsapp">Telefono</label>
                <input type="text" class="form-control border border-primary rounded" name="whatsapp"
                    value="{{ $user->whatsapp }}">
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label for="address">Dirección</label>
                <textarea class="form-control border border-primary rounded"
                    name="address">{{ $user->address}}</textarea>
            </div>
        </div>
       <!--  <div class="col-12">
            <div class="form-group">
                <h2 class="font-weight-bold text-white">Dirección de Billetera </h2>
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                <label for="wallet_address">Billetera</label>
                <input type="text" class="form-control border border-primary rounded" name="wallet_address"
                    value="{{ $user->wallet_address }}">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label for="type_wallet">Tipo de Billetera @if ($user->type_wallet != '')
                    Billetera Selecionada: {{ $user->type_wallet }} @endif</label>
                <select name="type_wallet" class="form-control border border-primary rounded">
                    <option value="" selected disabled>Seleccione una billetera</option>
                    <option value="BTC">Bitcoin (BTC)</option>
                    <option value="USDT">Tether (USDT)</option>
                </select>
            </div>
        </div>
        <div class="col-12 mb-2">
            <a href="https://accounts.binance.com/es/register" target="_blank" class="gold waves-effect waves-light">
                <b>¿No tiene billetera? Abre una cuenta en binance</b></a>
        </div> -->
        <div class="col-12">
            <div class="form-group">
                <h2 class="font-weight-bold text-white">Cambiar contraseña</h2>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label for="current_password">Contraseña Actual</label>
                <input type="password" class="form-control border border-primary rounded" name="current_password">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <input type="password" class="form-control border border-primary rounded" name="new_password">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label for="new_confirm_password">Confirme la Contraseña</label>
                <input type="password" class="form-control border border-primary rounded" name="new_confirm_password">
            </div>
        </div>
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-outline-primary rounded" type="submit">GUARDAR</button>
        </div>
    </div>
</form>
