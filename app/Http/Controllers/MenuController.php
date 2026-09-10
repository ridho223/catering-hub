<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::with('category')->latest()->get();
        return view('menu.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('menu.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id'=> 'required',
            'name'=> 'required',
            'price'=> 'required',
            'stock' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imageName = null;

        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/menu'),$imageName);
        }

        Menu::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'status' => $request->status,
            'image' => $imageName
        ]);

        return redirect()->route('menu.index')
            ->with('success','Menu berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('menu.edit', compact('menu','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name'=>'required',
            'category_id'=>'required',
            'price'=>'required|numeric',
            'stock'=>'required|numeric',
            'status'=>'required',
            'image'=>'nullable|image|mimes:jpg,jpeg,png|max:2048'        
        ]);

        $data = $request->except('image');

        if($request->hasFile('image')){
             if($menu->image && file_exists(public_path('uploads/menu/' . $menu->image))){
                unlink(public_path('uploads/menu/' . $menu->image));
             }

             $image = $request->file('image');
             $imageName = time() . '_' . $image->getClientOriginalName();
             $image->move(public_path('uploads/menu'), $imageName);

             $data['image'] = $imageName;
        }

        $menu->update($data);

        return redirect()
            ->route('menu.index')
            ->with('success','Menu berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        if($menu->image && file_exists(public_path('uploads/menu/' . $menu->image))){
            unlink(public_path('uploads/menu/' . $menu->image));
        }

        $menu->delete();

        return back()->with('success','Menu berhasil dihapus');

    }
}
