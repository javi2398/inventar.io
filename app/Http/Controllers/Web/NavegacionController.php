<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use App\Http\Controllers\Controller;

class NavegacionController extends Controller
{
    // NAVEGACION
    public function create()
    {
        return Inertia::render('create');
    }

    public function edit()
    {
        return Inertia::render('edit');
    }
    
}

