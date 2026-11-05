{{-- resources/views/admin/partials/ui.blade.php  (gaya bersama semua halaman admin) --}}
<style>
    .adm-shell { --ink:#1f2a24; --mute:#6b7770; --line:#e6ebe7; --soft:#f5f8f5; --green:#198754; --green-dark:#126640; --green-light:#eaf5ed; }

    .ui-page { display:grid; gap:1rem; }

    /* kartu */
    .ui-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:1.2rem; height:100%; }
    .ui-head { display:flex; justify-content:space-between; align-items:center; gap:.75rem; margin-bottom:1rem; flex-wrap:wrap; }
    .ui-head h2 { margin:0; font-size:1.02rem; font-weight:700; color:var(--ink); }
    .ui-head small { display:block; color:var(--mute); font-size:.85rem; }
    .ui-link { color:var(--green); font-weight:600; font-size:.85rem; text-decoration:none; }
    .ui-link:hover { color:var(--green-dark); text-decoration:underline; }

    /* ikon & nada warna */
    .ui-ic { width:44px; height:44px; border-radius:13px; display:grid; place-items:center; font-size:1.2rem; flex:none; }
    .t-green  { background:#e3f4ea; color:#146c43; }
    .t-blue   { background:#e2f0fb; color:#0b5a8a; }
    .t-amber  { background:#fff1d6; color:#8a5a00; }
    .t-purple { background:#e8e6fb; color:#4a3fb0; }
    .t-red    { background:#fde8e8; color:#a52834; }
    .t-gray   { background:#eef2ef; color:#6b7770; }

    /* angka ringkas */
    .ui-kpi { display:flex; gap:.9rem; align-items:flex-start; min-width:0; }
    .ui-kpi small { color:var(--mute); display:block; }
    .ui-kpi strong { font-size:1.45rem; color:var(--ink); line-height:1.25; display:block; overflow-wrap:anywhere; }
    .ui-kpi .sub { color:var(--mute); font-size:.8rem; }
    .ui-trend { font-size:.78rem; font-weight:600; margin-top:.2rem; display:inline-block; text-decoration:none; }
    .ui-trend.up { color:#146c43; } .ui-trend.down { color:#a52834; } .ui-trend.flat { color:var(--mute); }

    /* label status */
    .ui-chip { display:inline-flex; align-items:center; gap:.3rem; padding:.18rem .6rem; border-radius:999px; font-size:.75rem; font-weight:600; white-space:nowrap; }
    .ui-chip.warn  { background:#fff1d6; color:#8a5a00; }
    .ui-chip.info  { background:#e2f0fb; color:#0b5a8a; }
    .ui-chip.ready { background:#e8e6fb; color:#4a3fb0; }
    .ui-chip.ok    { background:#e3f4ea; color:#146c43; }
    .ui-chip.bad   { background:#fde8e8; color:#a52834; }
    .ui-chip.off   { background:#eceff1; color:#5f6b72; }

    /* tab / pilihan rentang */
    .ui-seg { display:inline-flex; background:var(--soft); border:1px solid var(--line); border-radius:10px; padding:3px; }
    .ui-seg button { border:0; background:transparent; padding:.25rem .8rem; border-radius:8px; color:var(--mute); font-size:.84rem; font-weight:600; }
    .ui-seg button.on { background:#fff; color:var(--green-dark); box-shadow:0 1px 3px rgba(0,0,0,.08); }

    /* baris daftar */
    .ui-row { display:flex; align-items:center; gap:.6rem; padding:.55rem 0; border-top:1px solid var(--line); font-size:.92rem; }
    .ui-head + .ui-row, .ui-head + .ui-rank, .dh-stack + .ui-row { border-top:0; }
    .ui-dot { width:10px; height:10px; border-radius:50%; flex:none; }
    .ui-row .n { flex:1; color:var(--ink); min-width:0; }
    .ui-row b { color:var(--ink); }
    .ui-row small { color:var(--mute); }

    .ui-rank { display:flex; align-items:center; gap:.7rem; padding:.55rem 0; border-top:1px solid var(--line); }
    .ui-rank em { font-style:normal; width:26px; height:26px; border-radius:8px; background:var(--soft); color:var(--mute); font-weight:700; font-size:.82rem; display:grid; place-items:center; flex:none; }
    .ui-rank.top em { background:#fff1d6; color:#8a5a00; }
    .ui-rank b { flex:1; color:var(--ink); font-weight:600; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:.92rem; }
    .ui-rank small { color:var(--mute); white-space:nowrap; }

    .ui-empty { text-align:center; color:var(--mute); padding:1.6rem 1rem; }
    .ui-empty i { font-size:2rem; display:block; margin-bottom:.4rem; color:#b9c7be; }
    .ui-empty b { display:block; color:var(--ink); }

    /* tabel */
    .ui-table { width:100%; border-collapse:collapse; font-size:.92rem; }
    .ui-table th { background:var(--soft); color:var(--mute); font-weight:600; font-size:.8rem; padding:.6rem .8rem; text-align:left; white-space:nowrap; }
    .ui-table th:first-child { border-radius:10px 0 0 10px; } .ui-table th:last-child { border-radius:0 10px 10px 0; }
    .ui-table td { padding:.7rem .8rem; border-top:1px solid var(--line); vertical-align:middle; }
    .ui-table thead + tbody tr:first-child td { border-top:0; }
    .ui-table a { color:var(--ink); font-weight:600; text-decoration:none; }
    .ui-table a:hover { color:var(--green); }
    .ui-table .sub { display:block; color:var(--mute); font-size:.8rem; font-weight:400; }

    /* HP: baris tabel jadi kartu kecil berlabel */
    @media (max-width:575.98px) {
        .ui-table--stack thead { display:none; }
        .ui-table--stack, .ui-table--stack tbody, .ui-table--stack tr, .ui-table--stack td { display:block; width:100%; }
        .ui-table--stack tr { padding:.7rem 0; border-top:1px solid var(--line); }
        .ui-table--stack tbody tr:first-child { border-top:0; padding-top:0; }
        .ui-table--stack td { display:flex; justify-content:space-between; align-items:center; gap:1rem; padding:.25rem 0; border:0; text-align:right; }
        .ui-table--stack td::before { content:attr(data-label); color:var(--mute); font-size:.8rem; font-weight:600; text-align:left; flex:none; }
    }

    /* paginasi Laravel */
    .ui-pg nav .flex-sm-fill { justify-content:flex-end !important; }
    .ui-pg nav .flex-sm-fill > div:first-child { display:none; }
    .ui-pg .pagination { margin:0; }
    .ui-pg .pagination .page-link { color:#267548; border-color:var(--line); }
    .ui-pg .pagination .active .page-link { background:var(--green); border-color:var(--green); color:#fff; }

    .adm-content a:focus-visible, .adm-content button:focus-visible { outline:3px solid #7fd1a0; outline-offset:2px; }

    @media print {
        .sb, .tb, .adm-overlay, .adm-toast, .no-print { display:none !important; }
        .adm-main { margin:0 !important; }
        .adm-content { padding:0 !important; }
        body { background:#fff !important; }
        .ui-card { break-inside:avoid; }
    }
</style>

<script>
    window.adm = window.adm || {};
    window.adm.short = function (v) {
        return v >= 1e6 ? (v / 1e6).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt'
             : v >= 1e3 ? (v / 1e3).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' rb' : v;
    };
    window.adm.rp = function (v) { return 'Rp' + Number(v).toLocaleString('id-ID'); };
</script>