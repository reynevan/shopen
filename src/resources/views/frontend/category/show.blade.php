@extends('shopen::frontend.layouts.main')

@section('content')

        Kategoria {{ $category->name }}

        @block('category.show.products-list')

@endsection