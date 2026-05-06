@extends('adminlte::page')

@section('title', 'Mis Pedidos')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-users-cog mr-2"></i> Gesti&oacute;n de mis Pedidos
                </h1>
            </div>
        </div>
    </div>
@stop

@section('content_top_nav_right')
<ul class="navbar-nav ml-auto">
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
</ul>
@endsection

@section('content')
    <livewire:customerOrders></livewire:customerOrders>           
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
