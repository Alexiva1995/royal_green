@extends('layouts.dashboard')

@section('content')
<div class="portlet light bordered form-fit">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-equalizer font-blue-hoki"></i>
            <span class="caption-subject font-blue-hoki bold uppercase">{{ $title }}</span>
            
        </div>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
  
    @if(session('action') === 'updated')
        <div class="alert alert-success">
            <strong>Saved!</strong> The changes were saved successfully.
        </div>
    @endif

    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ route('admin.settings.update') }}" method="post" class="form-horizontal form-bordered">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">Website Title</label>
                    <div class="col-md-9">
                        <input name="name" type="text" placeholder="Titúlo" class="form-control" value="{{ $settings->name }}">
                        <span class="help-block"> Tutúlo del sitio web </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Eslogan del sitio web</label>
                    <div class="col-md-9">
                        <input name="slogan" type="text" placeholder="Eslogan" class="form-control" value="{{ $settings->slogan }}">
                        <span class="help-block"> Eslogan del sitio web </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Tipo de sitio web</label>
                    <div class="col-md-9">
                        <select name="category_type" class="form-control">
                            <option value="0" @if( $settings->category_type == 0) selected @endif>Categoría 1</option>
                            <option value="1">Categoría 2</option>
                        </select>
                        <span class="help-block"> Selecciona la categoría de esté sitio web. </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Nombre de la empresa</label>
                    <div class="col-md-9">
                        <input name="company_name" type="text" placeholder="Nombre de la empresa" class="form-control" value="{{ $settings->company_name }}">
                        <span class="help-block"> En caso de no contar con una empresa dejar en blanco </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Correo administrativo</label>
                    <div class="col-md-9">
                        <input name="company_email" type="text" placeholder="Correo administrativo" class="form-control" value="{{ $settings->company_email }}">
                        <span class="help-block"> Correo de contacto con la empresa </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Correo de contacto</label>
                    <div class="col-md-9">
                        <input name="site_email" type="text" placeholder="Correo de contacto" class="form-control" value="{{ $settings->site_email }}">
                        <span class="help-block"> Correo de contacto con el administrador </span>
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label col-md-3">Descripción del sitio web</label>
                    <div class="col-md-9">
                        <input name="description" type="text" placeholder="Descripción" class="form-control" value="{{ $settings->description }}">
                        <span class="help-block"> Descripción del sitio web </span>
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label col-md-3">Estado de la pagina web</label>
                    <div class="col-md-9">
                        
                            <div class="icheck-inline">
                                <label>
                                    <input type="radio" value="1" name="status_web" checked class="icheck" data-radio="iradio_flat-green"> Online </label>
                                <label>
                                    <input type="radio" value="0" name="status_web" class="icheck" data-radio="iradio_flat-green"> Mantenimiento </label>
                                
                            </div>
                        <span class="help-block"> Estado de la pagina web </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Registro de nuevos usuarios</label>
                    <div class="col-md-9">
                        <div class="icheck-inline">
                                <label>
                                    <input type="radio" value="1" name="register" checked class="icheck" data-radio="iradio_flat-green"> Activo </label>
                                <label>
                                    <input type="radio" value="0" name="register" class="icheck" data-radio="iradio_flat-green"> Desactivado </label>
                                
                            </div>
                        <span class="help-block"> Habilitado / Deshabilitado el registro de nuevos usuarios </span>
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label col-md-3">Logo de la pagina web</label>
                    <div class="col-md-9">
                        <button class="btn btn-default">Examinar</button>
                        <span class="help-block"> Logo de la pagina web </span>
                    </div>
                </div>
                
                
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn green">
                            <i class="fa fa-check"></i> Guardar configuración</button>
                        <a href="{{ route('admin.index') }}" class="btn default">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>
</div>
@endsection