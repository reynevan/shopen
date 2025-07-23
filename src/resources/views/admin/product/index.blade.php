@extends('shopen::admin.layouts.admin')

@section('content')
    <div class="my-4 flex justify-end">
        <a href="{{ route('admin.products.create') }}" class="button-primary">Nowy produkt</a>
    </div>
    <products-table></products-table>
@endsection