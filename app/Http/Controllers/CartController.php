<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Package;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        $total = 0;

        foreach ($cart as $item){
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart','total'));
    }


    public function add(Request $request , Package $package)
    {
        if($package->status !== 'Tersedia'){
            return back()->with('error','Paket sudah tidak tersedia');
        }

        $quantity =(int) $request->quantity;
    
        if($quantity < 1){
            return back()->with('error','Jumlah porsi minimal 1.');
            }    
            
        $cart = session()->get('cart',[]);

        $key = 'package_' . $package->id;

        if(isset($cart[$key])){
            $quantity += $cart[$key]['quantity'];
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

        if($request->input('redirect') === 'checkout'){
            return redirect()->route('checkout.index');
        }

        if($request->input('redirect') === 'custom'){
            return redirect()->route('customer.custom.create', [
                'include_packages' => 1,
                'package_id' => $package->id,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success','Menu berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, string $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart',[]);

        if(!isset($cart[$key])){
            return back()->with('error','Paket tidak ditemukan dikeranjang.');
        }

        $package = Package::find($cart[$key]['id']);

        if(!$package || $package->status !== 'Tersedia'){
            return back()->with('error','Paket sudah tidak tersedia');
        }

        $cart[$key]['quantity'] = (int) $request->quantity;

        session()->put('cart', $cart);

        return back()->with('success', 'Keranjang berhasil diperbarui');
    }

    public function remove( string $key)
    {
        $cart = session()->get('cart',[]);

        if (!isset($cart[$key])){
            return back();
        }        

        unset($cart[$key]);

        session()->put('cart',$cart);

        return back()->with('success','Paket berhasil dihapus dari keranjang');
    }

    public function clear()
    {
        session()->forget('cart');

        return back()->with('success','Keranjang dikosongkan.');
    }

 }
