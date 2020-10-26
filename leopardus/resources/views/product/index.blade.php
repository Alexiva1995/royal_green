@extends('layouts.dashboard')

@section('content')
{{-- alertas --}}
@include('dashboard.componentView.alert')
<br>

<div class="col-xs-12">
    <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">
        Nuevo Producto
    </button>
</div>

{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="table-responsive">
                <table id="mytable" class="table zero-configuration">
                    <thead>
                        <tr>
                            <th class="text-center">
                                Product ID
                            </th>
                            <th class="text-center">
                                Imagen
                            </th>
                            <th class="text-center">
                                Nombre
                            </th>
                            <th class="text-center">
                                Descripcion
                            </th>
                            <th class="text-center">
                                Precio
                            </th>
                            <th class="text-center">
                                Nivel de pago
                            </th>
                            <th class="text-center">
                                Tipo de pago
                            </th>
                            <th class="text-center">
                                Porcentaje de Rentabilidad
                            </th>
                            <th class="text-center">
                                Dias Activos
                            </th>
                            <th class="text-center">
                                Visible en la tienda
                            </th>
                            <th class="text-center">
                                Archivo
                            </th>
                            <th>
                                Acción
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                        <tr>
                            <td class="text-center">
                                {{$product->ID}}
                            </td>
                            <td class="text-center">
                                <img src="{{$product->imagen}}" height="50">
                            </td>
                            <td class="text-center">
                                {{$product->post_title}}
                            </td>
                            <td class="text-center">
                                {{$product->post_content}}
                            </td>
                            <td class="text-center">
                                $ {{$product->meta_value}}
                            </td>
                            <td class="text-center">
                                {{$product->nivel_pago}}
                            </td>
                            <td class="text-center">
                                @if ($product->tipo_pago == 'asr')
                                    Activacion sin rentabilidad
                                @else
                                    Activacion con rentabilidad
                                @endif
                            </td>
                            <td class="text-center">
                                {{$product->porcentaje}}
                            </td>
                            <td class="text-center">
                                {{$product->dias_activos}}
                            </td>
                            <td class="text-center">
                                {{$product->visible}}
                            </td>
                            <td class="text-center">
                                @foreach ($product->file as $index => $value)
                                @if ($product->type->$index == 'documento')
                                    <a href="{{ asset('products/'.$value)}}" target="_blank">Descargar</a>
                                @elseif($product->type->$index == 'imagen')
                                    <img src="{{ asset('products/'.$value)}}" height="100">
                                @elseif($product->type->$index == 'audio')
                                    <audio src="{{ asset('products/'.$value)}}" controls></audio>
                                @elseif($product->type->$index == 'video')
                                    <video src="{{ asset('products/'.$value)}}" controls height="100"></video>
                                @endif
                                @endforeach
                            </td>
                            <td>
                                <a class="btn btn-info" onclick="editProduct({{json_encode($product)}})"> Editar</a>
                                <a class="btn btn-danger" href="{{route('save.delete', [$product->ID])}}"> Borrar</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Nuevo Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="{{route('save.product')}}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="">Nombre del Producto</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Descripcion</label>
                        <textarea name="content" id="" cols="30" rows="10" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Precio</label>
                        <input type="text" class="form-control" name="price">
                    </div>
                    <div class="form-group">
                        <label for="">Niveles de pago</label>
                        <select class="form-control" name="nivel_pago" id="" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1">Nivel 1</option>
                            <option value="2">Nivel 2</option>
                            <option value="3">Nivel 3</option>
                            <option value="4">Nivel 4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tipo de Pago</label>
                        <select class="form-control" name="tipo_pago" required onchange="toggletipo(this.value)">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="acr">Activacion con rentabilidad</option>
                            <option value="asr">Activacion sin rentabilidad</option>
                        </select>
                    </div>
                    <div class="form-group showsr" style="display: none">
                        <label for="">Dias de activacion</label>
                        <input type="number" class="form-control" name="dias_activos" step="any">
                        <p><small>Nota: Coloque el números de dias que este paquete va a mantener activo al usuario</small></p>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Porcentaje de Rentabilidad</label>
                        <input type="number" class="form-control" name="porcentaje" step="any">
                        <p><small>Nota: Coloque el valor entero el sistema lo va a pasar a porcentaje</small></p>
                    </div>
                    <div class="form-group">
                        <label for="">Visible en la tienda</label>
                        <select class="form-control" name="visible" id="" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="Visible">Visible</option>
                            <option value="No Visible">No Visible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Imagen Producto</label>
                        <input type="file" name="imagen" class="form-control" required accept="image/jpeg, image/png">
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Tipo de Archivo a subir 1</label>
                        <select class="form-control require" name="type_file_1" id="" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="documento">Documento (PDF, Docx, Excel, entre otros)</option>
                            <option value="imagen">Imagen</option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Archivo 1</label>
                        <input type="file" name="file_1"  class="form-control require" required>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Tipo de Archivo a subir 2</label>
                        <select class="form-control" name="type_file_2" id="">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="documento">Documento (PDF, Docx, Excel, entre otros)</option>
                            <option value="imagen">Imagen</option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Archivo 2</label>
                        <input type="file" name="file_2" class="form-control">
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Tipo de Archivo a subir 3</label>
                        <select class="form-control" name="type_file_3" id="">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="documento">Documento (PDF, Docx, Excel, entre otros)</option>
                            <option value="imagen">Imagen</option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Archivo 3</label>
                        <input type="file" name="file_3" class="form-control">
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Tipo de Archivo a subir 4</label>
                        <select class="form-control" name="type_file_4" id="">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="documento">Documento (PDF, Docx, Excel, entre otros)</option>
                            <option value="imagen">Imagen</option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Archivo 4</label>
                        <input type="file" name="file_4" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
{{-- modal Editar --}}
<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Editar Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="{{route('edit.product')}}" method="post"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="idproduct" id="product">
                    <div class="form-group">
                        <label for="">Nombre del Producto</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Descripcion</label>
                        <textarea name="content" id="content" cols="30" rows="10" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Precio</label>
                        <input type="text" class="form-control" id="price" name="price">
                    </div>
                    <div class="form-group">
                        <label for="">Niveles de pago</label>
                        <select class="form-control" name="nivel_pago" id="nivel_pago" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1">Nivel 1</option>
                            <option value="2">Nivel 2</option>
                            <option value="3">Nivel 3</option>
                            <option value="4">Nivel 4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tipo de Pago</label>
                        <select class="form-control" name="tipo_pago" id="tipo_pago" required onchange="toggletipo(this.value)">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="acr">Activacion con rentabilidad</option>
                            <option value="asr">Activacion sin rentabilidad</option>
                        </select>
                    </div>
                    <div class="form-group showsr" style="display: none">
                        <label for="">Dias de activacion</label>
                        <input type="number" class="form-control" name="dias_activos" id="dias_activos" step="any">
                        <p><small>Nota: Coloque el números de dias que este paquete va a mantener activo al usuario</small></p>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Porcentaje de Rentabilidad</label>
                        <input type="number" class="form-control" id="porcentaje" name="porcentaje" step="any">
                        <p><small>Nota: Coloque el valor entero el sistema lo va a pasar a porcentaje</small></p>
                    </div>
                    <div class="form-group">
                        <label for="">Visible en la tienda</label>
                        <select class="form-control" name="visible" id="visible" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="Visible">Visible</option>
                            <option value="No Visible">No Visible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Imagen Producto</label>
                        <input type="file" name="imagen" class="form-control" accept="image/jpeg, image/png">
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Tipo de Archivo a subir 1</label>
                        <select class="form-control" name="type_file_1" id="">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="documento">Documento (PDF, Docx, Excel, entre otros)</option>
                            <option value="imagen">Imagen</option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Archivo 1</label>
                        <input type="file" name="file_1" class="form-control">
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Tipo de Archivo a subir 2</label>
                        <select class="form-control" name="type_file_2" id="">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="documento">Documento (PDF, Docx, Excel, entre otros)</option>
                            <option value="imagen">Imagen</option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Archivo 2</label>
                        <input type="file" name="file_2" class="form-control">
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Tipo de Archivo a subir 3</label>
                        <select class="form-control" name="type_file_3" id="">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="documento">Documento (PDF, Docx, Excel, entre otros)</option>
                            <option value="imagen">Imagen</option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Archivo 3</label>
                        <input type="file" name="file_3" class="form-control">
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Tipo de Archivo a subir 4</label>
                        <select class="form-control" name="type_file_4" id="">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="documento">Documento (PDF, Docx, Excel, entre otros)</option>
                            <option value="imagen">Imagen</option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="form-group hiddensr">
                        <label for="">Archivo 4</label>
                        <input type="file" name="file_4" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    function editProduct(dataProduct) {
        $('#price').val(dataProduct.meta_value)
        $('#content').val(dataProduct.post_content)
        $('#name').val(dataProduct.post_title)
        $('#product').val(dataProduct.ID)
        $('#type_file').val(dataProduct.type)
        $('#nivel_pago').val(dataProduct.nivel_pago)
        $('#porcentaje').val(dataProduct.porcentaje)
        $('#visible').val(dataProduct.visible)
        if (dataProduct.tipo_pago == 'asr') {
            $('.hiddensr').css('display', 'none')
            $('.showsr').css('display', 'block')
        }else{
            $('.hiddensr').css('display', 'initial')
            $('.showsr').css('display', 'none')
        }
        $('#tipo_pago').val(dataProduct.tipo_pago)
        $('#dias_activos').val(dataProduct.dias_activos)
        $('#myModalEdit').modal('show')
    }

    function toggletipo(value) {
        if (value == 'asr') {
            $('.hiddensr').css('display', 'none')
            $('.hiddensr .require').removeAttr('required')
            $('.showsr').css('display', 'block')
        }else{
            $('.hiddensr').css('display', 'initial')
            $('.hiddensr .require').prop('required', true)
            $('.showsr').css('display', 'none')
        }
    }
</script>