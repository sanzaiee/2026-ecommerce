<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Admin\Services\DashboardService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->dashboard->stats(),
            'analytics' => $this->dashboard->analytics(),
        ]);
    }
}
