@extends('adminlte::page')

@section('title', 'Reglas de Productos')

@section('content_header')

<h1>
    <i class="fas fa-random mr-2"></i>
    Gestión de Reglas de Producto
</h1>

@stop

@section('content')

<livewire:product-rules />

@stop

@section('css')
@vite(['resources/css/app.css'])
@stop

@section('js')
@stack('script')
@stop