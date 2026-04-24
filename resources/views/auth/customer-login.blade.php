@extends('adminlte::auth.login')

@section('auth_body')
<div class="container-fluid p-0">
    <div class="row no-gutters min-vh-100">

        {{-- IZQUIERDA --}}
        <div class="col-lg-5 d-flex align-items-center justify-content-center bg-white">
            <div class="w-100 px-4 porky-login-box">

                {{-- LOGO --}}
                <div class="text-center mb-5">
                    <img src="{{ asset('vendor/adminlte/dist/img/logo.png') }}"
                         class="mb-3"
                         style="max-height: 80px;">

                    <h4 class="font-weight-bold mb-1 text-danger">
                        Piqueteadero Porky de la 105
                    </h4>

                    <p class="text-muted small">
                        Acceso clientes
                    </p>
                </div>

                {{-- ERRORES --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- LOGIN CLIENTE --}}
                <form method="POST" action="{{ route('customer.login.post') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="email" name="email"
                               class="form-control"
                               placeholder="Correo electrónico"
                               required>
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="password"
                               class="form-control"
                               placeholder="Contraseña"
                               required>
                    </div>

                    <button class="btn btn-danger btn-block">
                        Ingresar
                    </button>
                </form>

                {{-- ACCIONES --}}
                <div class="mt-4 text-center porky-login-actions">

                    <div class="porky-divider my-3">
                        <span>o</span>
                    </div>

                    <a href="#" class="btn porky-register-btn btn-block">
                        Crear cuenta
                    </a>

                    <div class="mt-3">
                        <a href="{{ url('/login') }}" class="porky-link small">
                            Acceso administrativo
                        </a>
                    </div>

                </div>

            </div>
        </div>

        {{-- DERECHA --}}
        <div class="col-lg-7 d-none d-lg-block porky-image-panel">
            <div class="h-100 position-relative">

                <div class="porky-bg"
                     style="background-image: url('{{ asset('vendor/adminlte/dist/img/banner2.jpg') }}');">
                </div>

                <div class="porky-overlay"></div>

                <div class="porky-content d-flex align-items-center justify-content-center">
                    <div class="text-center text-white px-5">

                        <i class="fas fa-user fa-5x mb-4"></i>

                        <h1 class="font-weight-bold mb-3">
                            Bienvenido
                        </h1>

                        <p class="lead mb-4">
                            Realiza tus pedidos fácilmente<br>
                            y sin filas
                        </p>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
@push('css')
<style>

/* Layout */
.min-vh-100 {
    min-height: 100vh;
}

/* Caja login */
.porky-login-box {
    max-width: 420px;
}

/* PANEL DERECHO */
.porky-image-panel {
    overflow: hidden;
}

/* Imagen */
.porky-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    filter: contrast(1.1) brightness(.8);
    transition: transform .8s ease;
}

/* Overlay */
.porky-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to right,
        rgba(20, 10, 10, 0.95),
        rgba(198, 40, 40, 0.75)
    );
}

/* Contenido */
.porky-content {
    position: relative;
    z-index: 2;
}

/* Hover */
.porky-image-panel:hover .porky-bg {
    transform: scale(1.05);
}

/* Separador */
.porky-divider {
    display: flex;
    align-items: center;
    justify-content: center;
}

.porky-divider span {
    font-size: 0.75rem;
    color: #999;
    position: relative;
}

.porky-divider span::before,
.porky-divider span::after {
    content: "";
    width: 40px;
    height: 1px;
    background: #ddd;
    display: inline-block;
    margin: 0 10px;
}

/* Botón registro */
.porky-register-btn {
    background: linear-gradient(135deg, #ff7043, #d84315);
    color: #fff;
    border-radius: 8px;
    padding: 0.6rem;
    font-weight: 500;
    border: none;
    box-shadow: 0 4px 12px rgba(216,67,21,.3);
}

.porky-register-btn:hover {
    background: linear-gradient(135deg, #e64a19, #bf360c);
}

/* Ajuste AdminLTE */
.login-box {
    width: 100% !important;
    max-width: none !important;
    box-shadow: none !important;
}

.login-card-body {
    padding: 0 !important;
}

.card {
    border: none !important;
}

/* Responsive */
@media (max-width: 992px) {
    .porky-image-panel {
        display: none !important;
    }
}

</style>
@endpush