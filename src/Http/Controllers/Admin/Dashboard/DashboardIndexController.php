<?php

namespace Shopen\Http\Controllers\Admin\Dashboard;

use Inertia\Inertia;
use Shopen\Services\Admin\DashboardService;

class DashboardIndexController
{
    public function __construct(
        protected readonly DashboardService $dashboardService
    )
    {

    }

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();
        return Inertia::render('Admin/Dashboard/Index', $data);
    }
}