<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::with('details.menu')
            ->latest()
            ->get();

        return view('packages.index',compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menus = Menu::where('status','Tersedia')
            ->orderBy('name')
            ->get();

        return view('packages.create', compact('menus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Tersedia,Tidak Tersedia',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'menus' => 'required|array|min:1',
            'menus.*' => 'exists:menus,id',
        ]);

        $image = null;

        if($request->hasFile('image')){
            $image = $request->file('image')->store('packages','public');
        }

        $package = Package::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'image' => $image,
            'status' => $validated['status'],
        ]);

        foreach ($validated['menus'] as $menuId){
            $package->details()->create([
                'menus_id' => $menuId,
                'quantity' => 1,
            ]);
        }

        return redirect()->route('packages.index')->with('success','Paket berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        $package->load('details.menu');

        return view('packages.show',compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        $package->load('details');

        $menus = Menu::orderBy('name')->get();

        $selectedMenus = $package->details
            ->pluck('menus_id')
            ->toArray();

        return view('packages.edit', compact(
            'package',
            'menus',
            'selectedMenus'        
        ));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Tersedia,Tidak Tersedia',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'menus' => 'required|array|min:1',
            'menus.*' => 'exists:menus,id',
        ]);

        $package->name = $validated['name'];
        $package->description = $validated['description'] ?? null;
        $package->price = $validated['price'];
        $package->status = $validated['status'];

        if($request->hasFile('image')){

            if(
                $package->image &&
                Storage::disk('public')->exists($package->image)
            ){
                Storage::disk('public')->delete($package->image);
            }

            $package->image = $request->file('image')->store('packages', 'public');
        }

        $package->save();

        // Hapus paket lama
        $package->details()->delete();

        // Masukan isi paket baru
        foreach($validated['menus'] as $menuId){
            $package->details()->create([
                'menus_id' => $menuId,
                'quantity' => 1,
            ]);
        }

        return redirect()->route('packages.index')->with('success','Paket berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        if(
            $package->image &&
            Storage::disk('public')->exists($package->image)
        ){
            Storage::disk('public')->delete($package->image);
        }

        $package->details()->delete();
        $package->delete();

        return redirect()->route('packages.index')->with('success','Paket berhasil dihapus');
    }

    public function customerIndex()
    {
        $packages = Package::with('details.menu')
            ->where('status','Tersedia')
            ->latest()
            ->get();

        return view('packages.customer.index',compact('packages'));
    }

    public function customerShow(Package $package)
    {
        if($package->status !== 'Tersedia'){
            abort(404);
        }

        $package->load('details.menu');

        return view('packages.customer.show',compact('package'));
    }
}
