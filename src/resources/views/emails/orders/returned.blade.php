@extends('shopen::layouts.mail')
@section('content')
    @push('message')
        dziękujemy za złożenie zamówienia nr <b>{{ $order->order_number }}</b> w naszym sklepie.
        Obecnie oczekuje ono na potwierdzenie – wyślemy Ci kolejnego maila, gdy zamówienie zostanie zatwierdzone i
        rozpocznie się jego realizacja.
    @endpush
    @includeFirst(['emails.orders.elements.status', 'shopen::emails.orders.elements.status'], ['order' => $order])

    @includeFirst(['emails.elements.greeting', 'shopen::emails.elements.greeting'], ['order' => $order])

    @includeFirst(['emails.orders.elements.message', 'shopen::emails.orders.elements.message'])

    @includeFirst(['emails.orders.elements.comments', 'shopen::emails.orders.elements.comments'], ['comment' => $comment])

    @includeFirst(['emails.orders.elements.details', 'shopen::emails.orders.elements.details'], ['order' => $order])

    <table style="padding: 20px 0 20px 0;  margin: 20px 0; background-color: #fff;">
        <tbody>
        <tr>
            <td>
                @includeFirst(['emails.orders.elements.products', 'shopen::emails.orders.elements.products'], ['items' => $order->items])

                @includeFirst(['emails.orders.elements.summary', 'shopen::emails.orders.elements.summary'], ['order' => $order])

                @includeFirst(['emails.orders.elements.addresses', 'shopen::emails.orders.elements.addresses'], ['order' => $order])
            </td>
        </tr>
        </tbody>

    </table>
@endsection