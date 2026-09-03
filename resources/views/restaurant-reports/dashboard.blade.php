@extends('adminlte::page')

@section('title', 'Reportes')

@section('content_header')
    <br>
@stop

@section('content')
    <livewire:restaurant-reports.restaurant-reports-dashboard />
@stop

@section('footer')
    <div class="text-center small text-muted">
        <strong>Piqueteadero Porky de la 105</strong> — v2.0.0 <br>
        &copy; {{ date('Y') }} Todos los derechos reservados.
    </div>
@stop

@section('css')
    @vite(['resources/sass/app.scss'])
@stop

@section('js')
    @stack('script')
@stop