@extends('adminlte::page')

@section('title', 'Floor Map')

@section('content_header')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-border-all mr-2"></i>
                Mapa de mesas Operacional
            </h1>
        </div>
    </div>
</div>
@stop

@section('content')
    <livewire:floor-map />
@stop

@section('css')
    @vite(['resources/css/app.css'])
    @stack('css')
@stop

@section('js')
    @stack('script')
@stop