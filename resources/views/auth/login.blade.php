@extends('adminlte::auth.login')

@section('auth_body')
<div class="container-fluid p-0">
    <div class="row no-gutters min-vh-100">

        {{-- PANEL IZQUIERDO: LOGIN --}}
        <div class="col-lg-5 d-flex align-items-center justify-content-center bg-white">
            <div class="w-100 px-4 porky-login-box">

                {{-- LOGO --}}
                <div class="text-center mb-5">
                    <img src="{{ asset('vendor/adminlte/dist/img/logo.png') }}"
                         alt="Porky 105"
                         class="mb-3"
                         style="max-height: 80px;">

                    <h4 class="font-weight-bold mb-1" style="color:#c62828;">
                        Piqueteadero Porky de la 105
                    </h4>

                    <p class="text-muted small">
                        Sistema de gestión del restaurante
                    </p>
                </div>

                {{-- FORM LOGIN --}}
                @parent

                {{-- ACCIONES USUARIO --}}
                <div class="mt-4 text-center porky-login-actions">

                    <div class="mb-2">
                        <a href="{{ route('password.request') }}" class="porky-link">
                            <i class="fas fa-key mr-1"></i>
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <div class="porky-divider my-3">
                        <span>o</span>
                    </div>

                    <div>
                        <a href="{{ route('register') }}" class="btn porky-register-btn">
                            <i class="fas fa-user-plus mr-1"></i>
                            Crear cuenta nueva
                        </a>
                    </div>

                </div>

            </div>
        </div>

        {{-- PANEL DERECHO: EXPERIENCIA VISUAL --}}
        <div class="col-lg-7 d-none d-lg-block porky-image-panel">
            <div class="h-100 position-relative">

                {{-- Imagen --}}
                <div class="porky-bg"
                     style="background-image: url('{{ asset('vendor/adminlte/dist/img/banner2.jpg') }}');">
                </div>

                {{-- Overlay --}}
                <div class="porky-overlay"></div>

                {{-- Contenido --}}
                <div class="porky-content d-flex align-items-center justify-content-center">
                    <div class="text-center text-white px-5">

                        <i class="fas fa-drumstick-bite fa-5x mb-4"></i>

                        <h1 class="font-weight-bold mb-3">
                            Sabor & Gestión
                        </h1>

                        <p class="lead mb-4">
                            Controla pedidos, cocina y ventas<br>
                            en tiempo real
                        </p>

                        <div class="porky-separator my-4">
                            <span></span>
                            <i class="fas fa-fire mx-3"></i>
                            <span></span>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <div class="mx-3 text-center">
                                <i class="fas fa-utensils fa-2x mb-2"></i>
                                <p class="small">Pedidos</p>
                            </div>
                            <div class="mx-3 text-center">
                                <i class="fas fa-concierge-bell fa-2x mb-2"></i>
                                <p class="small">Cocina</p>
                            </div>
                            <div class="mx-3 text-center">
                                <i class="fas fa-cash-register fa-2x mb-2"></i>
                                <p class="small">Ventas</p>
                            </div>
                        </div>

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

/* Hover imagen */
.porky-image-panel:hover .porky-bg {
    transform: scale(1.05);
}

/* Separador */
.porky-separator {
    display: flex;
    align-items: center;
    justify-content: center;
}

.porky-separator span {
    width: 80px;
    height: 1px;
    background: rgba(255,255,255,.4);
}

/* Iconos */
.fa-drumstick-bite,
.fa-fire {
    opacity: .95;
    text-shadow: 0 2px 6px rgba(0,0,0,.5);
}

/* LOGIN ACTIONS */
.porky-login-actions {
    font-size: 0.85rem;
}

/* Link recuperar */
.porky-link {
    color: #c62828;
    font-weight: 500;
    text-decoration: none;
    transition: all .2s ease;
}

.porky-link:hover {
    color: #8e0000;
    text-decoration: underline;
}

/* Divider */
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
    padding: 0.45rem 0.8rem;
    font-weight: 500;
    border: none;
    box-shadow: 0 4px 12px rgba(216,67,21,.3);
}

.porky-register-btn:hover {
    background: linear-gradient(135deg, #e64a19, #bf360c);
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 992px) {
    .porky-image-panel {
        display: none !important;
    }
}

/* Ajuste AdminLTE */
.login-box {
    width: 100% !important;
    max-width: none !important;
    box-shadow: none !important;
}

</style>
@endpush