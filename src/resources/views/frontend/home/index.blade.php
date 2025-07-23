@extends('shopen::frontend.layouts.main')


@section('content')

    home

    <div class="mb-6">
        @block('product.carousel', ['category_id' => 1, 'title' => 'Bestsellery'])
    </div>
    @block('product.carousel', ['category_id' => 10, 'title' => 'Takie tam', 'limit' => 5])
@endsection