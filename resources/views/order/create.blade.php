@extends('layouts.admin')
@section('title','Tambah Pesanan')
@section('content')

<div class="container-fluid py-3">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-success text-white rounded-top-4">
            <h5 class="mb-0">
                <i class="bi bi-cart-plus me-2"></i>
                Tambah Pesanan
            </h5>
        </div>

        <div class="card-body py-3">

            <form action="{{route('orders.store')}}" method="POST">
                @csrf

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Customer
                        </label>

                        <select name="customer_id" class="form-select" required>

                            <option value="">--Pilih Customer</option>

                            @foreach ($customers as $customer )
                                
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Tanggal Pesanan
                        </label>

                        <input type="date" name="order_date" class="form-control" value="{{date('Y-m-d')}}" required>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Tanggal Kirim
                        </label>

                        <input type="date" name="delivery_date" class="form-control" required>

                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-basket me-2"></i>
                        Detail Pesanan
                    </h6>

                    <button type="button" class="btn btn-success btn-sm" id="addMenu">

                        <i class="bi bi-plus-circle me-1"></i>
                          Tambah Menu

                    </button>

                </div>

                <div class="table-responsive">

                    <table class="table align-middle" id="orderTable">

                        <thead class="table-success">

                            <tr>
                                <th>Menu</th>
                                <th width="120">Qty</th>
                                <th width="150">Harga</th>
                                <th width="170">Subtotal</th>
                                <th width="60"></th>
                            </tr>
                            
                        </thead>

                        <tbody id="orderItems">

                            <tr class="order-item">

                                <td>

                                    <select name="items[0][menu_id]" class="form-select menu-select" required>

                                        <option value="">
                                            --Pilih Menu--
                                        </option>

                                        @foreach ($menus as $menu )
                                            
                                            <option value="{{$menu->id}}" data-price="{{$menu->price}}" data-stock="{{$menu->stock}}">

                                                {{$menu->name}}
                                                - Rp {{ number_format($menu->price, 0, ',', '.')}}

                                            </option>

                                        @endforeach

                                    </select>

                                </td>

                                <td>

                                    <input type="number" name="items[0][quantity]" class="form-control quantity" min="1" value="1" required>

                                </td>


                                <td>

                                    <span class="price">
                                        Rp 0
                                    </span>

                                </td>

                                <td >

                                    <span class="subtotal fw-semibold">
                                        Rp 0
                                    </span>

                                </td>

                                <td>

                                    <button type="button" class="btn btn-danger btn-sm remove-item">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

                <div class="row justify-content-end mt-4">

                    <div class="col-md-4">

                        <div class="card bg-light border-0">

                            <div class="card-body">

                                <div class="d-flex justify-content-between">

                                    <span class="fw-semibold">
                                        Total
                                    </span>

                                    <span class="fw-bold text-success fs-5" id="grandTotal">
                                        Rp 0
                                    </span>

                                </div>

                                <input type="hidden" name="total" id="totalInput" value="0">
                                
                            </div>

                        </div>

                    </div>

                </div>

                <div class="mt-4">

                    <label class="form-label fw-semibold">
                        Catatan
                    </label>

                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan Pesanan...."></textarea>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a href="{{route('orders.index')}}" class="btn btn-secondary">
                        
                        <i class="bi bi-arrow-left me-1">
                            kembali
                        </i>
                        
                    </a>

                    <button type="submit" class="btn btn-success">

                        <i class="bi bi-check-circle me-1"></i>
                            Simpan Pesanan

                    </button>

                </div>

            </form>

        </div>


    </div>

</div>

@endsection

@push('scripts')

<script>

    

let itemIndex = 1;

const menus = @json($menus);

function formatRupiah(number)
{
    return 'Rp' + Number(number).toLocaleString('id-ID');
}

function calculateRow(row)
{
    const select = row.querySelector('.menu-select');
    const quantity = row.querySelector('.quantity');
    const priceElement = row.querySelector('.price');
    const subtotalElement = row.querySelector('.subtotal');

    const option = select.options[select.selectedIndex];

    const price = Number(option.dataset.price || 0);
    const qty = Number(quantity.value || 0);

    const subtotal = price * qty;

    priceElement.textContent = formatRupiah(price);
    subtotalElement.textContent = formatRupiah(subtotal);

    calculateTotal();
}

function calculateTotal()
{
    let total = 0;

    document.querySelectorAll('.order-item').forEach(row => {

        const select = row.querySelector('.menu-select');
        const quantity = row.querySelector('.quantity');

        const option = select.options[select.selectedIndex];

        const price = Number(option.dataset.price || 0);
        const qty = Number(quantity.value || 0);

        total += price * qty;

    });

    document.getElementById('grandTotal').textContent = 
            formatRupiah(total);

    document.getElementById('totalInput').value = total;

}

function menuOptions()
{
    let html = '<option value="">--Pilih Menu--</option>';

    menus.forEach(menu => {

        html += `
            <option value="${menu.id}"
            data-price="${menu.price}"
            data-stock="${menu.stock}"> 
            
            ${menu.name} - ${formatRupiah(menu.price)}
       
            </option>

        `;
    });

    return html;
}

document.getElementById('addMenu').addEventListener('click', function(){

    const tbody = document.getElementById('orderItems');
    const row = document.createElement('tr');

    row.classList.add('order-item');

    row.innerHTML = `

        <td>
            
            <select name="items[${itemIndex}][menu_id]"
                    class="form-select menu-select" required>
            
                ${menuOptions()}
            
            </select>

        </td>

        <td>

            <input type="number"
                    name="items[${itemIndex}][quantity]"
                    class="form-control quantity"
                    min="1"
                    value="1"
                    required>

        </td>


        <td>
            <span class="price">Rp 0 </span>
        </td>
        
        <td>
            <span class="subtotal fw-semibold">Rp 0 </span>
        </td>
        
        <td>

            <button type="button" class="btn btn-danger btn-sm remove-item">

                <i class="bi bi-trash"></i>

            </button>

        </td>
    `;

    tbody.appendChild(row);

    itemIndex++;

});

document.addEventListener('change', function(e){
    if(e.target.classList.contains('menu-select')){
        calculateRow(e.target.closest('.order-item'));
    }
});

document.addEventListener('input', function(e){
    if(e.target.classList.contains('quantity')){
        calculateRow(e.target.closest('.order-item'));
    }
});

document.addEventListener('click', function(e){
    const button = e.target.closest('.remove-item');

    if(!button) return;

    const rows = document.querySelectorAll('.order-item');

    if(rows.length <= 1){

        alert('Minimal harus ada satu menu.');

        return;
    }

    button.closest('.order-item').remove();

    calculateTotal();
    
});

</script>
    
@endpush