<?php

namespace App\Http\Middleware\SPA;

use App\Http\Controllers\SPA\AdminAreaSPAController;
use App\Http\Controllers\SPA\ClientAreaSPAController;

class ClientAreaAutoSPA extends AutoSPA
{
    protected function getSPAView()
    {
        return ClientAreaSPAController::view();
    }
}
