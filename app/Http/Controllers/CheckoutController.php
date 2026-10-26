<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }


        if (!$customer) {
            return redirect()
                ->route('customer.register')
                ->with('error', 'Data customer belum ditemukan.');
        }

        return view('checkout.index', compact('cart', 'total', 'customer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'delivery_date' => 'required|date|after_or_equal:today',
            'delivery_method' => 'required|in:pickup,delivery',
            'notes' => 'nullable|string',
        ]);    

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

         if (!$customer) {
            return back()
                ->with('error', 'Data customer tidak ditemukan.');
        }

        try {

            $order = DB::transaction(function () use ($validated, $cart, $customer) {

                /*
                |--------------------------------------------------------------------------
                | 1. perbarui customer
                |--------------------------------------------------------------------------
                */         

                $customer->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                ]);   

                /*
                |--------------------------------------------------------------------------
                | 2. Buat order
                |--------------------------------------------------------------------------
                */                

                $order = Order::create([
                    'order_code' => 'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999),                      
                    'customer_id' => $customer->id,
                    'order_date' => now()->toDateString(),
                    'delivery_date' => $validated['delivery_date'],
                    'delivery_method' => $validated['delivery_method'],
                    'delivery_fee' => 0,
                    'delivery_distance' => null,
                    'status' => 'Pending',
                    'total' => 0,
                    'notes' => $validated['notes'] ?? null,
                ]);

                $total = 0;

                /*
                |--------------------------------------------------------------------------
                | 3. Proses setiap paket di keranjang
                |--------------------------------------------------------------------------
                */

                foreach ($cart as $item) {

                    if (($item['type'] ?? null) !== 'package') {
                        throw new \Exception(
                            'Isi keranjang tidak valid.'
                        );
                    }

                    $package = Package::with('details')
                        ->lockForUpdate()
                        ->findOrFail($item['id']);

                    if ($package->status !== 'Tersedia') {
                        throw new \Exception(
                            "Paket {$package->name} sudah tidak tersedia."
                        );
                    }

                    $quantity = (int) $item['quantity'];

                    if ($quantity < 1) {
                        throw new \Exception(
                            "Jumlah paket {$package->name} tidak valid."
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 4. Simpan paket yang dibeli
                    |--------------------------------------------------------------------------
                    */

                    $subtotal = $package->price * $quantity;

                    $total += $subtotal;

                    OrderPackage::create([
                        'order_id' => $order->id,
                        'package_id' => $package->id,
                        'quantity' => $quantity,
                        'price' => $package->price,
                        'subtotal' => $subtotal,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | 5. Cek & kurangi stok menu berdasarkan isi paket
                    |--------------------------------------------------------------------------
                    */

                    foreach ($package->details as $detail) {

                        $menu = Menu::lockForUpdate()
                            ->findOrFail($detail->menus_id);

                        $requiredStock =
                            $detail->quantity * $quantity;

                        if ($menu->status !== 'Tersedia') {
                            throw new \Exception(
                                "Menu {$menu->name} tidak tersedia."
                            );
                        }

                        if ($menu->stock < $requiredStock) {
                            throw new \Exception(
                                "Stock {$menu->name} tidak mencukupi. " .
                                "Dibutuhkan {$requiredStock}, " .
                                "tersedia {$menu->stock}."
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Simpan penggunaan menu dalam order_details
                        |--------------------------------------------------------------------------
                        */

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'menu_id' => $menu->id,
                            'quantity' => $requiredStock,
                            'price' => $menu->price,
                            'subtotal' => $menu->price * $requiredStock,
                            'source' => 'package',
                        ]);

                        /*
                        |--------------------------------------------------------------------------
                        | Kurangi stok
                        |--------------------------------------------------------------------------
                        */

                        $menu->decrement(
                            'stock',
                            $requiredStock
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | 6. Simpan total order
                |--------------------------------------------------------------------------
                */

                $order->update([
                    'total' => $total,
                ]);

                return $order;
            });

            /*
            |--------------------------------------------------------------------------
            | 7. Kosongkan keranjang
            |--------------------------------------------------------------------------
            */

            session()->forget('cart');

            return redirect()
                ->route('checkout.success', $order)
                ->with(
                    'success',
                    'Pesanan berhasil dibuat.'
                );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function success(Order $order)
    {
         $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        abort_if(!$customer, 404);

        $order = $customer->orders()
            ->with([
                'customer',
                'packages.package',
                'details.menu',
            ])
        ->findOrFail($order->id);

        return view(
            'checkout.success',
            compact('order')
        );
    }
}