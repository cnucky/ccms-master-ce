<?php

namespace App\Http\Middleware\SPA;

use App\Http\Controllers\SPA\AdminAreaSPAController;

class AdminAreaAutoSPA extends AutoSPA
{
    protected function getSPAView()
    {
        return AdminAreaSPAController::view();
    }
}
