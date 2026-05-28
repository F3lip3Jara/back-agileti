<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | AgileTI</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, var(--bg-dark));
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 5%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }

        .brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #a5b4fc, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-tag {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .header h2 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 900px;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.6);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .card h3 {
            font-size: 1.35rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-main);
        }

        .card p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .card-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--primary);
            transition: gap 0.2s ease;
        }

        .card:hover .card-link {
            gap: 0.75rem;
            color: #818cf8;
        }

        .footer {
            padding: 2rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            background: rgba(15, 23, 42, 0.3);
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="brand">
        <h1>AgileTI Admin</h1>
    </div>
    <div class="nav-actions">
        <span class="user-tag">Sesión: <strong>{{ Auth::user()->name }}</strong></span>
        <form method="POST" action="/admin/logout" style="display: inline;">
            @csrf
            <button type="submit" class="btn-logout">Cerrar Sesión</button>
        </form>
    </div>
</header>

<div class="container">
    <div class="header">
        <h2>Panel de Control Administrativo</h2>
        <p>Selecciona la herramienta del sistema a la que deseas acceder</p>
    </div>

    <div class="grid">
        <!-- Tarjeta 1: Telescope -->
        <a href="/telescope" class="card">
            <div>
                <div class="card-icon">🔭</div>
                <h3>Laravel Telescope</h3>
                <p>Monitorea en tiempo real las peticiones HTTP, consultas SQL, colas de trabajo, excepciones y rendimiento general del backend.</p>
            </div>
            <div class="card-link">
                Acceder a Telescope &rarr;
            </div>
        </a>

        <!-- Tarjeta 2: Keysmith -->
        <a href="/settings/api-tokens" class="card">
            <div>
                <div class="card-icon">🔑</div>
                <h3>Administración de Claves</h3>
                <p>Gestiona, genera y revoca tokens de acceso de API (Laravel Sanctum) para integraciones externas y seguridad del ecosistema.</p>
            </div>
            <div class="card-link">
                Administrar Tokens &rarr;
            </div>
        </a>
    </div>
</div>

<footer class="footer">
    &copy; {{ date('Y') }} AgileTI. Entorno de Administración Exclusivo (SUPER).
</footer>

</body>
</html>
