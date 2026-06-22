@extends('adminlte::page')

@section('title', 'POS')

@section('content')
    <livewire:pos-order
        :order="$order"
    />
@stop

@section('css')
    @vite(['resources/css/app.css'])
@stop

@section('js')
    @stack('script')
@stop