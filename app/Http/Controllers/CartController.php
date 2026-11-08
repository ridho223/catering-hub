<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Samakan isi keranjang di sesi dengan data terbaru di database
     * (harga, nama, gambar, ketersediaan). Item yang sudah tidak ada/tidak tersedia dibuang.
     *
     * @return array{0: array, 1: array} [keranjang, daftar peringatan]
     */
    public static function syncCart(): array
    {
        $cart = session()->get('cart', []);
        $notes = [];
        $changed = false;

        foreach ($cart as $key => $item) {
            $package = isset($item['id']) ? Package::with('details.menu')->find($item['id']) : null;

            if (! $package || $package->status !== 'Tersedia') {
                $notes[] = ($item['name'] ?? 'Sebuah paket') . ' sudah tidak tersedia dan dihapus dari keranjang.';
                unset($cart[$key]);
                $changed = true;
                continue;
            }

            $max = $package->maxQuantity();
            $qty = (int) ($item['quantity'] ?? 1);

            if ($max < 1) {
                $notes[] = "Stok {$package->name} sedang habis, item dihapus dari keranjang.";
                unset($cart[$key]);
                $changed = true;
                continue;
            }

            if ($qty > $max) {
                $notes[] = "Jumlah {$package->name} disesuaikan menjadi {$max} porsi sesuai stok.";
                $qty = $max;
                $changed = true;
            }

            if ((float) $item['price'] !== (float) $package->price) {
                $changed = true;
            }

            $cart[$key] = [
                'type' => 'package',
                'id' => $package->id,
                'name' => $package->name,
                'price' => $package->price,
                'image' => $package->image,
                'quantity' => $qty,
            ];
        }

        if ($changed) {
            session()->put('cart', $cart);
        }

        return [$cart, $notes];
    }

    public function index()
    {
        [$cart, $notes] = self::syncCart();

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if ($notes) {
            session()->now('error', implode(' ', $notes));
        }

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, Package $package)
    {
        $maxQty = (int) config('shop.max_quantity', 99);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $maxQty,
            'redirect' => 'nullable|in:checkout,cart',
        ]);

        $package->load('details.menu');

        if (! $package->isOrderable()) {
            return back()->with('error', 'Paket sedang tidak tersedia atau stok menunya habis.');
        }

        $quantity = (int) $validated['quantity'];
        $cart = session()->get('cart', []);
        $key = 'package_' . $package->id;

        if (isset($cart[$key])) {
            $quantity += (int) $cart[$key]['quantity'];
        }

        $max = min($maxQty, $package->maxQuantity());

        if ($quantity > $max) {
            return back()->with('error', "Stok hanya cukup untuk {$max} porsi {$package->name}.");
        }

        $cart[$key] = [
            'type' => 'package',
            'id' => $package->id,
            'name' => $package->name,
            'price' => $package->price,
            'image' => $package->image,
            'quantity' => $quantity,
        ];

        session()->put('cart', $cart);

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index');
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Paket berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, string $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . (int) config('shop.max_quantity', 99),
        ]);

        $cart = session()->get('cart', []);

        if (! isset($cart[$key])) {
            return back()->with('error', 'Paket tidak ditemukan dikeranjang.');
        }

        $package = Package::with('details.menu')->find($cart[$key]['id']);

        if (! $package || $package->status !== 'Tersedia') {
            return back()->with('error', 'Paket sudah tidak tersedia');
        }

        $quantity = (int) $request->quantity;
        $max = $package->maxQuantity();

        if ($quantity > $max) {
            return back()->with('error', "Stok hanya cukup untuk {$max} porsi {$package->name}.");
        }

        $cart[$key]['quantity'] = $quantity;

        session()->put('cart', $cart);

        return back()->with('success', 'Keranjang berhasil diperbarui');
    }

    public function remove(string $key)
    {
        $cart = session()->get('cart', []);

        if (! isset($cart[$key])) {
            return back();
        }

        unset($cart[$key]);

        session()->put('cart', $cart);

        return back()->with('success', 'Paket berhasil dihapus dari keranjang');
    }

    public function clear()
    {
        session()->forget('cart');

        return back()->with('success', 'Keranjang dikosongkan.');
    }
}