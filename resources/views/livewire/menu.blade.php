@extends('adminlte::page')

@section('title', 'Menu Principal')

@section('content_header')
    <br>
@stop

@section('content')
    <livewire:menu></livewire:menu>
    <livewire:cart />
    <livewire:cart-sidebar /> 
    <livewire:cart-floating />         
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