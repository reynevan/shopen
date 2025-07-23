@extends('shopen::admin.layouts.admin')

@section('content')
    <div class="my-4 flex justify-end">
        <a href="{{ route('admin.promo-codes.create') }}" class="button-primary">Nowy kod</a>
    </div>

    <promo-codes-table></promo-codes-table>
@endsection