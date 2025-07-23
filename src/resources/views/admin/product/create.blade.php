@extends('shopen::admin.layouts.admin')

@section('content')
    <product-form></product-form>
@endsection

@push('body-scripts')
    <script src="/js/tinymce/tinymce.min.js"></script>
@endpush