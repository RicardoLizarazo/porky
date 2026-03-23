@extends('adminlte::page')

@section('title', 'Inicio')

@section('content')
<div class="container-fluid py-4 forensic-dashboard">

    {{-- ENCABEZADO --}}
    <div class="row mb-5">
        <div class="col text-center">
            <h2 class="font-weight-bold mb-2 text-dark">
                Sistema de Información Forense
            </h2>
            <p class="text-muted">
                Gestión, análisis y trazabilidad de información forense institucional
            </p>
        </div>
    </div>

    @php
        $modules = [];

        if (auth()->user()->can('specialists.view')) {
            $modules[] = [
                'icon' => 'fas fa-users',
                'color' => 'forensic-primary',
                'title' => 'Personas',
                'text' => 'Gestión de colaboradores, peritos y registros asociados.',
                'route' => route('specialists'),
            ];
        }

        if (auth()->user()->can('consultations.view')) {
            $modules[] = [
                'icon' => 'fas fa-file-signature',
                'color' => 'forensic-success',
                'title' => 'Procesos',
                'text' => 'Seguimiento de solicitudes y flujos de validación.',
                'route' => route('consultations'),
            ];
        }

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
        @forelse ($modules as $module)
            <div class="col-sm-6 col-md-4 col-lg-3 mb-4 d-flex">
                <div class="card forensic-card w-100">

                    <div class="card-body text-center d-flex flex-column">

                        <div class="forensic-icon {{ $module['color'] }} mb-3">
                            <i class="{{ $module['icon'] }}"></i>
                        </div>

                        <h5 class="font-weight-bold mb-2">
                            {{ $module['title'] }}
                        </h5>

                        <p class="text-muted small mb-4">
                            {{ $module['text'] }}
                        </p>

                        <a href="{{ $module['route'] }}"
                           class="btn btn-outline-dark btn-sm mt-auto">
                            Acceder
                        </a>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    No tienes permisos asignados para acceder a los módulos del sistema.
                </div>
            </div>
        @endforelse
    </div>
</div>
@stop

@section('footer')
<div class="text-center small text-muted">
    <strong>Sistema de Información Forense</strong> — v1.0.0 <br>
    &copy; {{ date('Y') }} Todos los derechos reservados.
</div>
@stop
