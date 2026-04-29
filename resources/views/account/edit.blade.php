@extends('layouts.app')

@section('title', 'Editar Cuenta')
@section('page-title', 'Editar Cuenta')
@section('page-description', 'Actualiza tu información personal y contraseña')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8">
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-slate-800 mb-2">Configuración de Cuenta</h3>
            <p class="text-slate-600">Gestiona tu información personal y seguridad de la cuenta.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('account.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Sección Datos Básicos -->
            <div class="border-b border-slate-200 pb-8">
                <h4 class="text-lg font-medium text-slate-800 mb-4">Datos Básicos</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nombre de Usuario</label>
                        <input type="text" id="name" name="name" value="{{ old('name', data_get($user, 'name')) }}"
                               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', data_get($user, 'email')) }}"
                               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Guardar Datos Básicos
                    </button>
                </div>
            </div>
        </form>

        <form action="{{ route('account.password.update') }}" method="POST" class="space-y-8 mt-10">
            @csrf
            @method('PUT')

            <!-- Sección Contraseña -->
            <div class="">
                <h4 class="text-lg font-medium text-slate-800 mb-4">Cambiar Contraseña</h4>
                <p class="text-slate-600 mb-6">Ingresa tu contraseña actual y la nueva contraseña.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">Contraseña Actual</label>
                        <input type="password" id="current_password" name="current_password"
                               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-slate-700 mb-2">Nueva Contraseña</label>
                        <input type="password" id="new_password" name="new_password"
                               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('new_password') border-red-500 @enderror">
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirmar Nueva Contraseña</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('new_password_confirmation') border-red-500 @enderror">
                        @error('new_password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-slate-200">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Actualizar Contraseña
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection