<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->with('items')->latest()->limit(5)->get();

        return view('cabinet.dashboard', [
            'title' => 'Личный кабинет',
            'recentOrders' => $recentOrders,
        ]);
    }
}
