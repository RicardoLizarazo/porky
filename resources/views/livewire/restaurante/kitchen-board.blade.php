@extends('adminlte::page')

@section('title', 'Monitor Cocina')

@section('content_header')
    <h1>
        <i class="fas fa-fire"></i>
        Monitor Cocina
    </h1>
@stop

@section('content')

    <livewire:restaurante.kitchen-board :station="$station" />

@stop

@section('css')
    @vite(['resources/css/app.css'])
    @stack('css')
@stop

@section('js')
    @stack('script')
@stop   