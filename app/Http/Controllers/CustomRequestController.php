<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestDetail;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\Package;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomRequestController extends Controller
{
    public function create()
    {
        $categories = Category::with([
            'menus' => function ($query) {
                $query->where('status', 'Tersedia')
                    ->where('stock', '>', 0)
                    ->orderBy('name');
            }
        ])->orderBy('name')->get();

        $includePackages = request()->boolean('include_packages');
        $packageId = request()->integer('package_id');
        $quantity = max(1, request()->integer('quantity', 1));

        $package = null;

        if($includePackages){
            $package = Package::with('details.menu')
                ->where('status', 'Tersedia')->findOrFail($packageId);

            $cart = session()->get('cart',[]);
                           
            $cartPackages = collect($cart)->filter(fn($item) => ($item['type'] === 'package')
            )->values();

            $quantity = (int) (
                $cart['packages_'. $package->id]['quantity'] ?? $quantity
            );
        } else {
            $cartPackages = collect();
        }   

        return view('customer.custom.create', compact('categories', 'includePackages','cartPackages', 'package', 'quantity'));
    }



    public function store(Request $request)
    {
        $includePackages = $request->boolean('include_packages');
        $account = Auth::guard('customer')->user();
        $customer = $account?->customer;

        if (!$customer) {
            abort(403, 'Data customer tidak ditemukan.');
        }

        // Validasi dasar untuk kedua mode.
        $rules = [
            'description' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'address' => 'required|string|max:2000',
            'menus' => 'nullable|array',
            'menus.*.menu_id' => 'required_with:menus.*.quantity|exists:menus,id',
            'menus.*.quantity' => 'nullable|integer|min:0',
            'replacements' => 'nullable|array',
            'replacements.*' => 'nullable|exists:menus,id',
        ];

        if ($includePackages) {
            $rules['package_id'] = 'required|exists:packages,id';
            $rules['quantity'] = 'required|integer|min:1';
        } else {
            $rules['title'] = 'required|string|max:255';
            $rules['guest_count'] = 'required|integer|min:1';
            $rules['estimated_budget'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $selectedMenus = collect($validated['menus'] ?? [])
            ->filter(fn ($item) => (int) ($item['quantity'] ?? 0) > 0)
            ->values();

        // Ambil item paket yang benar dari keranjang.
        $cart = session()->get('cart', []);
        $packageItems = collect($cart)
            ->filter(fn ($item) => ($item['type'] ?? null) === 'package')
            ->values();

        $package = null;
        $packageQuantity = 0;
        $replacements = $validated['replacements'] ?? [];

        if ($includePackages) {
            $package = Package::with('details.menu')
                ->where('status', 'Tersedia')
                ->findOrFail($validated['package_id']);

            // Pastikan paket yang dikirim sesuai dengan paket di form.
            $cartPackage = $packageItems->first(
                fn ($item) => (int) ($item['id'] ?? 0) === (int) $package->id
            );

            if (!$cartPackage) {
                return back()->withInput()->withErrors([
                    'include_packages' =>
                        'Paket tidak ditemukan di keranjang. Silakan pilih paket kembali.',
                ]);
            }

            $packageQuantity = (int) $validated['quantity'];

            if ($packageQuantity < 1) {
                return back()->withInput()->withErrors([
                    'quantity' => 'Jumlah paket minimal satu.',
                ]);
            }

            // Pastikan key replacement benar-benar berasal dari detail paket ini.
            $packageDetailIds = $package->details->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();

            foreach (array_keys($replacements) as $detailId) {
                if (!in_array((string) $detailId, $packageDetailIds, true)) {
                    return back()->withInput()->withErrors([
                        'replacements' => 'Pilihan pengganti tidak valid untuk paket ini.',
                    ]);
                }
            }
        } elseif ($selectedMenus->isEmpty()) {
            return back()->withInput()->withErrors([
                'menus' => 'Pilih minimal satu menu dan isi jumlah porsinya.',
            ]);
        }

        // Periksa menu tambahan yang dipilih.
        foreach ($selectedMenus as $item) {
            $menu = Menu::findOrFail($item['menu_id']);

            if ($menu->status !== 'Tersedia') {
                return back()->withInput()->withErrors([
                    'menus' => "Menu {$menu->name} sudah tidak tersedia.",
                ]);
            }

            if ((int) $item['quantity'] > $menu->stock) {
                return back()->withInput()->withErrors([
                    'menus' => "Jumlah {$menu->name} melebihi stok yang tersedia.",
                ]);
            }
        }

        // Pastikan menu pengganti tersedia.
        if ($includePackages) {
            foreach ($replacements as $replacementMenuId) {
                if (!$replacementMenuId) {
                    continue;
                }

                $replacementMenu = Menu::findOrFail($replacementMenuId);

                if (
                    $replacementMenu->status !== 'Tersedia' ||
                    $replacementMenu->stock < 1
                ) {
                    return back()->withInput()->withErrors([
                        'replacements' =>
                            "Menu pengganti {$replacementMenu->name} tidak tersedia.",
                    ]);
                }
            }
        }

        if ($includePackages) {
            logger()->info('DEBUG CUSTOM REPLACEMENTS', [
                'package_id' => $package->id,
                'replacements_from_form' => $replacements,
                'package_details' => $package->details->map(fn ($detail) => [
                    'detail_id' => $detail->id,
                    'original_menu_id' => $detail->menus_id,
                    'original_menu_name' => $detail->menu->name ?? null,
                ])->toArray(),
            ]);
        }

        try {
            $customerRequest = DB::transaction(function () use (
                $validated,
                $selectedMenus,
                $includePackages,
                $customer,
                $package,
                $packageQuantity,
                $replacements
            ) {
                $title = $includePackages
                    ? 'Custom Paket - ' . $package->name
                    : $validated['title'];

                $guestCount = $includePackages
                    ? $packageQuantity
                    : (int) $validated['guest_count'];

                $customerRequest = CustomerRequest::create([
                    'customer_id' => $customer->id,
                    'title' => $title,
                    'description' => $validated['description'] ?? null,
                    'guest_count' => $guestCount,
                    'event_date' => $validated['event_date'],
                    'address' => $validated['address'],
                    'estimated_budget' => $includePackages
                        ? null
                        : ($validated['estimated_budget'] ?? null),
                    'status' => 'Diajukan',
                ]);

                if ($includePackages) {
                    $lockedPackage = Package::lockForUpdate()
                        ->findOrFail($package->id);

                    if ($lockedPackage->status !== 'Tersedia') {
                        throw new \RuntimeException(
                            "Paket {$lockedPackage->name} sudah tidak tersedia."
                        );
                    }

                    $customerRequest->packages()->attach($lockedPackage->id, [
                        'quantity' => $packageQuantity,
                        'price' => $lockedPackage->price,
                    ]);

                    // Simpan menu yang menggantikan komponen paket.
                    foreach ($package->details as $packageDetail) {
                        $replacementMenuId =
                            $replacements[$packageDetail->id] ?? null;

                        if (!$replacementMenuId) {
                            continue;
                        }

                        $replacementMenu = Menu::findOrFail(
                            $replacementMenuId
                        );

                        CustomerRequestDetail::create([
                            'customer_request_id' => $customerRequest->id,
                            'menu_id' => $replacementMenu->id,
                            'quantity' =>
                                (int) $packageDetail->quantity * $packageQuantity,
                            'requested_price' => $replacementMenu->price,
                            'customization_type' => 'replacement',
                            'replaces_menu_id' => $packageDetail->menus_id,
                        ]);
                    }
                }

                // Simpan menu tambahan yang dipilih customer.
                foreach ($selectedMenus as $item) {
                    $menu = Menu::findOrFail($item['menu_id']);

                    CustomerRequestDetail::create([
                        'customer_request_id' => $customerRequest->id,
                        'menu_id' => $menu->id,
                        'quantity' => (int) $item['quantity'],
                        'requested_price' => $menu->price,
                        'customization_type' => 'additional',
                        'replaces_menu_id' => null,
                    ]);
                }

                return $customerRequest;
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'request' => $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'Permintaan belum berhasil disimpan. Periksa kembali data dan struktur database.',
            ]);
        }

        if ($includePackages) {
            // Hapus hanya paket yang sedang diproses, jangan hapus seluruh keranjang.
            $cart = session()->get('cart', []);
            unset($cart['package_' . $package->id]);
            session()->put('cart', $cart);
        }

        return redirect()
            ->route('customer.custom.index')
            ->with(
                'success',
                $includePackages
                    ? 'Permintaan custom paket berhasil dikirim untuk diperiksa admin.'
                    : 'Permintaan custom catering berhasil dikirim untuk diperiksa admin.'
            );
    }


    public function edit(int $id)
    {
        $customer = Auth::guard('customer')->user()->customer;

        $customRequest = CustomerRequest::with('details.menu')->findOrFail($id);

        abort_unless($customRequest->customer_id == $customer->id, 403);

        if (!in_array($customRequest->status, [
            'Diajukan',
            'Menunggu Persetujuan',
            'Ditolak'
        ])) {
            return back()->with(
                'error',
                'Permintaan ini sudah tidak dapat diedit.'
            );
        }


        $categories = Category::with([
            'menus' => function ($query) {
                $query->where('status', 'Tersedia')
                    ->orderBy('name');
            }
        ])
        ->orderBy('name')
        ->get();

        return view(
            'customer.custom.edit',
            compact('customRequest', 'categories')
        );
    }

    public function update(Request $request, int $id)
    {
        $customer = Auth::guard('customer')
            ->user()
            ->customer;

        $customRequest = CustomerRequest::with('details')
            ->findOrFail($id);

        abort_unless(
            $customRequest->customer_id == $customer->id,
            403
        );

        if (!in_array($customRequest->status, [
            'Diajukan',
            'Menunggu Persetujuan',
            'Ditolak'
        ])) {
            return back()->with(
                'error',
                'Permintaan ini sudah tidak dapat diedit.'
            );
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'guest_count' => 'required|integer|min:1',
            'event_date' => 'required|date|after_or_equal:today',
            'address' => 'required|string',
            'estimated_budget' => 'nullable|numeric|min:0',

            'menus' => 'required|array',
            'menus.*.menu_id' => 'required|exists:menus,id',
            'menus.*.quantity' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use (
            $customRequest,
            $validated
        ) {

            $customRequest->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'guest_count' => $validated['guest_count'],
                'event_date' => $validated['event_date'],
                'address' => $validated['address'],
                'estimated_budget' =>
                    $validated['estimated_budget'] ?? null,

                // Kalau customer mengedit request yang sebelumnya
                // ditolak, kita kembalikan ke Diajukan.
                'status' => 'Diajukan',
                'rejected_by' => null,
                'final_price' => null,
                'admin_notes' => null,
            ]);

            $customRequest->details()->delete();

            foreach ($validated['menus'] as $menu) {

                if ((int) $menu['quantity'] <= 0) {
                    continue;
                }

                $menuData = Menu::findOrFail(
                    $menu['menu_id']
                );

                CustomerRequestDetail::create([
                    'customer_request_id' => $customRequest->id,
                    'menu_id' => $menuData->id,
                    'quantity' => $menu['quantity'],
                    'requested_price' => $menuData->price,
                ]);
            }
        });

        return redirect()
            ->route(
                'customer.custom.show',
                $customRequest->id
            )
            ->with(
                'success',
                'Permintaan custom berhasil diperbarui.'
            );
    }

    public function index()
    {
        $account = Auth::guard('customer')->user();

        $customer = $account->customer;

        $customRequests = $customer
            ->customRequest()->with([
                'packages.details.menu',
                'details.menu',
                'details.replacesMenu',
            ])->latest()->get();

        return view('customer.custom.index', compact('customRequests'));
    }

    public function show(CustomerRequest $customerRequest)
    {        
        $customer = Auth::guard('customer')
            ->user()
            ->customer;

    
        abort_if(!$customer, 404, 'Data Customer tidak ditemukan.');

        abort_unless(
            $customerRequest->customer_id == $customer->id,
            403
        );

        $customerRequest->load([
            'packages.details.menu',
            'details.menu',
            'details.replacesMenu'
        ]);

        return view(
            'customer.custom.show',
            compact('customerRequest')
        );
    }

    public function destroy(int $id)
    {
        $customer = Auth::guard('customer')
            ->user()
            ->customer;

        $customRequest = CustomerRequest::findOrFail($id);

        abort_unless(
            $customRequest->customer_id == $customer->id,
            403
        );

        if (!in_array($customRequest->status, [
            'Diajukan',
            'Menunggu Persetujuan',
            'Ditolak'
        ])) {
            return back()->with(
                'error',
                'Permintaan ini sudah tidak dapat dihapus.'
            );
        }

        $customRequest->delete();

        return redirect()
            ->route('customer.custom.index')
            ->with(
                'success',
                'Permintaan custom berhasil dihapus.'
            );
    }

    public function approve(Request $request, int $id)
    {
        $customer = Auth::guard('customer')->user()->customer;

        $customRequest = CustomerRequest::with([
            'details',
            'packages.details',
        ])->findOrFail($id);

        abort_unless(
            $customRequest->customer_id == $customer->id,
            403
        );

        if ($customRequest->status !== 'Menunggu Persetujuan') {
            return back()->with(
                'error',
                'Permintaan ini tidak dapat disetujui.'
            );
        }

        $validated = $request->validate([
            'delivery_method' => 'required|in:pickup,delivery',
        ]);

        if ($customRequest->packages->isEmpty()
             && $customRequest->details->isEmpty()
        ) {
            return back()->with(
                'error',
                'Tidak ada menu custom dalam permintaan ini.'
            );
        }

        $additionalDetails = $customRequest->details->
            where('customization_type', 'additional');

        foreach ($additionalDetails as $detail) {
            if ($detail->approved_price === null) {
                return back()->with(
                    'error',
                    'Harga menu custom belum ditentukan oleh admin.'
                );
            }
        }

        try {
            $order = DB::transaction(function () use (
                $customRequest,
                $validated
            ) {
                /*
                |--------------------------------------------------------------------------
                | 1. Ambil paket dan menu custom
                |--------------------------------------------------------------------------
                */

                $customRequest->load([
                    'details.menu',
                    'packages.details.menu',
                ]);

                $packageLines = [];
                $customLines = [];
                $requiredStock = [];

                /*
                * Paket: hitung kebutuhan stok berdasarkan isi paket.
                */
                foreach ($customRequest->packages as $requestPackage) {
                    $package = \App\Models\Package::with('details')
                        ->lockForUpdate()
                        ->findOrFail($requestPackage->id);

                    if ($package->status !== 'Tersedia') {
                        throw new \RuntimeException(
                            "Paket {$package->name} sudah tidak tersedia."
                        );
                    }

                    $quantity = (int) $requestPackage->pivot->quantity;

                    if ($quantity < 1) {
                        throw new \RuntimeException(
                            "Jumlah paket {$package->name} tidak valid."
                        );
                    }

                    $packagePrice = (float) $requestPackage->pivot->price;

                    $packageLines[] = [
                        'package' => $package,
                        'quantity' => $quantity,
                        'price' => $packagePrice,
                        'subtotal' => $packagePrice * $quantity,
                    ];

                    foreach ($package->details as $packageDetail) {

                        $replacement = $customRequest->details->first(
                            fn($detail) => $detail->customization_type === 'replacement'
                            && (int) $detail->replaces_menu_id === (int) $packageDetail->menus_id
                        );

                        // Pakai stok menu pengganti jika komponen paket diganti.
                        $menuId = $replacement
                            ? (int) $replacement->menu_id
                            : (int) $packageDetail->menus_id;

                        $quantityNeeded =
                            (int) $packageDetail->quantity * $quantity;

                        $requiredStock[$menuId] =
                            ($requiredStock[$menuId] ?? 0) + $quantityNeeded;
                    }
                }

                /*
                * Menu custom: hitung kebutuhan stok juga.
                */
                foreach ($customRequest->details
                            ->where('customization_type', 'additional')
                             as $detail) {

                    $menu = Menu::findOrFail($detail->menu_id);

                    $quantity = (int) $detail->quantity;
                    $price = (float) $detail->approved_price;

                    $customLines[] = [
                        'menu' => $menu,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $price * $quantity,
                    ];

                    $requiredStock[$menu->id] =
                        ($requiredStock[$menu->id] ?? 0) + $quantity;
                }

                /*
                |--------------------------------------------------------------------------
                | 2. Periksa stok gabungan sebelum membuat order
                |--------------------------------------------------------------------------
                |
                | Jika ayam ada dalam paket dan dipilih lagi sebagai custom,
                | kebutuhan stoknya harus dijumlahkan.
                */

                $lockedMenus = [];

                foreach ($requiredStock as $menuId => $quantityNeeded) {
                    $menu = Menu::lockForUpdate()->findOrFail($menuId);

                    if (
                        $menu->status !== 'Tersedia' ||
                        $menu->stock < $quantityNeeded
                    ) {
                        throw new \RuntimeException(
                            "Stok menu {$menu->name} tidak mencukupi. " .
                            "Dibutuhkan {$quantityNeeded}, tersedia {$menu->stock}."
                        );
                    }

                    $lockedMenus[$menuId] = $menu;
                }

                /*
                |--------------------------------------------------------------------------
                | 3. Buat satu order untuk paket + custom
                |--------------------------------------------------------------------------
                */

                $totalPaket = collect($packageLines)->sum('subtotal');
                $totalCustom = collect($customLines)->sum('subtotal');
                $total = $totalPaket + $totalCustom;

                $order = Order::create([
                    'order_code' => 'ORD-' .
                        now()->format('YmdHis') . '-' .
                        random_int(100, 999),

                    'customer_id' => $customRequest->customer_id,
                    'order_date' => now()->toDateString(),
                    'delivery_date' => $customRequest->event_date,
                    'delivery_method' => $validated['delivery_method'],
                    'delivery_fee' => 0,
                    'delivery_distance' => null,
                    'status' => 'Pending',
                    'total' => $total,
                    'notes' => $customRequest->description,
                ]);

                /*
                |--------------------------------------------------------------------------
                | 4. Simpan paket yang dipesan
                |--------------------------------------------------------------------------
                */

                foreach ($packageLines as $line) {
                    OrderPackage::create([
                        'order_id' => $order->id,
                        'package_id' => $line['package']->id,
                        'quantity' => $line['quantity'],
                        'price' => $line['price'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    /*
                    * Simpan komponen paket untuk pencatatan stok.
                    * Nilai source=package mencegah biaya dihitung dua kali.
                    */
                    foreach ($line['package']->details as $packageDetail) {

                        $replacement = $customRequest->details->first(
                            fn($detail) => $detail->customization_type === 'replacement'
                            && (int) $detail->replaces_menu_id === (int) $packageDetail->menus_id
                        );
    
                        $menuId = $replacement ? (int) $replacement->menu_id : (int) $packageDetail->menus_id;
    
                        $menu = $lockedMenus[$menuId];

                        $quantityNeeded =
                            (int) $packageDetail->quantity * $line['quantity'];

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'menu_id' => $menu->id,
                            'quantity' => $quantityNeeded,
                            'price' => $menu->price,
                            'subtotal' => $menu->price * $quantityNeeded,
                            'source' => 'package',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | 5. Simpan menu tambahan yang dipilih pelanggan
                |--------------------------------------------------------------------------
                */

                foreach ($customLines as $line) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'menu_id' => $line['menu']->id,
                        'quantity' => $line['quantity'],
                        'price' => $line['price'],
                        'subtotal' => $line['subtotal'],
                        'source' => 'custom',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | 6. Kurangi stok sesuai total kebutuhan
                |--------------------------------------------------------------------------
                */

                foreach ($requiredStock as $menuId => $quantityNeeded) {
                    $lockedMenus[$menuId]->decrement(
                        'stock',
                        $quantityNeeded
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 7. Tandai permintaan sebagai disetujui
                |--------------------------------------------------------------------------
                */

                $customRequest->update([
                    'order_id' => $order->id,
                    'status' => 'Disetujui',
                    'rejected_by' => null,
                ]);

                return $order;
            });

            return redirect()
                ->route('customer.orders.show', $order->id)
                ->with(
                    'success',
                    'Permintaan berhasil disetujui dan pesanan telah dibuat.'
                );

        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'Pesanan belum dapat dibuat. Silakan periksa kembali data dan stok menu.'
            );
        }
    }

    public function reject(int $id)
    {
        $customer = Auth::guard('customer')
            ->user()
            ->customer;

        $customRequest = CustomerRequest::findOrFail($id);

        abort_unless(
            $customRequest->customer_id == $customer->id,
            403
        );

        if ($customRequest->status !== 'Menunggu Persetujuan') {
            return back()->with('error', 'Permintaan ini tidak dapat ditolak.');
        }

        $customRequest->update([
            'status' => 'Ditolak',
            'rejected_by' => 'customer',
        ]);

        return redirect()
            ->route('customer.custom.show', $customRequest->id)
            ->with('success', 'Permintaan custom berhasil ditolak.');
    }
}
