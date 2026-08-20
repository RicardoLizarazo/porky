<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porky — Cocina</title>

    {{-- Ajusta esta ruta a donde tengas compilado tu theme.css
         (el mismo que ya usa el panel admin, con las variables
         --brand-* que usan el Board y el Despacho). --}}
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Evita cualquier margen/scroll accidental en modo TV */
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
    </style>

    @livewireStyles

    {{-- Alpine.js explícito: el layout admin anterior probablemente lo
         cargaba por su cuenta, y este layout minimalista no lo traía,
         por eso la rotación de tarjetas no se activaba. Se carga ANTES
         de @livewireScripts para que Livewire lo detecte y lo use en
         vez de intentar cargar su propia copia (evita conflictos). --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>

    {{ $slot }}

    @livewireScripts

</body>
</html>