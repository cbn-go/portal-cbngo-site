<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial do portal institucional.
     */
    public function index(): View
    {
        return view('home');
    }
}
