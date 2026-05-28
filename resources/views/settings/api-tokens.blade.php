<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Claves API | AgileTI</title>
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
            --success: #10b981;
            --danger: #ef4444;
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
            text-decoration: none;
        }

        .brand a {
            text-decoration: none;
            color: inherit;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .container {
            flex: 1;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            padding: 3rem 1.5rem;
        }

        .header {
            margin-bottom: 2.5rem;
        }

        .header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--text-muted);
        }

        .token-banner {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .token-banner h3 {
            color: #a7f3d0;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .token-banner p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .token-copy-box {
            display: flex;
            gap: 0.75rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            align-items: center;
        }

        .token-string {
            font-family: monospace;
            font-size: 1rem;
            color: #34d399;
            flex: 1;
            word-break: break-all;
        }

        .btn-copy {
            background: var(--primary);
            border: none;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-copy:hover {
            background: var(--primary-hover);
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            align-items: start;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 2rem;
            backdrop-filter: blur(8px);
        }

        .card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.75rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            color: var(--text-main);
            outline: none;
            transition: border-color 0.2s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
        }

        .permissions-title {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .permissions-list {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 1.5rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            cursor: pointer;
            color: var(--text-muted);
            user-select: none;
        }

        .checkbox-label input {
            accent-color: var(--primary);
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        .tokens-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .token-item {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .token-info h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .token-meta {
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .token-abilities {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .ability-badge {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .btn-revoke {
            background: transparent;
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            padding: 0.4rem 0.85rem;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-revoke:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .no-tokens {
            text-align: center;
            color: var(--text-muted);
            padding: 2rem 0;
            font-size: 0.95rem;
        }

        .footer {
            padding: 2rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            background: rgba(15, 23, 42, 0.3);
            margin-top: 3rem;
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="brand">
        <a href="/admin/dashboard"><h1>AgileTI Admin</h1></a>
    </div>
    <div class="nav-actions">
        <a href="/admin/dashboard" class="btn-back">&larr; Volver al Panel</a>
    </div>
</header>

<div class="container">
    <div class="header">
        <h2>Administración de Claves API</h2>
        <p>Genera y administra tokens de acceso personal de Laravel Sanctum</p>
    </div>

    <!-- Banner único de revelación del token generado -->
    @if (session('api_token'))
        <div class="token-banner">
            <h3><span>🎉</span> ¡Token de API Generado con Éxito!</h3>
            <p>Por razones de seguridad, este token solo se mostrará una vez. Asegúrate de copiarlo ahora mismo.</p>
            <div class="token-copy-box">
                <span class="token-string" id="tokenString">{{ session('api_token') }}</span>
                <button class="btn-copy" onclick="copyToken()">Copiar Clave</button>
            </div>
        </div>
    @endif

    <div class="section-grid">
        <!-- Crear Token -->
        <div class="card">
            <h3>Generar Nuevo Token</h3>
            <form method="POST" action="/settings/api-tokens">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre del Token</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Ej. Integración CRM" required autocomplete="off">
                </div>

                <div class="permissions-title">Habilidades / Permisos</div>
                <div class="permissions-list">
                    @foreach ($availablePermissions as $permission)
                        <label class="checkbox-label">
                            <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                                @if(in_array($permission, $defaultPermissions)) checked @endif>
                            {{ ucfirst($permission) }}
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="btn-submit">Crear Token</button>
            </form>
        </div>

        <!-- Listado de Tokens -->
        <div class="card">
            <h3>Tokens de Acceso Activos</h3>
            
            @if ($tokens->isEmpty())
                <div class="no-tokens">
                    No has generado ningún token de acceso de API aún.
                </div>
            @else
                <div class="tokens-list">
                    @foreach ($tokens as $token)
                        <div class="token-item">
                            <div class="token-info">
                                <h4>{{ $token->name }}</h4>
                                <div class="token-meta">
                                    <span>Creado: {{ $token->created_at->diffForHumans() }}</span>
                                    <span>Último uso: {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca usado' }}</span>
                                </div>
                                <div class="token-abilities">
                                    @foreach ($token->abilities as $ability)
                                        <span class="ability-badge">{{ $ability }}</span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <form method="POST" action="/settings/api-tokens/{{ $token->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-revoke" onclick="return confirm('¿Estás seguro de que deseas revocar este token?')">Revocar</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function copyToken() {
        const tokenText = document.getElementById('tokenString').innerText;
        navigator.clipboard.writeText(tokenText).then(() => {
            const btn = document.querySelector('.btn-copy');
            btn.innerText = '¡Copiado!';
            btn.style.backgroundColor = '#10b981';
            setTimeout(() => {
                btn.innerText = 'Copiar Clave';
                btn.style.backgroundColor = 'var(--primary)';
            }, 2000);
        });
    }
</script>

<footer class="footer">
    &copy; {{ date('Y') }} AgileTI. Entorno de Administración Exclusivo (SUPER).
</footer>

</body>
</html>
