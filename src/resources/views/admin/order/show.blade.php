@extends('shopen::admin.layouts.admin')

@section('content')
    <div class="px-6 py-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="panel w-full sm:w-1/2">
                @block('order.show.overview')
            </div>
            <div class="panel w-full sm:w-1/2">
                @block('order.show.addresses')
            </div>
        </div>
        <div class="panel">
            @block('order.show.items')
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="panel w-full sm:w-1/2">
                @block('order.show.status')
            </div>
            <div class="panel w-full sm:w-1/2">
                @block('order.show.summary')
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="panel w-full sm:w-1/2">
                @block('order.show.shipping')
            </div>
        </div>
    </div>
@endsection