<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\Package;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        [$cart, $notes] = CartController::syncCart();

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', $notes ? implode(' ', $notes) : 'Keranjang masih kosong.');
        }

        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        if (! $customer) {
            return redirect()
                ->route('customer.register')
                ->with('error', 'Data customer belum ditemukan.');
        }

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if ($notes) {
            session()->now('error', implode(' ', $notes));
        }

        return view('checkout.index', compact('cart', 'total', 'customer'));
    }

    public function store(Request $request)
    {
        $request->merge(['phone' => Customer::normalizePhone($request->input('phone'))]);

        $minDate = now()->addDays((int) config('shop.min_lead_days', 0))->toDateString();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^0[0-9]{8,14}$/'],
            'address' => 'required|string|max:500',
            'delivery_date' => 'required|date|after_or_equal:' . $minDate . '|before_or_equal:' . now()->addYear()->toDateString(),
            'delivery_method' => 'required|in:pickup,delivery',
            'notes' => 'nullable|string|max:1000',
        ], [
            'phone.regex' => 'Nomor telepon tidak valid. Contoh: 081234567890.',
            'delivery_date.after_or_equal' => 'Tanggal kirim/ambil minimal ' . \Carbon\Carbon::parse($minDate)->translatedFormat('d F Y') . '.',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        if (! $customer) {
            return back()->with('error', 'Data customer tidak ditemukan.');
        }

        // Batasi pesanan Pending per customer (mencegah penimbunan stok).
        $maxPending = (int) config('shop.max_pending_orders', 3);

        if ($maxPending > 0 && $customer->orders()->where('status', 'Pending')->count() >= $maxPending) {
            return back()
                ->withInput()
                ->with('error', "Kamu masih punya {$maxPending} pesanan yang belum diproses. Selesaikan atau batalkan salah satunya dulu.");
        }

        // Cegah pesanan ganda karena klik dua kali / submit bersamaan.
        $dedupeKey = 'checkout:' . $account->id . ':' . md5(json_encode([$cart, $validated]));

        if (! Cache::add($dedupeKey, true, 20)) {
            return redirect()
                ->route('customer.orders.index')
                ->with('success', 'Pesananmu sedang/sudah diproses. Cek daftar pesanan di bawah.');
        }

        try {

            $order = DB::transaction(function () use ($validated, $cart, $customer) {

                // Email tidak diubah dari checkout: email akun = email login & tujuan verifikasi.
                $customer->update([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                ]);

                do {
                    $orderCode = 'ORD-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
                } while (Order::where('order_code', $orderCode)->exists());

                $order = Order::create([
                    'order_code' => $orderCode,
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

                // Stok tiap menu dikunci dan dibaca ulang dari database di setiap iterasi,
                // jadi menu yang dipakai beberapa paket tetap dihitung akurat.
                foreach ($cart as $item) {

                    if (($item['type'] ?? null) !== 'package') {
                        throw new \RuntimeException('Isi keranjang tidak valid.');
                    }

                    $package = Package::with('details')
                        ->lockForUpdate()
                        ->findOrFail($item['id']);

                    if ($package->status !== 'Tersedia') {
                        throw new \RuntimeException("Paket {$package->name} sudah tidak tersedia.");
                    }

                    $quantity = (int) $item['quantity'];

                    if ($quantity < 1 || $quantity > (int) config('shop.max_quantity', 99)) {
                        throw new \RuntimeException("Jumlah paket {$package->name} tidak valid.");
                    }

                    $subtotal = $package->price * $quantity;

                    $total += $subtotal;

                    OrderPackage::create([
                        'order_id' => $order->id,
                        'package_id' => $package->id,
                        'quantity' => $quantity,
                        'price' => $package->price,
                        'subtotal' => $subtotal,
                    ]);

                    foreach ($package->details as $detail) {

                        $menu = Menu::lockForUpdate()->findOrFail($detail->menus_id);

                        $requiredStock = $detail->quantity * $quantity;

                        if ($menu->status !== 'Tersedia') {
                            throw new \RuntimeException("Menu {$menu->name} tidak tersedia.");
                        }

                        if ($menu->stock < $requiredStock) {
                            throw new \RuntimeException(
                                "Stok {$menu->name} tidak mencukupi. Dibutuhkan {$requiredStock}, tersedia {$menu->stock}."
                            );
                        }

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'menu_id' => $menu->id,
                            'quantity' => $requiredStock,
                            'price' => $menu->price,
                            'subtotal' => $menu->price * $requiredStock,
                            'source' => 'package',
                        ]);

                        $menu->decrement('stock', $requiredStock);
                    }
                }

                $order->update(['total' => $total]);

                return $order;
            });

        } catch (ModelNotFoundException $e) {

            Cache::forget($dedupeKey);

            return back()
                ->withInput()
                ->with('error', 'Salah satu paket di keranjang sudah tidak ada. Perbarui keranjangmu.');

        } catch (\RuntimeException $e) {

            Cache::forget($dedupeKey);

            return back()->withInput()->with('error', $e->getMessage());

        } catch (\Throwable $e) {

            Cache::forget($dedupeKey);
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Pesanan gagal dibuat. Silakan coba lagi beberapa saat.');
        }

        // Pesanan sudah tersimpan. Bagian di bawah di luar try/catch.
        session()->forget('cart');

        app(\App\Services\AdminNotifier::class)->newOrder($order);

        if ($order->delivery_method === 'pickup') {
            return redirect()->route('customer.payment.create', $order->id)
                ->with('success', 'Pesanan dibuat. silakan lakukan pembayaran.');
        }

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Pesanan dibuat. Admin akan mengonfirmasi ongkir, setelah itu anda bisa membayar.');
    }

    public function success(Order $order)
    {
        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        abort_if(! $customer, 404);

        $order = $customer->orders()
            ->with([
                'customer',
                'packages.package',
                'details.menu',
                'payments',
            ])
            ->findOrFail($order->id);

        return view('checkout.success', compact('order'));
    }
}