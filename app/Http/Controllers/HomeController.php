<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Package;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $package = Package::where('status', 'Tersedia')            
            ->latest()
            ->get();

        return view('home.index', compact('package'));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }
}
