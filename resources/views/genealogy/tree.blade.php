@extends('layouts.dashboard')

@section('content')

<div class="row">

    @if ($type_tm == 1)
    @include('genealogy.component.unilevel')
    @elseif ($type_tm == 2)
    @include('genealogy.component.binary')
    @endif

    <div class="col-12">
        <div class="padre">
            <ul>
                <li class="baseli">
                    <a class="base" href="#">
                        @if (empty($base->photoDB))
                        <img src="{{asset('assets/img/royal_green/logos/logo.svg')}}" alt="{{$base->name}}"
                            title="{{$base->name}}" class="pt-1 rounded-circle"
                            style="width: 95%;height: 107%;margin-left: 0px;margin-top: -8px;">
                        @else
                        <img src="{{asset('storage/photo/'.$base->photoDB)}}" alt="{{$base->name}}"
                            title="{{$base->name}}" class="pt-1 rounded-circle"
                            style="width: 95%;height: 107%;margin-left: 0px;margin-top: -8px;">
                        @endif
                    </a>
                    {{-- Nivel 1 --}}
                    <ul>
                        @foreach ($trees as $child)
                        {{-- genera el lado binario derecho haciendo vacio --}}
                        @include('genealogy.component.sideEmpty', ['side' => 'D', 'cant' => count($trees),'ladouser' =>
                        $child->binary_side])
                        <li href="#prestamo" data-toggle="modal">
                            @include('genealogy.component.subniveles', ['data' => $child])
                            @if (!empty($child->children))
                            {{-- nivel 2 --}}
                            <ul>
                                @foreach ($child->children as $child2)
                                {{-- genera el lado binario derecho haciendo vacio --}}
                                @include('genealogy.component.sideEmpty', ['side' => 'D', 'cant' =>
                                count($child->children),'ladouser' => $child2->binary_side])
                                <li>
                                    @include('genealogy.component.subniveles', ['data' => $child2])
                                    @if (!empty($child2->children))
                                    {{-- nivel 3 --}}
                                    <ul>
                                        @foreach ($child2->children as $child3)
                                        {{-- genera el lado binario derecho haciendo vacio --}}
                                        @include('genealogy.component.sideEmpty', ['side' => 'D', 'cant' =>
                                        count($child2->children),'ladouser' => $child3->binary_side])
                                        <li>
                                            @include('genealogy.component.subniveles', ['data' => $child3])
                                            @if (!empty($child->children))
                                            {{-- nivel 4 
                                            <ul>
                                                @foreach ($child->children as $child)
                                                <li>
                                                    @include('genealogy.component.subniveles', ['data' => $child])
                                                    @if (!empty($child->children))
                                                     nivel 5 
                                                    <ul>
                                                        @foreach ($child->children as $child)
                                                        <li>
                                                            @include('genealogy.component.subniveles', ['data' => $child])
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    fin nivel 5
                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                             fin nivel 4  --}}
                                            @endif
                                        </li>
                                        {{-- genera el lado binario izquierdo haciendo vacio --}}
                                        @include('genealogy.component.sideEmpty', ['side' => 'I', 'cant' =>
                                        count($child2->children),'ladouser' => $child3->binary_side])
                                        @endforeach
                                    </ul>
                                    {{-- fin nivel 3 --}}
                                    @endif
                                </li>
                                {{-- genera el lado binario izquierdo haciendo vacio --}}
                                @include('genealogy.component.sideEmpty', ['side' => 'I', 'cant' =>
                                count($child->children),'ladouser' => $child2->binary_side])
                                @endforeach
                            </ul>
                            {{-- fin nivel 2 --}}
                            @endif
                        </li>
                        {{-- genera el lado binario izquierdo haciendo vacio --}}
                        @include('genealogy.component.sideEmpty', ['side' => 'I', 'cant' => count($trees),'ladouser' =>
                        $child->binary_side])
                        @endforeach
                    </ul>
                    {{-- fin nivel 1 --}}
                </li>
            </ul>
        </div>
    </div>

    @if (Auth::id() != $base->id)
    <div class="col-12 text-center">
        <a class="btn btn-outline-primary border-primary rounded" href="{{route('genealogy_type', strtolower($type))}}">Regresar a mi arbol</a>
    </div>
    @endif

</div>
@endsection

<script type="text/javascript">
    function tarjeta(data, url) {

        // console.log(data);

        $('#nombre').text(data.fullname);

        if (data.photoDB == null) {
            $('#imagen').attr('src', "{{asset('assets/img/royal_green/logos/arbol.svg')}}");
        } else {
            $('#imagen').attr('src', '/storage/photo/' + data.photoDB);
        }

        var date_db = new Date(data.created_at);
        var year = date_db.getFullYear();
        var month = (1 + date_db.getMonth()).toString();
        month = month.length > 1 ? month : '0' + month;
        var day = date_db.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        var date = month + '/' + day + '/' + year;
        $('#fecha_ingreso').text(date);

        $('#email').text(data.email);

        if (data.status == 0) {
            $('#estado').html('<span class="badge badge-warning">Inactivo</span>');
        } else if (data.status == 1) {
            $('#estado').html('<span class="badge badge-success">Activo</span>');
        } else if (data.status == 2) {
            $('#estado').html('<span class="badge badge-danger">Eliminado</span>');
        }

        // if(data.inversion != ' '){
        //     $('#inversion').text(data.inversion);
        // }else{
        //     $('#inversion').text('Sin inversion');
        // }

        $('#ver_arbol').attr('href', url);

        $('#tarjeta').removeClass('d-none');
    }

</script>