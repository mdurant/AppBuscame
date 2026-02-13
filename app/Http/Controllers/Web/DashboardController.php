<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Datos dummy para gráficos y KPIs del dashboard (reproducibles para demo).
     */
    private function getDashboardDummyData(): array
    {
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        return [
            'chart_properties_offered' => [
                'labels' => $months,
                'data' => [42, 58, 65, 78, 92, 105, 118, 134, 148, 165, 182, 198],
                'total' => 1285,
            ],
            'chart_property_searches' => [
                'labels' => $months,
                'data' => [320, 380, 410, 490, 560, 620, 710, 780, 850, 920, 990, 1050],
                'total' => 8080,
            ],
            'chart_rental_income' => [
                'labels' => $months,
                'data' => [12.5, 14.2, 15.8, 17.1, 18.5, 19.2, 20.1, 21.3, 22.0, 23.5, 24.2, 25.8],
                'total_percent' => 23.8,
            ],
            'chart_messages' => [
                'labels' => $months,
                'data' => [85, 102, 118, 135, 152, 168, 185, 202, 218, 235, 252, 268],
                'total' => 1920,
            ],
            'kpis' => [
                'properties_offered' => ['value' => 198, 'trend' => 12, 'label' => 'Propiedades ofertadas', 'suffix' => ''],
                'total_searches' => ['value' => 1050, 'trend' => 8, 'label' => 'Búsquedas (este mes)', 'suffix' => ''],
                'rental_income' => ['value' => 25.8, 'trend' => 5.2, 'label' => 'Ingresos por arriendo', 'suffix' => '%'],
                'messages_answered' => ['value' => 268, 'trend' => -3, 'label' => 'Mensajes contestados', 'suffix' => ''],
            ],
        ];
    }

    public function index(): View
    {
        $user = auth()->user()->load('profile');
        $dummy = $this->getDashboardDummyData();

        return view('dashboard.index', [
            'user' => $user,
            'chartData' => $dummy,
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
