<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $menus = Menu::where('status','Tersedia')->where('stock', '>', 0)->orderBy('name')->get();

        return view('order.create', compact('customers','menus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        try{
            DB::transaction(function () use ($request){

                $total = 0;
                $orderItems = [];

                foreach ($request->items as $item) {

                    $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);

                    //cek status menu
                    if($menu->status !== 'Tersedia'){
                        throw new \Exception(
                            "Menu {$menu->name} sudah tidak tersedia."
                        );
                    }

                    //cek stock
                    if($menu->stock < $item['quantity']){
                        throw new \Exception(
                            "Stock menu {$menu->name} tidak mencukupi. Stock tersedia: {$menu->stock}."
                        );
                    }

                    //hitung subtotal
                    $subtotal = $menu->price * $item['quantity'];

                    $total += $subtotal;

                    $orderItems[] = [
                        'menu' => $menu,
                        'quantity' => $item['quantity'],
                        'price' => $menu->price,
                        'subtotal' => $subtotal,
                    ];

                }

                $order = Order::create([
                    'customer_id' => $request->customer_id,
                    'order_date' => $request->order_date,
                    'delivery_date' => $request->delivery_date,
                    'status' => 'Pending',
                    'total' => $total,
                    'notes' => $request->notes,
                ]);

                foreach ($orderItems as $item){
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'menu_id' => $item['menu']->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    $item['menu']->decrement(
                        'stock',
                        $item['quantity']
                    );
                }

            });

            return redirect()->route('orders.index')->with('success','Pesanan berhasil dibuat.');

        }catch(\Exception $e){
            return back()->withInput()->with('error' , $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return view('order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $customers = Customer::orderBy('name')->get();

        $menus = Menu::where('status', 'Tersedia')
            ->where('stock','>',0)
            ->orderBy('name')
            ->get();

        return view('order.edit',compact('order','customers','menus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:order_date',
            'status' => 'required|in:Pending,Diproses,Selesai',
            'notes' => 'nullable|string',
        ]);

        $order->update([
            'customer_id' => $request->customer_id,
            'order_date' => $request->order_date,
            'delivery_date' => $request->delivery_date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('orders.index')->with('success','Pesanan berhasil diperbarui');
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
}
