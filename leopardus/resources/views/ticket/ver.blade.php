@extends('layouts.dashboard')

@section('content')


@php
$falta = DB::table($settings->prefijo_wp.'users')
->where('ID', '=', $ticket->user_id)
->first();

$picture = null;
$nombre = 'Usuario Eliminado';
if (!empty($falta)) {
    $nombre = $falta->user_nicename;
if ($falta->gender == 'F'){
$picture = "avatares/Woman/N/1.png";
}else{
$picture = "avatares/Men/N/1.png";
}
}
@endphp

<div class="card">
  <div class="card-header">
      <h4 class="card-title">Ticket - {{$ticket->titulo}}</h4>
  </div>
  <div class="card-content">
    <div class="card-body">
      <div class="media">
        <img src="{{asset('assets/img/'.$picture)}}" class="mr-1" alt="img placeholder" height="64" width="64">
        <div class="media-body">
          <h5 class="mt-0">{{$nombre}}</h5>
          <p>{!! $ticket->comentario !!}</p>
          <p style="float:right;">{{date('d-m-Y', strtotime($ticket->created_at))}}</p>
          @foreach ($comentario as $comen)
          @if($comen->tickets_id == $ticket->id)
          @php
          $buscar = DB::table($settings->prefijo_wp.'users')
          ->where('ID', '=', $comen->user_id)
          ->get()[0];
          $picture = null;
          if (empty($picture)) {
          if ($buscar->gender == 'F'){
          $picture = "avatares/Woman/N/1.png";
          }else{
          $picture = "avatares/Men/N/1.png";
          }
          }
          @endphp
          <div class="media mt-3">
            <a class="mr-1" href="#">
              <img src="{{asset('assets/img/'.$picture)}}" alt="Generic placeholder image" height="64" width="64" />
            </a>
            <div class="media-body">
              <h5 class="mt-0">{{$buscar->user_nicename}}</h5>
              {!! $comen->comentario !!}
              <p style="float:right;">{{date('d-m-Y', strtotime($comen->created_at))}}</p>
            </div>
          </div>
          @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection