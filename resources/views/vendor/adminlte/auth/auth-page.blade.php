@extends('adminlte::master')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@endsection

@section('classes_body', 'hold-transition login-page')

@section('body')
    <div class="login-box">

        {{-- Contenido del login --}}
        <div class="card">
            <div class="card-body login-card-body">
                @yield('auth_body')
            </div>
        </div>

    </div>
@endsection

@section('adminlte_js')
    @stack('js')
    @yield('js')
@endsection