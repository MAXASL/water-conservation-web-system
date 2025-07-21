<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeUsage;

class ReportController extends Controller
{
    public function index0()
    {
        $usageData = HomeUsage::where('date', '>=', now()->subDays(7)->toDateString())->get()->groupBy('area');
        return view('reports', compact('usageData'));
    }
}
