@extends('shopen::layouts.mail')
@section('content')
    @push('message')
        Przesyłamy fakturę elektroniczną do Twojego zamówienia o numerze <b>{{ $invoice->order->order_number }}</b>.
        <br>
        Dokument znajdziesz w załączniku do tej wiadomości.
    @endpush

    @includeFirst(['emails.elements.greeting', 'shopen::emails.elements.greeting'], ['name' => $invoice->order->getUserFirstName()])

    @includeFirst(['emails.orders.elements.message', 'shopen::emails.orders.elements.message'])

@endsection