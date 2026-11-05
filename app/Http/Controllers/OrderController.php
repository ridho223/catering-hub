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
            'customer_mode'        => 'required|in:existing,new',
            'customer_id'          => 'required_if:customer_mode,existing|nullable|exists:customers,id',
            'new_customer.name'    => 'required_if:customer_mode,new|nullable|string|max:255',
            'new_customer.phone'   => 'required_if:customer_mode,new|nullable|string|max:30',
            'new_customer.address' => 'nullable|string',

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


                if ($validated['customer_mode'] === 'new') {
                    // rapikan nomor ("0812 3456-789" => "08123456789") supaya tidak jadi pelanggan ganda
                    $phone = preg_replace('/[^\d+]/', '', $validated['new_customer']['phone']);
                
                    $customerId = Customer::firstOrCreate(
                        ['phone' => $phone],
                        [
                            'name'    => $validated['new_customer']['name'],
                            'address' => $validated['new_customer']['address'] ?? null,
                        ]
                    )->id;
                } else {
                    $customerId = $validated['customer_id'];
                }

                $order = Order::create([
                    'order_code' => $orderCode,
                    'customer_id' => $customerId,                  
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
        // Urutan alur: Pending -> Diproses -> Siap Diantar/Diambil -> Selesai
        $flow = ['Pending', 'Diproses', 'Siap_diambil_atau_diantar', 'Selesai'];
    
        $currentIdx = array_search($order->status, $flow, true);
        if ($currentIdx === false) {
            $currentIdx = 1;
        }
    
        // Pending: status berubah otomatis setelah pembayaran, jadi tidak bisa diubah manual di sini.
        $allowedStatuses = $currentIdx === 0
            ? []
            : array_slice($flow, $currentIdx);   // status sekarang + tahap berikutnya    
    
        $validated = $request->validate([
            'status' => $allowedStatuses
            ? ['required', Rule::in($allowedStatuses)]
            : ['nullable'],
            'order_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'packages' => 'required|array|min:1',
            'packages.*.package_id' => 'required|exists:packages,id',
            'packages.*.quantity' => 'required|integer|min:1',
        ]);

          $newStatus = $allowedStatuses ? $validated['status'] : $order->status;
          if ($newStatus === 'Selesai' && !$order->isFullyPaid()) {
                return back()->withInput()->with('error', 'Pesanan belum lunas. Minta customer melunasi atau catat pembayaran tunai dulu.');
            }

        try {
            DB::transaction(function () use ($validated, $order, $newStatus) {
                $lockedOrder = Order::whereKey($order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Kembalikan stok dari detail pesanan sebelumnya.
                $oldDetails = OrderDetail::where(
                    'order_id',
                    $lockedOrder->id
                )->get();

                foreach ($oldDetails as $detail) {
                    $menu = Menu::whereKey($detail->menu_id)
                        ->lockForUpdate()
                        ->first();

                    if ($menu) {
                        $menu->increment('stock', $detail->quantity);
                    }
                }

                $packageRows = [];
                $requiredStock = [];
                $subtotalPaket = 0;

                // Validasi paket dan hitung kebutuhan stok.
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

                    $packageRows[] = [
                        'package' => $package,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];

                    $subtotalPaket += $subtotal;

                    foreach ($package->details as $detail) {
                        $menuId = (int) $detail->menus_id;
                        $needed = (int) $detail->quantity * $quantity;

                        $requiredStock[$menuId] =
                            ($requiredStock[$menuId] ?? 0) + $needed;
                    }
                }

                // Kunci menu dan validasi stok gabungan.
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
                            "Salah satu menu paket sudah tidak tersedia."
                        );
                    }

                    if ($menu->stock < $needed) {
                        throw new \RuntimeException(
                            "Stok {$menu->name} tidak mencukupi. " .
                            "Dibutuhkan {$needed}, tersedia {$menu->stock}."
                        );
                    }
                }

                // Hapus detail pesanan lama setelah validasi lolos.
                OrderDetail::where('order_id', $lockedOrder->id)->delete();

                OrderPackage::where(
                    'order_id',
                    $lockedOrder->id
                )->delete();

                // Simpan paket dan detail menu bawaannya.
                foreach ($packageRows as $row) {
                    $package = $row['package'];
                    $quantity = $row['quantity'];

                    OrderPackage::create([
                        'order_id' => $lockedOrder->id,
                        'package_id' => $package->id,
                        'quantity' => $quantity,
                        'price' => $row['price'],
                        'subtotal' => $row['subtotal'],
                    ]);

                    foreach ($package->details as $detail) {
                        $menuId = (int) $detail->menus_id;
                        $menu = $lockedMenus->get($menuId);
                        $componentQuantity =
                            (int) $detail->quantity * $quantity;

                        OrderDetail::create([
                            'order_id' => $lockedOrder->id,
                            'menu_id' => $menu->id,
                            'quantity' => $componentQuantity,
                            'price' => $menu->price,
                            'subtotal' =>
                                (float) $menu->price * $componentQuantity,
                            'source' => 'package',
                        ]);
                    }
                }

                // Kurangi stok sesuai seluruh paket yang baru dipilih.
                foreach ($requiredStock as $menuId => $needed) {
                    $lockedMenus->get($menuId)->decrement(
                        'stock',
                        $needed
                    );
                }

                $deliveryFee = (float) ($lockedOrder->delivery_fee ?? 0);

                $lockedOrder->update([
                    'order_date' => $validated['order_date'],
                    'delivery_date' => $validated['delivery_date'],
                    'status' => $newStatus,
                    'notes' => $validated['notes'] ?? null,
                    'total' => $subtotalPaket + $deliveryFee,
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
