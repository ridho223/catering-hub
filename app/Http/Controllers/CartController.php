<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request , int $id)
    {
        $menu = Menu::where('status','Tersedia')
            ->where('stock','>',0)
            ->findOrFail($id);

        $quantity = max(1, (int) $request->quantity);

        $cart = session()->get('cart',[]);

        if(isset($cart[$id])){
            $quantity += $cart[$id]['quantity'];
        }

        if($quantity > $menu->stock){
            return back()->with('error','Jumlah Melebihi stok menu.');
        }

        $cart[$id] = [
            'id' => $menu->id,
            'name' => $menu->name,
            'price' => $menu->price,
            'image' => $menu->image,
            'quantity' => $quantity,
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success','Menu berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart',[]);

        if(!isset($cart[$id])){
            return back();
        }

        $menu = Menu::findOrFail($id);

        if ($request->quantity > $menu->stock){
            return back()->with('error','Jumlah melebihi stok menu');
        }

        $cart[$id]['quantity'] = $request->quantity;

        session()->put('cart', $cart);

        return back()->with('success', 'Keranjang berhasil diperbarui');
    }

    public function remove( int $id)
    {
        $cart = session()->get('cart',[]);

        unset($cart[$id]);

        session()->put('cart',$cart);

        return back()->with('success','Menu dihapus dari keranjang');
    }

    public function clear()
    {
        session()->forget('cart');

        return back()->with('success','Keranjang dikosongkan.');
    }

 }
