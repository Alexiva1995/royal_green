@extends('layouts.dashboard')

@section('content')
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

<div class="portlet light bordered form-fit">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-equalizer font-blue-hoki"></i>
            <span class="caption-subject font-blue-hoki bold uppercase">Autenticación con Facebook</span>
            
        </div>
    </div>

    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ action('AdminController@updateAuth', ['provider' => 'facebook']) }}" method="post" class="form-horizontal form-bordered">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">APP ID</label>
                    <div class="col-md-9">
                        <input name="facebook_secret_key" type="text" placeholder="ID de la aplicación" class="form-control" value="{{ $socialite_settings->get('facebook')->get('client_id') }}">
                        <span class="help-block"> ID de la aplicación de Facebook </span>
                    </div>
                </div>
                

                <div class="form-group">
                    <label class="control-label col-md-3">Llave secreta</label>
                    <div class="col-md-9">
                        <input name="facebook_public_key" type="text" placeholder="Llave secreta" class="form-control" value="{{ $socialite_settings->get('facebook')->get('client_secret') }}">
                        <span class="help-block"> Llave secreta de la aplicación de Facebook </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Autenticación</label>
                    <div class="col-md-9">
                        
                            <div class="icheck-inline">
                                <label>
                                    <input type="radio" value="1" name="facebook_enable_auth" @if( $settings->enable_auth_fb ) checked @endif class="icheck" data-radio="iradio_flat-green"> Activado </label>
                                <label>
                                    <input @if( !$settings->enable_auth_fb ) checked @endif type="radio" value="0" name="facebook_enable_auth" class="icheck" data-radio="iradio_flat-green"> Desactivado </label>
                            </div>
                        <span class="help-block"> Activa o desactiva la autenticación con Facebook </span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn green">
                            <i class="fa fa-check"></i> Guardar configuración</button>
                    </div>
                </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>
</div>

<div class="portlet light bordered form-fit">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-equalizer font-blue-hoki"></i>
            <span class="caption-subject font-blue-hoki bold uppercase">Autenticación con Twitter</span>
            
        </div>
    </div>    

    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ action('AdminController@updateAuth', ['provider' => 'twitter']) }}" method="post" class="form-horizontal form-bordered">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">APP ID</label>
                    <div class="col-md-9">
                        <input name="twitter_public_key" type="text" placeholder="Id de la aplicación" class="form-control" value="{{ $socialite_settings->get('twitter')->get('client_id') }}">
                        <span class="help-block"> ID de la aplicación en Twitter </span>
                    </div>
                </div>
                

                <div class="form-group">
                    <label class="control-label col-md-3">Llave secreta</label>
                    <div class="col-md-9">
                        <input name="twitter_secret_key" type="text" placeholder="Llave secreta" class="form-control" value="{{ $socialite_settings->get('twitter')->get('client_secret') }}">
                        <span class="help-block"> Llave secreta de la aplicación en Twitter </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Autenticación</label>
                    <div class="col-md-9">
                        
                            <div class="icheck-inline">
                                <label>
                                    <input @if( $settings->enable_auth_tw ) checked @endif type="radio" value="1" name="twitter_enable_auth"  class="icheck" data-radio="iradio_flat-green"> Activado </label>
                                <label>
                                    <input @if( !$settings->enable_auth_tw ) checked @endif type="radio" value="0" name="twitter_enable_auth" class="icheck" data-radio="iradio_flat-green"> Desactivado </label>
                            </div>
                        <span class="help-block"> Activa o desactiva la autenticación con Twitter </span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn green">
                            <i class="fa fa-check"></i> Guardar configuración</button>
                    </div>
                </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>
</div>

<div class="portlet light bordered form-fit">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-equalizer font-blue-hoki"></i>
            <span class="caption-subject font-blue-hoki bold uppercase">Autenticación con Google Plus</span>
            
        </div>
    </div>

    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form action="{{ action('AdminController@updateAuth', ['provider' => 'google']) }}" method="post" class="form-horizontal form-bordered">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">APP ID</label>
                    <div class="col-md-9">
                        <input name="google_public_key" type="text" placeholder="ID de la aplicación" class="form-control" value="{{ $socialite_settings->get('google')->get('client_id') }}">
                        <span class="help-block"> ID de la aplicación en Google Plus </span>
                    </div>
                </div>
                

                <div class="form-group">
                    <label class="control-label col-md-3">Llave secreta</label>
                    <div class="col-md-9">
                        <input name="google_secret_key" type="text" placeholder="Llave secreta" class="form-control" value="{{ $socialite_settings->get('google')->get('client_secret') }}">
                        <span class="help-block"> Llave secreta de la aplicación en Google Plus </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Autenticación</label>
                    <div class="col-md-9">
                        
                            <div class="icheck-inline">
                                <label>
                                    <input type="radio" value="1" name="google_enable_auth" @if( $settings->enable_auth_google ) checked @endif class="icheck" data-radio="iradio_flat-green"> Activado </label>
                                <label>
                                    <input @if( !$settings->enable_auth_google ) checked @endif type="radio" value="0" name="google_enable_auth" class="icheck" data-radio="iradio_flat-green"> Desactivado </label>
                                
                            </div>
                        <span class="help-block"> Activa o desactiva la autenticación con Google Plus </span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn green">
                            <i class="fa fa-check"></i> Guardar configuración</button>
                    </div>
                </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>
</div>
@endsection