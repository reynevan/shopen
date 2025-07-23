@extends('shopen::admin.layouts.admin')

@section('content')
    <product-form id="{{ $product->id }}"></product-form>
@endsection

@push('body-scripts')
    <script src="/js/tinymce/tinymce.min.js"></script>
@endpush