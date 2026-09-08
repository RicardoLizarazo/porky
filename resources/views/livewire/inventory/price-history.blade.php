@extends('adminlte::page')

@section('title', 'Historial de Precios')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-chart-line mr-2"></i> Historial de Precios
                </h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <livewire:inventory.price-history></livewire:inventory.price-history>
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
