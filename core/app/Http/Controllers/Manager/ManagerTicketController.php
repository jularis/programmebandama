<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;

class ManagerTicketController extends Controller {
    use SupportTicketManager;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->user         = auth()->user();
            $this->redirectLink = 'manager.ticket.view';
            $this->userType     = 'manager';
            $this->column       = 'user_id';
            return $next($request);
        });

    }
}
