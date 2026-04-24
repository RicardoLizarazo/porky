@extends('adminlte::page')

@section('title', 'Menu Principal')

@section('content_header')
    <br>
@stop

@section('content_top_nav_right')
    @if(auth('customer')->check())
        <li class="nav-item">
            <form method="POST" action="{{ route('logout.any') }}">
                @csrf
                <button type="submit" class="nav-link btn btn-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </li>
    @endif
@endsection

@section('content')
    <livewire:menu></livewire:menu>
    <livewire:cart />
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