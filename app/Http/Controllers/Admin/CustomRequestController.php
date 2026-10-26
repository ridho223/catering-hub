<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomRequestController extends Controller
{
    public function index()
    {
        $customRequests = CustomerRequest::with('customer')->latest()->get();

        return view('Custom-request.index', compact('customRequests'));
    }

    public function show(int $id)
    {
        $customRequest = CustomerRequest::with([
            'customer',
            'details.menu',
        ])->findOrFail($id);

        $statusClass = match($customRequest->status) {
            'Diajukan' => 'bg-primary',            
            'Menunggu Persetujuan' => 'bg-warning text-dark',
            'Disetujui' => 'bg-success',
            'Ditolak' => 'bg-danger',
            'Selesai' => 'bg-dark',
            default => 'bg-secondary',
        };

        return view('Custom-request.show', compact('customRequest','statusClass'));
    }

    public function edit(int $id)
    {
        $customRequest = CustomerRequest::with([
            'customer',
            'details.menu',
        ])->findOrFail($id);

        

        return view('Custom-request.edit', compact('customRequest'));
    }

    public function update(Request $request, int $id)
    {
        $customRequest = CustomerRequest::with([
            'details',
            'packages'
        ])->findOrFail($id);

        $rules = [
            'approved_price' => 'required|array',
            'approved_price.*' => 'required|numeric|min:0',            
            'admin_notes' => 'nullable|string',
            'status' => 'required|in:Menunggu Persetujuan,Ditolak,Disetujui',
        ];

        if($customRequest->packages->isNotEmpty()) {
            $rules['package_price'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($customRequest, $validated) {

            $total = 0;

            // simpan hara paket setelah penyesuaian
            foreach ($customRequest->packages as $package){
                $price = (float) $validated['package_price'];
                $quantity = (int) $package->pivot->quantity;

                $customRequest->packages()->updateExistingPivot($package->id,['price' => $price]);

                $total += $price * $quantity;
            }


            // Simpan harga detail menu
            foreach ($customRequest->details as $detail){
                if($detail->customization_type === 'replacement'){
                    //harga penganti sudah termasuk dalam harga paket.
                    continue;
                }

                $price = (float) ( $validated['approved_price'][$detail->id]
                        ?? $detail->approved_price ?? $detail->requested_price ?? 0 );

                $detail->update([
                    'approved_price' => $price,
                ]);

                $total += $price * (int) $detail->quantity; 
            }

            $customRequest->update([
                'final_price' => $total,
                'admin_notes' => $validated['admin_notes'] ?? null,
                'status' => $validated['status'],
                'rejected_by' => $validated['status'] === 'Ditolak' ? 'admin' : null,
            ]);
        });

        return redirect()->route('admin.custom-requests.show', $customRequest->id)->with('success', 'Review custom catering berhasil disimpan');
    }


}
