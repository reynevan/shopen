@extends('shopen::frontend.layouts.main')

@section('content')

        <div class="px-6 py-4">
            <div class="w-md">
                @include('shopen::frontend.auth.elements.registration-form')
            </div>
        </div>
@endsection