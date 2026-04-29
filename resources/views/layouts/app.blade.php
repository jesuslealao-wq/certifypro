<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CertifyPro - Sistema de Gestión')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap');

        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        #sidebar { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .collapsed { width: 80px !important; }
        .collapsed .nav-text { display: none; }

        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }

        .a4-landscape {
            aspect-ratio: 1.414 / 1;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .animate-fade-in { animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 flex h-screen overflow-hidden">

    <aside id="sidebar" class="w-64 bg-white border-r border-slate-200 flex flex-col">
        <div class="p-6 border-b border-slate-200">
            <h1 class="text-2xl font-bold text-blue-600">CertifyPro</h1>
            <p class="text-xs text-slate-500 mt-1">Sistema de Certificados</p>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="nav-text">Dashboard</span>
            </a>

            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-slate-400 uppercase">Contenido</p>
            </div>

            <a href="{{ route('cursos.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cursos.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span class="nav-text">Cursos</span>
            </a>

            <a href="{{ route('modulos.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('modulos.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="layers" class="w-5 h-5"></i>
                <span class="nav-text">Módulos</span>
            </a>

            <a href="{{ route('clases.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('clases.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span class="nav-text">Clases</span>
            </a>

            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-slate-400 uppercase">Personal</p>
            </div>

            <a href="{{ route('autoridades.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('autoridades.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="user-check" class="w-5 h-5"></i>
                <span class="nav-text">Autoridades</span>
            </a>

            <a href="{{ route('alumnos.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('alumnos.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="nav-text">Alumnos</span>
            </a>

            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-slate-400 uppercase">Ejecución</p>
            </div>

            <a href="{{ route('cohortes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cohortes.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span class="nav-text">Cohortes</span>
            </a>

            <a href="{{ route('certificados.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('certificados.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="award" class="w-5 h-5"></i>
                <span class="nav-text">Certificados</span>
            </a>

            <a href="{{ route('planillas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('planillas.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="file-code" class="w-5 h-5"></i>
                <span class="nav-text">Planillas</span>
            </a>

            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-slate-400 uppercase">Configuración</p>
            </div>

            <a href="{{ route('estatus.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('estatus.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span class="nav-text">Estatus</span>
            </a>

            <a href="{{ route('account.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('account.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="user" class="w-5 h-5"></i>
                <span class="nav-text">Mi Cuenta</span>
            </a>
        </nav>

        <!-- Logout section -->
        <div class="p-4 border-t border-slate-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-left text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="nav-text">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-sm text-slate-500 mt-1">@yield('page-description', '')</p>
                </div>
                <div class="flex items-center gap-4">
                    @if(session('success'))
                        <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-slate-50" id="view-port">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();

        function switchTab(viewId) {
            document.querySelectorAll('#view-port > section').forEach(section => {
                section.classList.add('hidden');
            });
            const target = document.getElementById('view-' + viewId);
            if(target) target.classList.remove('hidden');

            document.querySelectorAll('.nav-item').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-slate-600', 'hover:bg-slate-100');
            });
            event.target.closest('.nav-item').classList.add('bg-blue-600', 'text-white');
            event.target.closest('.nav-item').classList.remove('text-slate-600', 'hover:bg-slate-100');
        }
    </script>
    @stack('scripts')
</body>
</html>
