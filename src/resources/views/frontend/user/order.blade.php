@extends('shopen::frontend.layouts.main')


@section('content')
    <div class="flex">

        @block('user.menu')

        <div>
            <div>
                {{ $order }}
            </div>

            <div class="mb-4">
                <div class="text-2xl">Zamówienie nr {{ $order->order_number }}</div>
                <div class="text-lg text-neutral-500">Złożone {{ $order->placed_time }}</div>
            </div>
            <div>
                <div class="text-lg">Dostawa</div>
                @block('user.order.shipping')
            </div>
            <div>
                <div class="flex">
                    <div>

                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection