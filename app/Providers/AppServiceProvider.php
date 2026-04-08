<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ejemplo 1: Una variable simple
        View::share('appName', 'CertifyPro System');

        // Ejemplo 2: Datos compartidos (como configuraciones de la DB)
        // Solo como ejemplo, podrías pasar el nombre de la empresa
        View::share('version', '1.0.2');
            View::composer('*', function ($view) {
        $view->with('navItems', [
    ['icon' => 'layout-dashboard', 'label' => 'Cursos', 'route' => 'cursos.index'],
    ['icon' => 'shield-check', 'label' => 'CertifyPro', 'route' => 'certifypro.index'],
]);
 
        $view->with('user', [
            'name' => 'Admin Usuario',
            'role' => 'Súper Usuario',
        ]);
 
        $view->with('footerStats', [
            ['icon' => 'database', 'label' => 'Sync Central: Activa'],
            ['icon' => 'cpu', 'label' => 'Engine Render: v5.2', 'class' => 'text-blue-500'],
        ]);
 
        $view->with('breadcrumb', $view->getData()['breadcrumb'] ?? ['Menu', 'Cursos']);
        $view->with('activeView', $view->getData()['activeView'] ?? 'dashboard');
    });

    }
}
