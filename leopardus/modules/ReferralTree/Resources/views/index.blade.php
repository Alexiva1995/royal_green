@extends('layouts.app')

@section('content')
	<div class="col-md-10">
		<div class="tree" style="margin-left:40%;" scroll="auto"> 
		    <ul>
		        <li>
		            @php
		                if ($referidoBase->status == '1'){
		                	if ($referidoBase->gender == 'F'){
		                		$picture = "verdem_(2).png";
		                	}else{
		                		$picture = "verde_(2).png";
		                	}
		                }
		                else{
		                    if ($referidoBase->gender == 'F'){
		                		$picture = "roja_(2)";
		                	}else{
		                		$picture = "rojo_(2).png";
		                	}
		                } 
		            @endphp
		                    
		            <!-- NODO PRINCIPAL -->
		            <a onclick="" href="#"> 
		                <img title="{{ $referidoBase->display_name }}" src="{{ asset('assets/img/') }}/{{ $picture }}" style="width:64px; margin: 5px 8px;">
		            </a> 
		                    
		            <!-- LISTA DE REFERIDOS DIRECTOS -->
		            <ul>
		                @foreach ($referidosDirectos as $referido)
		                    @php
		                         if ($referidoBase->status == '1'){
		                	if ($referidoBase->gender == 'F'){
		                		$picture = "verdem_(2).png";
		                	}else{
		                		$picture = "verde_(2).png";
		                	}
		                }
		                else{
		                    if ($referidoBase->gender == 'F'){
		                		$picture = "roja_(2)";
		                	}else{
		                		$picture = "rojo_(2).png";
		                	}
		                } 
		                    @endphp
		                    <li>
		                        <a href="{{ route('moretree', $referido->id) }}"> 
		                            <img title="{{ $referido->display_name }}" src="{{ asset('assets/img/') }}/{{ $picture }}" style="width:64px; margin: 5px 8px;">
		                        </a>
		                        <ul>
		                            <!-- LISTADO DE REFERIDOS DE LOS REFERIDOS -->
		                            @php
		                                $referidosIndirectos = DB::table($settings->prefijo_wp.'users')
		                                                        ->select('id', 'display_name', 'status', 'gender')
		                                                        ->where('referred_id', '=', $referido->id)
		                                                        ->get();
		                            @endphp     

		                            @foreach ($referidosIndirectos as $referidoInd)
		                                @php
		                                          if ($referidoBase->status == '1'){
		                	if ($referidoBase->gender == 'F'){
		                		$picture = "verdem_(2).png";
		                	}else{
		                		$picture = "verde_(2).png";
		                	}
		                }
		                else{
		                    if ($referidoBase->gender == 'F'){
		                		$picture = "roja_(2)";
		                	}else{
		                		$picture = "rojo_(2).png";
		                	}
		                }  
		                                @endphp
		                                <li>
		                                    <a href="{{ route('moretree', $referidoInd->id) }}"> 
		                                        <img title="{{ $referidoInd->display_name }}" src="{{ asset('assets/img/') }}/{{ $picture }}" style="width:64px">
		                                    </a>
		                                    <ul>
		                            <!-- LISTADO DE REFERIDOS DE LOS REFERIDOS -->
		                            @php
		                                $referidosIndirectos = DB::table($settings->prefijo_wp.'users')
		                                                        ->select('id', 'display_name', 'status', 'gender')
		                                                        ->where('referred_id', '=', $referidoInd->id)
		                                                        ->get();
		                            @endphp     

		                            @foreach ($referidosIndirectos as $referidoInd)
		                                @php
		                                          if ($referidoBase->status == '1'){
		                	if ($referidoBase->gender == 'F'){
		                		$picture = "verdem_(2).png";
		                	}else{
		                		$picture = "verde_(2).png";
		                	}
		                }
		                else{
		                    if ($referidoBase->gender == 'F'){
		                		$picture = "roja_(2)";
		                	}else{
		                		$picture = "rojo_(2).png";
		                	}
		                }  
		                                @endphp
		                                <li>
		                                    <a href="{{ route('moretree', $referidoInd->id) }}"> 
		                                        <img title="{{ $referidoInd->display_name }}" src="{{ asset('assets/img/') }}/{{ $picture }}" style="width:64px">
		                                    </a>
		                                    
		                                </li>
		                            @endforeach
		                        </ul>
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
	
		<div class="col-md-2">
		@if ($principal == 'NO')
		    <center> <a href="{{ route('referraltree')}}">Regresar a mi árbol</a></center>
		@endif
	</div>
@endsection