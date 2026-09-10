<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        return view('dashboard.index',[
            'totalCategory' => Category::count(),
            'totalMenu' => Menu::count(),
        ]);
    }
}
