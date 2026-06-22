@extends('adminlte::page')

@section('title', 'Despacho Cocina')

@section('content_header')
    <h1>
        <i class="fas fa-check-circle"></i>
        Despacho Cocina
    </h1>
@stop

@section('content')

    <livewire:restaurante.kitchen-dispatch />

@stop

@section('css')
    @vite(['resources/css/app.css'])
    @stack('css')
@stop

@section('js')
    @stack('script')
@stop