@extends('adminlte::page')

@section('title', 'Despacho Historial')

@section('content_header')
    <h1>
        <i class="fas fa-check-circle"></i>
        Despacho Historial
    </h1>
@stop

@section('content')

    <livewire:restaurante.kitchen-dispatch-history />

@stop

@section('css')
    @vite(['resources/css/app.css'])
    @stack('css')
@stop

@section('js')
    @stack('script')
@stop