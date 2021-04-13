{{-- <div class="col-12 text-right">
    <button type="button" class="btn btn-secondary mt-2 mb-2" onclick="activarPago();">Edit</button>
</div> --}}

<form action="{{ action($controler, ['data' => 'pago']) }}" method="post"
    enctype="multipart/form-data">
    {{ method_field('PUT') }}
    {{ csrf_field() }}
    <input name="id" type="hidden" value="{{$data['segundo']->ID}}">
    <div class="form-group" style="margin-bottom: 15px;">
        <label>Wallet (BTC)</label>
        <input name="paypal" id="paypal" type="text" placeholder="{{$data['segundo']->paypal}}" class="form-control pago"
            value="{{(!empty($data['segundo']->paypal)) ? $data['segundo']->paypal : old('paypal')}}" required>
    </div>
    <div class="col-12" id="botom4">
        <button type="submit" class="btn btn-success">Send</button>
    </div>
</form>