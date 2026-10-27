<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\Package;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('customer')
            ->latest('order_date')->get();

        return view('order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        $packages = Package::where('status', 'Tersedia')
            ->with('details.menu')
            ->orderBy('name')
            ->get()
            ->filter(function ($package) {
                return $package->details->isNotEmpty()
                    && $package->details->every(function ($detail) {
                        return $detail->menu
                            && $detail->menu->status === 'Tersedia'
                            && $detail->menu->stock > 0;
                    });
            })
            ->values();

        return view('order.create', compact('customers', 'packages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:order_date',
            'notes' => 'nullable|string',

            'packages' => 'required|array|min:1',
            'packages.*.package_id' => 'required|exists:packages,id',
            'packages.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                $subtotalPaket = 0;
                $packageRows = [];
                $requiredStock = [];

                foreach ($validated['packages'] as $item) {
                    $package = Package::where('status', 'Tersedia')
                        ->with('details.menu')
                        ->lockForUpdate()
                        ->findOrFail($item['package_id']);

                    if ($package->details->isEmpty()) {
                        throw new \RuntimeException(
                            "Paket {$package->name} belum memiliki menu."
                        );
                    }

                    $quantity = (int) $item['quantity'];
                    $price = (float) $package->price;
                    $subtotal = $price * $quantity;

                    $subtotalPaket += $subtotal;

                    $packageRows[] = [
                        'package' => $package,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];

                    foreach ($package->details as $detail) {
                        $menuId = (int) (
                            $detail->menus_id
                            ?? $detail->menu?->id
                            ?? 0
                        );

                        if ($menuId <= 0) {
                            throw new \RuntimeException(
                                "Menu dalam paket {$package->name} tidak valid."
                            );
                        }

                        $needed = (int) $detail->quantity * $quantity;

                        $requiredStock[$menuId] =
                            ($requiredStock[$menuId] ?? 0) + $needed;
                    }
                }

                $lockedMenus = Menu::whereIn(
                    'id',
                    array_keys($requiredStock)
                )
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($requiredStock as $menuId => $needed) {
                    $menu = $lockedMenus->get($menuId);

                    if (!$menu || $menu->status !== 'Tersedia') {
                        throw new \RuntimeException(
                            "Menu ID {$menuId} sudah tidak tersedia."
                        );
                    }

                    if ($menu->stock < $needed) {
                        throw new \RuntimeException(
                            "Stok {$menu->name} tidak mencukupi. "
                            . "Dibutuhkan {$needed}, tersedia {$menu->stock}."
                        );
                    }
                }

                $deliveryFee = 0;

                do {
                    $orderCode = 'ORD-' . now()->format('ymd') . '-'
                        . strtoupper(Str::random(5));
                } while (Order::where('order_code', $orderCode)->exists());

                $order = Order::create([
                    'order_code' => $orderCode,
                    'customer_id' => $validated['customer_id'],
                    'order_type' => 'paket',
                    'order_date' => $validated['order_date'],
                    'delivery_date' => $validated['delivery_date'],
                    'status' => 'Pending',
                    'total' => $subtotalPaket + $deliveryFee,
                    'notes' => $validated['notes'] ?? null,
                    'delivery_method' => 'pickup',
                    'delivery_distance' => null,
                    'delivery_fee' => $deliveryFee,
                ]);

                foreach ($packageRows as $row) {
                    $package = $row['package'];
                    $quantity = $row['quantity'];

                    OrderPackage::create([
                        'order_id' => $order->id,
                        'package_id' => $package->id,
                        'quantity' => $quantity,
                        'price' => $row['price'],
                        'subtotal' => $row['subtotal'],
                    ]);

                    foreach ($package->details as $detail) {
                        $menuId = (int) (
                            $detail->menus_id
                            ?? $detail->menu?->id
                            ?? 0
                        );

                        $menu = $lockedMenus->get($menuId);

                        if (!$menu) {
                            throw new \RuntimeException(
                                "Menu ID {$menuId} tidak ditemukan."
                            );
                        }

                        $componentQuantity =
                            (int) $detail->quantity * $quantity;

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'menu_id' => $menu->id,
                            'quantity' => $componentQuantity,
                            'price' => $menu->price,
                            'subtotal' =>
                                (float) $menu->price * $componentQuantity,
                            'source' => 'package',
                        ]);
                    }
                }

                foreach ($requiredStock as $menuId => $needed) {
                    $lockedMenus->get($menuId)->decrement('stock', $needed);
                }

                return $order;
            });

            return redirect()
                ->route('orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'Pesanan gagal dibuat. Periksa kembali data dan stok menu.'
                );
        }
    }            

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
            $order->load([
                'customer',
                'packages.package.details.menu',
                'details.menu',
                'payment',                
            ]);

        return view('order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load([
            'customer',
            'packages.package.details.menu',
            'details.menu',
            'payment',      
        ]);

        $customers = Customer::orderBy('name')->get();

        $curentMenuIds = $order->details->pluck('menu_id')->filter()->
                unique()->values()->all();

        $menus = Menu::with('category')
            ->where(function ($query) use ($curentMenuIds){
            $query->where(function ($subQuery){
                $subQuery->where('status', 'Tersedia')->where('stock', '>', 0);
            });

            if (!empty($curentMenuIds)){
                $query->orWhereIn('id', $curentMenuIds);
            }
        })->orderBy('name')->get();

        $packages = Package::where('status', 'Tersedia')->with('details.menu')->orderBy('name')->get();

        return view('order.edit',compact('order','customers','menus', 'packages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',

            'order_type' => 'required|in:paket,paket_custom,custom',

            'status' => 'required|in:Pending,Diproses,Selesai',

            'order_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:order_date',

            'notes' => 'nullable|string',

            'packages' => 'nullable|array',
            'packages.*.package_id' => 'required|exists:packages,id',
            'packages.*.quantity' => 'required|integer|min:1',
            'packages.*.price' => 'required|numeric|min:0',

            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        
            'replacements' => 'nullable|array',
            'replacements.*' => 'nullable|array',
            'replacements.*.*' => 'nullable|exists:menus,id',
        ]);

        $packageItems = $validated['packages'] ?? [];
        $menuItems = $validated['items'] ?? [];
        $replacementItems = $request->input('replacements', []);
        

        // Pastikan kombinasi paket dan menu sesuai jenis pesanan.
        if (
            $validated['order_type'] === 'paket'
            && (empty($packageItems) || !empty($menuItems))
        ) {
            return back()
                ->withInput()
                ->with('error', 'Pesanan paket harus berisi paket tanpa menu custom.');
        }

        if (
            $validated['order_type'] === 'paket_custom'
            && (empty($packageItems) || empty($menuItems))
        ) {
            return back()
                ->withInput()
                ->with('error', 'Pesanan paket + custom harus berisi paket dan menu custom.');
        }

        if (
            $validated['order_type'] === 'custom'
            && (!empty($packageItems) || empty($menuItems))
        ) {
            return back()
                ->withInput()
                ->with('error', 'Pesanan custom harus berisi menu tanpa paket.');
        }

        if($validated['order_type'] !== 'paket_custom'){
            $replacementItems = [];
        }

             try {
            DB::transaction(function () use (
                $order,
                $validated,
                $packageItems,
                $menuItems,
                $replacementItems
            ) {

        $lockedOrder = Order::whereKey($order->id)->lockForUpdate()
            ->firstOrFail();         

        // Jika admin memilih menu baru, gunakan pilihan tersebut.
        // Jika dropdown kosong, pertahankan menu yang sudah digunakan.
        $resolveReplacement = function (
            $packageIndex,
            $originalMenuId
        ) use ($validated, $replacementItems) {
            if ($validated['order_type'] !== 'paket_custom') {
                return null;
            }

            $replacementId =
                $replacementItems[$packageIndex][$originalMenuId] ?? null;

            if (!filled($replacementId)) {
                return null;
            }

            $replacementId = (int) $replacementId;

            // Jika yang dipilih kembali adalah menu bawaan,
            // berarti penggantian dibatalkan.
            return $replacementId === (int) $originalMenuId
                ? null
                : $replacementId;
        };

        // 1. Kembalikan stok seluruh detail lama, baik paket maupun menu custom.
        $oldDetails = OrderDetail::where('order_id', $lockedOrder->id)->get();

        foreach ($oldDetails as $detail) {
            $menu = Menu::whereKey($detail->menu_id)
                ->lockForUpdate()
                ->first();

            if ($menu) {
                $menu->increment('stock', $detail->quantity);
            }
        }

        // 2. Ambil paket dan komponen paket yang dipilih admin.
        $subtotalPaket = 0;
        $packageRows = [];
        $requiredStock = [];

        foreach ($packageItems as $packageIndex => $item) {

            $package = Package::with('details.menu')
                ->where('status', 'Tersedia')
                ->lockForUpdate()
                ->findOrFail($item['package_id']);

            $quantity = (int) $item['quantity'];
            $price = (float) $item['price'];

            $packageRows[] = [
                'index' => $packageIndex,
                'package' => $package,
                'package_id' => $package->id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $price * $quantity,
            ];

            $subtotalPaket += $price * $quantity;

            foreach ($package->details as $packageDetail) {
                $originalMenuId = (int) $packageDetail->menus_id;

                $replacementId = $resolveReplacement(
                    $packageIndex,
                    $originalMenuId
                );

                $menuId = $replacementId ?? $originalMenuId;

                $needed = (int) $packageDetail->quantity * $quantity;

                $requiredStock[$menuId] =
                    ($requiredStock[$menuId] ?? 0) + $needed;
            }
        }

        // 3. Tambahkan kebutuhan stok dari menu custom/tambahan.
        foreach ($menuItems as $item) {
            $menuId = (int) $item['menu_id'];
            $quantity = (int) $item['quantity'];

            $requiredStock[$menuId] =
                ($requiredStock[$menuId] ?? 0) + $quantity;
        }

        // 4. Kunci dan validasi stok gabungan.
        $lockedMenus = [];

        foreach ($requiredStock as $menuId => $quantity) {
            $menu = Menu::whereKey($menuId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($menu->status !== 'Tersedia') {
                throw new \RuntimeException(
                    "Menu {$menu->name} sudah tidak tersedia."
                );
            }

            if ($menu->stock < $quantity) {
                throw new \RuntimeException(
                    "Stok menu {$menu->name} tidak mencukupi. " .
                    "Dibutuhkan {$quantity}, tersedia {$menu->stock}."
                );
            }

            $lockedMenus[$menuId] = $menu;
        }

        // 5. Hitung menu custom. Harga komponen pengganti tidak ditambahkan
        // lagi ke total, karena sudah termasuk harga paket.
        $subtotalMenu = 0;
        $menuRows = [];

        foreach ($menuItems as $item) {
            $menu = $lockedMenus[(int) $item['menu_id']];
            $quantity = (int) $item['quantity'];
            $price = (float) $menu->price;

            $menuRows[] = [
                'menu' => $menu,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $price * $quantity,
            ];

            $subtotalMenu += $price * $quantity;
        }

        // 6. Simpan kembali daftar paket.
        OrderPackage::where('order_id', $lockedOrder->id)->delete();

        foreach ($packageRows as $row) {
            OrderPackage::create([
                'order_id' => $lockedOrder->id,
                'package_id' => $row['package_id'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'subtotal' => $row['subtotal'],
            ]);
        }

        


        // 7. Buat ulang detail komponen paket dan menu custom.
        OrderDetail::where('order_id', $lockedOrder->id)
            ->whereIn('source', ['package', 'custom'])
            ->delete();

        // Simpan komponen paket beserta menu penggantinya.
        foreach ($packageRows as $row) {
            foreach ($row['package']->details as $packageDetail) {

                // Ambil ID menu bawaan dari komponen paket.
                $originalMenuId = (int) $packageDetail->menus_id;

                // Ambil menu pengganti berdasarkan indeks paket DAN menu bawaan.
                $replacementId = $resolveReplacement(
                    $row['index'],
                    $originalMenuId
                );

                // Jika tidak diganti, gunakan menu bawaan.
                $menuId = $replacementId ?? $originalMenuId;

                if (!isset($lockedMenus[$menuId])) {
                    throw new \RuntimeException(
                        "Menu ID {$menuId} tidak ditemukan dalam validasi stok."
                    );
                }

                $menu = $lockedMenus[$menuId];

                $quantity = (int) $packageDetail->quantity
                    * (int) $row['quantity'];

                OrderDetail::create([
                    'order_id' => $lockedOrder->id,
                    'menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'price' => $menu->price,
                    'subtotal' => $menu->price * $quantity,
                    'source' => 'package',
                ]);
            }
        }

        // Simpan menu tambahan secara terpisah.
        foreach ($menuRows as $row) {
            OrderDetail::create([
                'order_id' => $lockedOrder->id,
                'menu_id' => $row['menu']->id,
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'subtotal' => $row['subtotal'],
                'source' => 'custom',
            ]);
        }

        // 9. Kurangi stok berdasarkan semua komponen paket dan menu custom.
        foreach ($requiredStock as $menuId => $quantity) {
            $lockedMenus[$menuId]->decrement('stock', $quantity);
        }
    
        // 10. Hitung total pesanan terbaru.
       // Pertahankan ongkir yang sudah diatur dari halaman detail pesanan.
            $deliveryFee = (float) ($lockedOrder->delivery_fee ?? 0);

            $total = $subtotalPaket + $subtotalMenu + $deliveryFee;

            // Status pesanan diatur melalui form edit pesanan,
            // bukan ditentukan dari nominal pembayaran.
            $lockedOrder->update([
                'customer_id' => $validated['customer_id'],
                'order_type' => $validated['order_type'],
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'total' => $total,
            ]);
        });

            return redirect()
                ->route('orders.show', $order->id)
                ->with('success', 'Pesanan berhasil diperbarui.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'Pesanan gagal diperbarui. Periksa kembali data dan stok menu.'
                );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order){
            foreach ($order->details as $detail){
                $menu = Menu::find($detail->menu_id);

                if($menu){
                    $menu->increment('stock', $detail->quantity);
                }
            }

            $order->details()->delete();

            $order->delete();

        });

        return redirect()->route('orders.index')->with('success','Pesanan berhasil dihapus dan stok dikembalikan');
    }


    public function updateShipping(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_distance' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
        ]);

        $method = $validated['delivery_method'];

        if ($method === 'pickup') {
            $distance = null;
            $fee = 0;
        } else {
            $distance = (float) ($validated['delivery_distance'] ?? 0);

            if ($distance > 5 && !isset($validated['delivery_fee'])) {
                return back()
                    ->withErrors([
                        'delivery_fee' =>
                            'Ongkir wajib diisi untuk jarak di atas 5 km.',
                    ])
                    ->withInput();
            }

            // Jarak sampai 5 km gratis.
            $fee = $distance <= 5
                ? 0
                : (float) $validated['delivery_fee'];

            if ($distance > 5 && $fee < 1) {
                return back()
                    ->withErrors([
                        'delivery_fee' =>
                            'Ongkir untuk jarak di atas 5 km harus lebih dari Rp0.',
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($order, $method, $distance, $fee) {
            $lockedOrder = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Paket sudah memiliki subtotal sendiri.
            $subtotalPaket = OrderPackage::where('order_id', $lockedOrder->id)
                ->sum('subtotal');

            // Hanya menu tambahan/custom yang dijumlahkan di sini.
            // Komponen menu bawaan paket tidak dihitung dua kali.
            $subtotalMenu = OrderDetail::where('order_id', $lockedOrder->id)
                ->where('source', 'custom')
                ->sum('subtotal');

            $lockedOrder->update([
                'delivery_method' => $method,
                'delivery_distance' => $distance,
                'delivery_fee' => $fee,
                'total' => $subtotalPaket + $subtotalMenu + $fee,
            ]);
        });

        return redirect()
            ->route('orders.show', $order->id)
            ->with('success', 'Informasi pengiriman berhasil diperbarui.');
    }
}
