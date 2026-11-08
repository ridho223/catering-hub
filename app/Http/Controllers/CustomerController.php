<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::latest()->get();

        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge(['phone' => Customer::normalizePhone($request->input('phone'))]);

        $data = $request->validate([
            'name' => 'required|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => ['required', 'regex:/^0[0-9]{8,14}$/'],
            'address' => 'required|max:500'
        ], [
            'phone.regex' => 'Nomor telepon tidak valid. Contoh: 081234567890.',
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success','Customer berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load('account');
        $orders = $customer->orders()->latest()->limit(10)->get();

        $notCancelled = $customer->orders()->where('status', '!=', 'Dibatalkan');

        $stats = [
            'total'  => $customer->orders()->count(),
            'active' => $customer->orders()->whereIn('status', ['Pending', 'Diproses', 'Siap_diambil_atau_diantar'])->count(),
            'done'   => $customer->orders()->where('status', 'Selesai')->count(),
            'valid'  => (clone $notCancelled)->count(),
            'spent'  => (float) (clone $notCancelled)->sum('total'),
        ];

        return view('customer.show', compact('customer', 'orders', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->merge(['phone' => Customer::normalizePhone($request->input('phone'))]);

        $data = $request->validate([
            'name' => 'required|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => ['required', 'regex:/^0[0-9]{8,14}$/'],
            'address' => 'required|max:500'
        ], [
            'phone.regex' => 'Nomor telepon tidak valid. Contoh: 081234567890.',
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')->with('success','Customer berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Customer yang punya pesanan tidak boleh dihapus (riwayat transaksi & laporan).
        if ($customer->orders()->exists()) {
            return redirect()->route('customers.index')
                ->with('error','Customer ini punya riwayat pesanan sehingga tidak bisa dihapus.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success','Customer berhasil dihapus');
    }
}