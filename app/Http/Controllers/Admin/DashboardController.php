<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            return Inertia::render('Admin/Dashboard/Index', [
                'menu' => 'dashboard',
                'title' => 'Dashboard',
            ]);
        } catch (\Throwable $th) {
            // ke error 500
            return Inertia::render('Errors/Error500', [
                'status' => false,
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'code' => $th->getCode(),
            ]);
        }
    }
}
