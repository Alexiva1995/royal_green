{{-- <a class="met" onclick="tarjeta({{$data}}, '{{route('genealogy_type_id', [strtolower($type), base64_encode($data->id)])}}')"> --}}
<a onclick="tarjeta( {{$data}}, '{{route('genealogy_type_id', [strtolower($type), base64_encode($data->id)])}}', '{{ $data->inversionMasAlta() != null ? $data->inversionMasAlta()->getPackageOrden->img() :  asset('assets/img/royal_green/logos/logo.svg')}}')">
    @if (empty($data->photoDB))
        <img src="{{ $data->inversionMasAlta() != null ? $data->inversionMasAlta()->getPackageOrden->img() :  asset('assets/img/royal_green/logos/logo.svg')}}" class="rounded-circle" width="100%" height="100%" alt="{{$data->name}}" title="{{$data->name}}">
    @else
        <img src="{{asset('storage/photo/'.$data->photoDB)}}" class="rounded-circle" alt="{{$data->name}}" title="{{$data->name}}" width="55" height="55">
    @endif
</a>

