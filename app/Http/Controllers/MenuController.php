<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\OrderDetail;
use App\Models\PackageDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Menu::with('category');

            // Cari berdasarkan nama atau deskripsi
            if ($request->filled('search')) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            // Filter kategori
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            // Filter status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $menus = $query
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();

            // Kategori untuk dropdown filter
            $categories = Category::orderBy('name')->get();

            // Statistik seluruh data menu, bukan hanya halaman aktif
            $availableCount = Menu::where('status', 'Tersedia')
                ->where('stock', '>', 0)
                ->count();

            $outOfStockCount = Menu::where(function ($q) {
                $q->where('stock', '<=', 0)
                ->orWhere('status', 'Habis');
            })->count();

            return view('menu.index', compact(
                'menus',
                'categories',
                'availableCount',
                'outOfStockCount'
            ));
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
            'category_id'=> 'required|exists:categories,id',
            'name'=> 'required|string|max:255',
            'price'=> 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            // harus sama dengan enum kolom menus.status dan pilihan di form
            'status' => 'required|in:Tersedia,Habis',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'price.integer' => 'Harga harus berupa angka bulat (tanpa titik/koma).',
        ]);

        $imageName = null;

        if($request->hasFile('image')){
            $imageName = $this->makeImageName($request->file('image'));
            $request->file('image')->move(public_path('uploads/menu'),$imageName);
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
            'name'=>'required|string|max:255',
            'category_id'=>'required|exists:categories,id',
            'price'=>'required|integer|min:0',
            'stock'=>'required|integer|min:0',
            'status'=>'required|in:Tersedia,Habis',
            'description'=>'nullable|string|max:1000',
            'image'=>'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ], [
            'price.integer' => 'Harga harus berupa angka bulat (tanpa titik/koma).',
        ]);

        // Hanya kolom yang memang boleh diubah (bukan seluruh isi request).
        $data = $request->only(['name', 'category_id', 'price', 'stock', 'status', 'description']);

        if($request->hasFile('image')){
             if($menu->image && file_exists(public_path('uploads/menu/' . $menu->image))){
                unlink(public_path('uploads/menu/' . $menu->image));
             }

             $image = $request->file('image');
             $imageName = $this->makeImageName($image);
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
        // Menu yang pernah dipesan atau masih jadi isi paket tidak boleh dihapus:
        // riwayat pesanan dan isi paket ikut rusak. Ubah status jadi Habis saja.
        if (OrderDetail::where('menu_id', $menu->id)->exists()) {
            return back()->with('error', 'Menu ini pernah dipesan sehingga tidak bisa dihapus (riwayat pesanan akan rusak). Ubah status menjadi Habis jika sudah tidak dijual.');
        }

        if (PackageDetails::where('menus_id', $menu->id)->exists()) {
            return back()->with('error', 'Menu ini masih menjadi isi paket. Keluarkan dari paket dulu sebelum dihapus.');
        }

        if($menu->image && file_exists(public_path('uploads/menu/' . $menu->image))){
            unlink(public_path('uploads/menu/' . $menu->image));
        }

        $menu->delete();

        return back()->with('success','Menu berhasil dihapus');

    }


    // Nama file acak dari ekstensi hasil deteksi isi file (bukan nama dari pengguna).
    private function makeImageName($file): string
    {
        return time() . '_' . Str::random(8) . '.' . $file->extension();
    }
}