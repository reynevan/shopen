@extends('shopen::admin.layouts.admin')

@section('content')
   <promo-code-form :id="{{ $promoCode->id }}"></promo-code-form>
@endsection