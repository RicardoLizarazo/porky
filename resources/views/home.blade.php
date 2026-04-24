@extends('adminlte::page')

@section('title', 'Inicio')

@section('content')
<div class="container-fluid py-4 forensic-dashboard">

    {{-- ENCABEZADO --}}
    <div class="row mb-5">
        <div class="col text-center">
            <h2 class="font-weight-bold mb-2 text-dark">
                Sistema de Información Porky
            </h2>
            <p class="text-muted">
                Gestión, análisis y trazabilidad de información
            </p>
        </div>
    </div>

    @php
        $modules = [];

        if (auth()->user()->can('users.view')) {
            $modules[] = [
                'icon' => 'fas fa-chart-line',
                'color' => 'forensic-warning',
                'title' => 'Reportes',
                'text' => 'Indicadores, reportes y documentos forenses.',
                'route' => route('users'),
            ];
        }

        if (auth()->user()->can('users.view')) {
            $modules[] = [
                'icon' => 'fas fa-user-shield',
                'color' => 'forensic-dark',
                'title' => 'Administración',
                'text' => 'Usuarios, roles, permisos y configuración.',
                'route' => route('users'),
            ];
        }
    @endphp

    {{-- MÓDULOS --}}
    <div class="row justify-content-center">

    </div>
</div>
@stop

@section('footer')
<div class="text-center small text-muted">
    <strong>Sistema de Información Porku</strong> — v1.0.0 <br>
    &copy; {{ date('Y') }} Todos los derechos reservados.
</div>
@stop
