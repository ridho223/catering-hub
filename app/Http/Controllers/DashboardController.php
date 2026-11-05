<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard + Laporan.
 *
 * Definisi yang dipakai:
 * - Pemasukan  = jumlah pembayaran berstatus "Dibayar" (berdasarkan tanggal pembayaran).
 * - Pesanan    = data orders (berdasarkan order_date).
 * Nama tabel mengikuti default Laravel: orders, payments, order_packages, order_details, packages, menus.
 */
class DashboardController extends Controller
{
    /* =========================================================
     |  DASHBOARD
     |========================================================= */
    public function index()
    {
        $today      = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();

        // bandingkan dengan bulan lalu pada rentang hari yang sama
        $prevStart = $monthStart->copy()->subMonthNoOverflow();
        $prevEnd   = $prevStart->copy()
            ->addDays((int) $monthStart->diffInDays($today))
            ->min($prevStart->copy()->endOfMonth());

        $revenueThisMonth = $this->revenueBetween($monthStart, $today);
        $revenuePrevMonth = $this->revenueBetween($prevStart, $prevEnd);

        $ordersThisMonth = Order::whereBetween('order_date', [$monthStart->toDateString(), $today->toDateString()])->count();
        $ordersPrevMonth = Order::whereBetween('order_date', [$prevStart->toDateString(), $prevEnd->toDateString()])->count();

        // grafik pemasukan 30 hari terakhir (tampilan 7 hari dipotong di sisi browser)
        $from = $today->copy()->subDays(29);
        $map  = $this->revenueByDay($from, $today);
        $days = collect(range(29, 0))->map(fn ($i) => $today->copy()->subDays($i));

        $chartLabels = $days->map(fn ($d) => $d->translatedFormat('d M'))->values();
        $chartData   = $days->map(fn ($d) => (float) ($map[$d->toDateString()] ?? 0))->values();

        $statusCounts = Order::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')->pluck('c', 'status')->all();

        $lowStockQuery = Menu::where('status', 'Tersedia')->where('stock', '<=', 5);

        return view('dashboard.index', [
            'totalCategory' => Category::count(),
            'totalMenu'     => Menu::count(),
            'totalCustomer' => Customer::count(),

            'revenueThisMonth' => $revenueThisMonth,
            'revenueTrend'     => $this->trend($revenueThisMonth, $revenuePrevMonth),
            'ordersThisMonth'  => $ordersThisMonth,
            'ordersTrend'      => $this->trend($ordersThisMonth, $ordersPrevMonth),

            'statusCounts'    => $statusCounts,
            'pendingPayments' => Payment::where('status', 'Menunggu Verifikasi')->count(),
            'todayCount'      => Order::whereDate('delivery_date', $today)->where('status', '!=', 'Selesai')->count(),
            'lowStockCount'   => (clone $lowStockQuery)->count(),
            'lowStock'        => (clone $lowStockQuery)->orderBy('stock')->limit(5)->get(),

            'recentOrders'   => Order::with(['customer', 'payment'])->latest('id')->limit(6)->get(),
            'upcomingOrders' => Order::with('customer')
                                    ->where('status', '!=', 'Selesai')
                                    ->orderBy('delivery_date')->limit(6)->get(),

            'topPackages' => $this->topPackages($today->copy()->subDays(29), $today),

            'chartLabels' => $chartLabels,
            'chartData'   => $chartData,
        ]);
    }

    /* =========================================================
     |  LAPORAN
     |========================================================= */
    public function report(Request $request)
    {
        [$from, $to] = $this->period($request);

        $orders = fn () => Order::whereBetween('order_date', [$from->toDateString(), $to->toDateString()]);

        $ordersCount = $orders()->count();
        $grossOrders = (float) $orders()->sum('total');
        $revenue     = $this->revenueBetween($from, $to);
        $unpaid = (float) $orders()->with('payments')->get()->sum(fn ($o) => $o->remainingAmount());

        $statusBreakdown = $orders()
            ->select('status', DB::raw('COUNT(*) as c'), DB::raw('SUM(total) as t'))
            ->groupBy('status')->get();

        $methodBreakdown = Payment::where('status', 'Dibayar')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->select('method', DB::raw('COUNT(*) as c'), DB::raw('SUM(amount) as t'))
            ->groupBy('method')->get();

        // grafik pemasukan harian pada periode
        $map  = $this->revenueByDay($from, $to);
        $days = collect();
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $days->push($d->copy());
        }

        return view('dashboard.report', [
            'from' => $from,
            'to'   => $to,

            'revenue'     => $revenue,
            'ordersCount' => $ordersCount,
            'grossOrders' => $grossOrders,
            'averageOrder'=> $ordersCount ? $grossOrders / $ordersCount : 0,
            'unpaid'      => $unpaid,
            'finished'    => $orders()->where('status', 'Selesai')->count(),

            'statusBreakdown' => $statusBreakdown,
            'methodBreakdown' => $methodBreakdown,

            'chartLabels' => $days->map(fn ($d) => $d->translatedFormat('d M'))->values(),
            'chartData'   => $days->map(fn ($d) => (float) ($map[$d->toDateString()] ?? 0))->values(),

            'topPackages' => $this->topPackages($from, $to),
            'topMenus'    => $this->topMenus($from, $to),

            'orders' => $orders()->with(['customer', 'payment'])
                ->orderByDesc('order_date')->orderByDesc('id')
                ->paginate(15)->withQueryString(),
        ]);
    }

    /** Unduh daftar pesanan pada periode sebagai CSV (bisa dibuka di Excel). */
    public function exportReport(Request $request)
    {
        [$from, $to] = $this->period($request);

        $rows = Order::with(['customer', 'payment'])
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('order_date')->orderBy('id')->get();

        $filename = 'laporan-pesanan-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar

            fputcsv($out, ['Kode', 'Tanggal Pesanan', 'Tanggal Kirim/Ambil', 'Pelanggan', 'Total', 'Status Pesanan', 'Status Pembayaran', 'Dibayar']);

            foreach ($rows as $o) {
                fputcsv($out, [
                    $o->order_code ?? $o->id,
                    Carbon::parse($o->order_date)->format('Y-m-d'),
                    Carbon::parse($o->delivery_date)->format('Y-m-d'),
                    $o->customer?->name ?? '-',
                    (float) $o->total,
                    $o->status,
                    $o->payment?->status ?? 'Belum Dibayar',
                    $o->paymentState(),
                    (float) $o->paidAmount(),
                    (float) ($o->payment?->status === 'Dibayar' ? $o->payment->amount : 0),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /* =========================================================
     |  HELPER
     |========================================================= */

    /** Periode dari query ?from=&to= (default: awal bulan sampai hari ini, maksimal 366 hari). */
    private function period(Request $request): array
    {
        $parse = function (?string $value, Carbon $fallback): Carbon {
            try {
                return $value ? Carbon::parse($value)->startOfDay() : $fallback;
            } catch (\Throwable) {
                return $fallback;
            }
        };

        $from = $parse($request->input('from'), Carbon::today()->startOfMonth());
        $to   = $parse($request->input('to'), Carbon::today());

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }
        if ($from->diffInDays($to) > 365) {
            $from = $to->copy()->subDays(365);
        }

        return [$from, $to];
    }

    private function revenueBetween(Carbon $from, Carbon $to): float
    {
        return (float) Payment::where('status', 'Dibayar')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->sum('amount');
    }

    /** @return Collection<string, float> tanggal (Y-m-d) => total pemasukan */
    private function revenueByDay(Carbon $from, Carbon $to): Collection
    {
        return Payment::where('status', 'Dibayar')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as d, SUM(amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd');
    }

    /** Persentase perubahan; null jika tidak ada pembanding. */
    private function trend(float|int $current, float|int $previous): ?float
    {
        if ($previous <= 0) {
            return $current > 0 ? null : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /** Paket terlaris berdasarkan jumlah paket yang dipesan. */
    private function topPackages(Carbon $from, Carbon $to, int $limit = 5): Collection
    {
        return DB::table('order_packages as op')
            ->join('orders as o', 'o.id', '=', 'op.order_id')
            ->join('packages as p', 'p.id', '=', 'op.package_id')
            ->whereBetween('o.order_date', [$from->toDateString(), $to->toDateString()])
            ->select('p.name', DB::raw('SUM(op.quantity) as sold'), DB::raw('SUM(op.subtotal) as amount'))
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('sold')
            ->limit($limit)
            ->get();
    }

    /** Menu yang paling banyak disiapkan (termasuk isi paket), dalam porsi. */
    private function topMenus(Carbon $from, Carbon $to, int $limit = 5): Collection
    {
        return DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('menus as m', 'm.id', '=', 'od.menu_id')
            ->whereBetween('o.order_date', [$from->toDateString(), $to->toDateString()])
            ->select('m.name', DB::raw('SUM(od.quantity) as sold'))
            ->groupBy('m.id', 'm.name')
            ->orderByDesc('sold')
            ->limit($limit)
            ->get();
    }
}