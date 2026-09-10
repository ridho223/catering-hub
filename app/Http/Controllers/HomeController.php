<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $menus = Menu::where('status', 'Tersedia')
            ->where('stock', '>', 0)
            ->orderBy('name')->get();

        return view('home.index', compact('menus'));
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
