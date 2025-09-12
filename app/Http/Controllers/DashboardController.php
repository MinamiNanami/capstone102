<?php

namespace App\Http\Controllers;

use App\Models\PetCheckup;
use App\Models\PosSale;
use App\Models\PetInventory;
use App\Models\Transaction;
use App\Models\Schedule;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Previous sales & clients
        $totalPreviousDaySales = PosSale::whereDate('created_at', Carbon::yesterday())->sum('total');
        $totalPreviousWeekSales = PosSale::whereBetween('created_at', [
            Carbon::now()->subWeeks(2)->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->sum('total');

        $totalDailySales = PosSale::whereDate('created_at', today())->sum('total');
        $totalWeeklySales = PosSale::where('created_at', '>=', now()->subDays(7))->sum('total');
        $totalDailyClients = PosSale::whereDate('created_at', today())->count('id');
        $totalWeeklyClients = PosSale::where('created_at', '>=', now()->subDays(7))->count('id');

        // Daily transactions
        $dailyTransactions = Transaction::with('items')
            ->whereDate('created_at', today())
            ->get();

        // Schedules
        $schedules = Schedule::all();

        // Inventory items expiring in next 30 days
        $today = Carbon::today();
        $nextMonth = Carbon::today()->addMonth();
        $expiringItems = InventoryItem::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [$today->toDateString(), $nextMonth->toDateString()])
            ->get();

        return view('dashboard', compact(
            'totalDailySales',
            'totalWeeklySales',
            'totalDailyClients',
            'totalWeeklyClients',
            'totalPreviousDaySales',
            'totalPreviousWeekSales',
            'dailyTransactions',
            'schedules',
            'expiringItems' // pass expiring items to blade
        ));
    }

    public function getCommonDiseasesData()
    {
        $checkupDiseases = PetCheckup::select('disease', DB::raw('count(*) as total'))
            ->whereNotNull('disease')
            ->groupBy('disease')
            ->pluck('total', 'disease');

        return response()->json([
            'labels' => $checkupDiseases->keys(),
            'data' => $checkupDiseases->values(),
        ]);
    }

    public function getMonthlyClientsData()
    {
        $currentYear = Carbon::now()->year;

        $monthlyClients = PosSale::selectRaw('MONTH(created_at) as month, COUNT(DISTINCT customer_name) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('F');
            $data[] = $monthlyClients->has($i) ? $monthlyClients[$i]->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getMonthlySalesData()
    {
        $currentYear = Carbon::now()->year;

        $monthlySales = PosSale::selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('F');
            $data[] = $monthlySales->has($i) ? round($monthlySales[$i]->total, 2) : 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
