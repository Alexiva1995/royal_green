@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="tree" style="margin-left:30%;" scroll="auto"> 
            <ul>
                <li>
                    @php
                        if ($referidoBase->status == '1'){
                            $picture = "verde.png";
                        }
                        else{
                            $picture = "rojo.png";
                        } 
                    @endphp
                    
                    <!-- NODO PRINCIPAL -->
                    <a onclick="" href="#"> 
                        <img title="{{ $referidoBase->display_name }}" src="{{ asset('assets/img/') }}/{{ $picture }}" style="width:64px">
                    </a> 
                    
                    <!-- LISTA DE REFERIDOS DIRECTOS -->
                    <ul>
                        @foreach ($referidosDirectos as $referido)
                            @php
                                if ($referido->status == '1'){
                                    $picture = "verde.png";
                                }
                                else{
                                    $picture = "rojo.png";
                                } 
                            @endphp
                            <li>
                                <a href="{{ route('arbol', $referido->id) }}"> 
                                    <img title="{{ $referido->display_name }}" src="{{ asset('assets/img/') }}/{{ $picture }}" style="width:64px">
                                </a> 

                                <ul>
                                    <!-- LISTADO DE REFERIDOS DE LOS REFERIDOS -->
                                    @php
                                        $referidosIndirectos = DB::table($settings->prefijo_wp.'users')
                                                                ->select('id', 'display_name', 'status')
                                                                ->where('referred_id', '=', $referido->id)
                                                                ->get();
                                    @endphp     

                                    @foreach ($referidosIndirectos as $referidoInd)
                                        @php
                                            if ($referidoInd->status == '1'){
                                                $picture = "verde.png";
                                            }
                                            else{
                                                $picture = "rojo.png";
                                            } 
                                        @endphp
                                        <li>
                                            <a onclick="" href="#"> 
                                                <img title="{{ $referidoInd->display_name }}" src="{{ asset('assets/img/') }}/{{ $picture }}" style="width:64px">
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach  
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
     <center> <a href="{{ route('arbol', Auth::user()->ID) }}">Regresar a mi árbol</a></center>
</div>
@endsection