@extends('shopen::frontend.layouts.main')


@section('content')
    <div class="flex">
        @block('user.menu')
        <div>
            @foreach ($orders as $order)
                <a href="{{ route('user.orders.show', $order) }}">{{ $order->order_number }}</a>
            @endforeach
        </div>

    </div>
@endsection