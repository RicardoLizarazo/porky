@extends('adminlte::page')

@section('title', 'Salidas y Ajustes')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-dolly mr-2"></i> Salidas y Ajustes de Inventario
                </h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <livewire:inventory.inventory-movements></livewire:inventory.inventory-movements>
@stop

@section('footer')
    <div class="text-center small text-muted">
        <strong>Piqueteadero Porky de la 105</strong> — v2.0.0 <br>
        &copy; {{ date('Y') }} Todos los derechos reservados.
    </div>
@stop

@section('css')
    @vite(['resources/css/app.css'])
@stop

@section('js')
    @stack('script')
@stop
