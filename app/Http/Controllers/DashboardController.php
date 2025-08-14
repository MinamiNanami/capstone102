<?php

namespace App\Http\Controllers;

use App\Models\PetCheckup;
use App\Models\PosSale;
use App\Models\PetInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPreviousDaySales = PosSale::whereDate('created_at', Carbon::yesterday())->sum('total');
        $totalPreviousWeekSales = PosSale::whereBetween('created_at', [
            Carbon::now()->subWeeks(2)->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->sum('total');


        $totalDailySales = PosSale::whereDate('created_at', today())->sum('total');
        $totalWeeklySales = PosSale::where('created_at', '>=', now()->subDays(7))->sum('total');
        $totalDailyClients = PosSale::whereDate('created_at', today())->count('id');
        $totalWeeklyClients = PosSale::where('created_at', '>=', now()->subDays(7))->count('id');

        return view('dashboard', compact(
            'totalDailySales',
            'totalWeeklySales',
            'totalDailyClients',
            'totalWeeklyClients',
            'totalPreviousDaySales',
            'totalPreviousWeekSales',
        ));
    }

    public function getCommonDiseasesData()
    {
        // Get diseases from PetCheckup
        $checkupDiseases = PetCheckup::select('disease', DB::raw('count(*) as total'))
            ->whereNotNull('disease')
            ->groupBy('disease')
            ->pluck('total', 'disease');

        // Get diseases from PetInventory
        $inventoryDiseases = PetInventory::select('disease', DB::raw('count(*) as total'))
            ->whereNotNull('disease')
            ->groupBy('disease')
            ->pluck('total', 'disease');

        // Combine both collections by disease name and sum their totals
        $combinedDiseases = $checkupDiseases->merge($inventoryDiseases)
            ->groupBy(function ($value, $key) {
                return $key; // group by disease name (key)
            })
            ->map(function ($group) {
                return $group->sum();
            });

        $diseaseLabels = $combinedDiseases->keys();
        $diseaseData = $combinedDiseases->values();

        return response()->json([
            'labels' => $diseaseLabels,
            'data' => $diseaseData,
        ]);
    }


    public function getMonthlyClientsData()
    {
        $currentYear = Carbon::now()->year;

        // Get count of distinct clients per month
        $monthlyClients = PosSale::selectRaw('MONTH(created_at) as month, COUNT(DISTINCT customer_name) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Generate labels and values for all 12 months
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

        // Get sum of sales per month
        $monthlySales = PosSale::selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Generate labels and values for all 12 months
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
