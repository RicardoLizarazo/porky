<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        h1 {
            font-size: 5rem;
            margin: 0;
            color: #f59e0b;
        }
        p {
            font-size: 1.2rem;
            margin-top: 0.5rem;
        }
        .redirect {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <h1>404</h1>
    <p>La página que buscas no existe.</p>
    <p class="redirect">Serás redirigido al inicio...</p>

    <script>
        setTimeout(() => window.location.href = "{{ route('home') }}", 2500);
    </script>
</body>
</html>
