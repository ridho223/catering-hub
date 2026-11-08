<?php

return [
    // Customer wajib verifikasi email sebelum checkout/bayar.
    // Set false di .env selama SMTP belum siap (mis. di lokal).
    'require_email_verification' => (bool) env('REQUIRE_EMAIL_VERIFICATION', true),

    // Pesanan Pending tanpa pembayaran dibatalkan otomatis setelah sekian jam (stok dikembalikan).
    'pending_expire_hours' => (int) env('PENDING_ORDER_EXPIRE_HOURS', 48),

    // Batas pesanan Pending per customer (mencegah penimbunan stok).
    'max_pending_orders' => (int) env('MAX_PENDING_ORDERS', 3),

    // Batas jumlah porsi per paket dalam satu item keranjang/pesanan.
    'max_quantity' => (int) env('MAX_QUANTITY', 99),

    // Minimal selisih hari antara hari ini dan tanggal kirim/ambil (0 = boleh hari ini).
    'min_lead_days' => (int) env('MIN_LEAD_DAYS', 0),
];