<?php

namespace App\Http\Controllers;

use App\Models\HomeUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\PDF as DomPDF;  // Changed to use the full namespace

class ReportsController extends Controller
{
    public function index2()
    {
        return view('reports');
    }

    public function generate(Request $request, DomPDF $pdf)  // Inject PDF dependency
    {
        $request->validate([
            'report_type' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required_if:report_type,custom|date',
            'end_date' => 'required_if:report_type,custom|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,html',
        ]);

        $userId = Auth::id();
        $reportType = $request->report_type;
        $format = $request->format;

        // Determine date range based on report type
        switch ($reportType) {
            case 'daily':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                $title = "Daily Water Usage Report";
                break;

            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $title = "Weekly Water Usage Report";
                break;

            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $title = "Monthly Water Usage Report";
                break;

            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $title = "Annual Water Usage Report";
                break;

            case 'custom':
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                $title = "Custom Water Usage Report ({$startDate->format('M d, Y')} to {$endDate->format('M d, Y')})";
                break;
        }

        // Get usage data
        $usageData = HomeUsage::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        // Group by area and date
$groupedData = $usageData->groupBy([
    'area',
    function ($item) use ($reportType) {
        $date = Carbon::parse($item->date);  // Force convert to Carbon

        switch ($reportType) {
            case 'daily': return $date->format('H:00');
            case 'weekly': return $date->format('l');
            case 'monthly': return $date->format('M d');
            case 'yearly': return $date->format('M');
            default: return $date->format('Y-m-d');
        }
    }
]);

        // Calculate totals
        $totalUsage = $usageData->sum('usage');
        $averageDailyUsage = $totalUsage / max(1, $startDate->diffInDays($endDate) + 1);

        // Prepare data for view
        $reportData = [
            'title' => $title,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'groupedData' => $groupedData,
            'totalUsage' => $totalUsage,
            'averageDailyUsage' => $averageDailyUsage,
            'reportType' => $reportType,
            'user' => Auth::user(),
        ];

        // Return in requested format
        if ($format === 'pdf') {
            $pdf = $pdf->loadView('reports.pdf', $reportData);
            return $pdf->download("water_usage_report_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.pdf");
        }

        return view('html', $reportData);
    }
}
