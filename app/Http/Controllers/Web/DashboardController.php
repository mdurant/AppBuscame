<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load('profile');

        return view('dashboard.index', [
            'user' => $user,
        ]);
    }

    public function publications(): View
    {
        $user = auth()->user()->load('profile');

        return view('dashboard.placeholder', [
            'user' => $user,
            'title' => 'Mis Publicaciones',
            'description' => 'Publicar y gestionar tus avisos.',
        ]);
    }

    public function history(): View
    {
        $user = auth()->user()->load('profile');

        return view('dashboard.placeholder', [
            'user' => $user,
            'title' => 'Mi Historial',
            'description' => 'Tu historial de actividad.',
        ]);
    }

    public function messages(): View
    {
        $user = auth()->user()->load('profile');

        return view('dashboard.placeholder', [
            'user' => $user,
            'title' => 'Mis Mensajes',
            'description' => 'Conversaciones y mensajes.',
        ]);
    }

    public function payments(): View
    {
        $user = auth()->user()->load('profile');

        return view('dashboard.placeholder', [
            'user' => $user,
            'title' => 'Pagos e Historial',
            'description' => 'Suscripción, facturas e historial de pagos.',
        ]);
    }
}
