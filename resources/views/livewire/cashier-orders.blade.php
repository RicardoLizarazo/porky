@extends('adminlte::page')

@section('title', 'Cobro de Mesas')

@section('content_header')
<div class="container-fluid">

    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-cash-register mr-2"></i>
                Cobro de Mesas
            </h1>
        </div>
    </div> 
</div>
@stop

@section('content')
    <livewire:cash.cashier-orders />
@stop

@section('css')
    @vite(['resources/css/app.css'])
@stop

@section('js')
    @stack('script')
@stop