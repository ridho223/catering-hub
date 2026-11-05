<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    // ADMIN: semua jenis paket dalam satu halaman
    public function index(Request $request)
    {
        $type = $request->query('type', 'semua');

        $query = Package::with('details.menu')
            ->latest();

        if (in_array($type, ['paket', 'prasmanan'], true)) {
            $query->where('package_type', $type);
        } else {
            $type = 'semua';
        }

        $packages = $query->get();

        return view('packages.index', compact('packages', 'type'));
    }

    public function create()
    {
        $menus = Menu::with('category')
            ->where('status', 'Tersedia')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($menu) => $menu->category?->name ?? 'Tanpa Kategori');

        return view('packages.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'package_type' => 'required|in:paket,prasmanan',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Tersedia,Tidak Tersedia',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'menus' => 'required|array|min:1',
            'menus.*' => 'required|exists:menus,id',
        ]);

        $image = $request->hasFile('image')
            ? $request->file('image')->store('packages', 'public')
            : null;

        $package = Package::create([
            'name' => $validated['name'],
            'package_type' => $validated['package_type'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'image' => $image,
            'status' => $validated['status'],
        ]);

        foreach ($validated['menus'] as $menuId) {
            $package->details()->create([
                'menus_id' => $menuId,
                'quantity' => 1,
            ]);
        }

        return redirect()
            ->route('packages.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    public function show(Package $package)
    {
        $package->load('details.menu');

        return view('packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        $package->load('details');

        $menus = Menu::with('category')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($menu) => $menu->category?->name ?? 'Tanpa Kategori');

        $selectedMenus = $package->details
            ->pluck('menus_id')
            ->toArray();

        return view('packages.edit', compact(
            'package',
            'menus',
            'selectedMenus'
        ));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'package_type' => 'required|in:paket,prasmanan',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Tersedia,Tidak Tersedia',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'menus' => 'required|array|min:1',
            'menus.*' => 'required|exists:menus,id',
        ]);

        $package->name = $validated['name'];
        $package->package_type = $validated['package_type'];
        $package->description = $validated['description'] ?? null;
        $package->price = $validated['price'];
        $package->status = $validated['status'];

        if ($request->hasFile('image')) {
            if (
                $package->image &&
                Storage::disk('public')->exists($package->image)
            ) {
                Storage::disk('public')->delete($package->image);
            }

            $package->image = $request->file('image')
                ->store('packages', 'public');
        }

        $package->save();

        $package->details()->delete();

        foreach ($validated['menus'] as $menuId) {
            $package->details()->create([
                'menus_id' => $menuId,
                'quantity' => 1,
            ]);
        }

        return redirect()
            ->route('packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        if (
            $package->image &&
            Storage::disk('public')->exists($package->image)
        ) {
            Storage::disk('public')->delete($package->image);
        }

        $package->details()->delete();
        $package->delete();

        return redirect()
            ->route('packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    // CUSTOMER: daftar paket catering biasa
    public function customerIndex()
    {
        $packages = Package::with('details.menu')
            ->where('package_type', 'paket')
            ->where('status', 'Tersedia')
            ->latest()
            ->get();

        return view('packages.customer.index', compact('packages'));
    }

    // CUSTOMER: daftar paket prasmanan
    public function customerPrasmananIndex()
    {
        $packages = Package::with('details.menu')
            ->where('package_type', 'prasmanan')
            ->where('status', 'Tersedia')
            ->latest()
            ->get();

        return view('packages.customer.prasmanan', compact('packages'));
    }

    // CUSTOMER: detail paket catering biasa
    public function customerShow(Package $package)
    {
        abort_unless(
            $package->status === 'Tersedia'
            && $package->package_type === 'paket',
            404
        );

        $package->load('details.menu.category');

        return view('packages.customer.show', compact('package'));
    }

    // CUSTOMER: detail paket prasmanan
    public function customerPrasmananShow(Package $package)
    {
        abort_unless(
            $package->status === 'Tersedia'
            && $package->package_type === 'prasmanan',
            404
        );

        $package->load('details.menu.category');

        return view('packages.customer.show', compact('package'));
    }
}
