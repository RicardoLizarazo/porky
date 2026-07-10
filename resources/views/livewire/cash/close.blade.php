@extends('adminlte::page')

@section('title', 'Cierre de Caja')

@section('content_header')
<div class="container-fluid">

    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-cash-register mr-2"></i>
                Cierre de Cajas
            </h1>
        </div>
    </div> 
</div>
@stop

@section('content')
    <livewire:cash.close-cash />
@stop

@section('css')
    @vite(['resources/css/app.css'])
@stop