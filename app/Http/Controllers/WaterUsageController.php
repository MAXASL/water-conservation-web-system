<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterUsage;
use App\Models\HomeUsage;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WaterUsageController extends Controller
{
    public function index(Request $request)
{
    $year = $request->input('year', date('Y'));
    $month = $request->input('month', date('m'));
    $week = $request->input('week');
    $householdId = $request->input('household');

    // Get all households for dropdown
    $households = User::where('id', '!=', 0)->get();

    // Only proceed if a household is selected
    if (!$householdId) {
        return view('water_usage.index', [
            'water_usage' => collect(), // Empty result
            'year' => $year,
            'month' => $month,
            'week' => $week,
            'households' => $households
        ]);
    }

    // Determine date range
    if ($week) {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfWeek()->addWeeks($week - 1);
        $endDate = $startDate->copy()->endOfWeek();
        $dateDisplay = $startDate->format('d M') . ' - ' . $endDate->format('d M, Y');
    } else {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $dateDisplay = $startDate->format('F Y');
    }

    // Fetch the selected user with filtered sensor readings
    $user = User::with(['sensorReadings' => function($query) use ($startDate, $endDate) {
        $query->whereBetween('date', [$startDate, $endDate]);
    }])->find($householdId);

    $water_usage = collect();

    if ($user) {
        $totalUsage = $user->sensorReadings->sum('usage');

        $water_usage->push((object)[
            'date_range' => $dateDisplay,
            'usages' => $totalUsage,
            'alert' => $totalUsage > 0 ? $this->getAlertMessage($totalUsage) : 'No usage data',
            'location' => $user->house_address,
            'houseNo' => $user->id,
            'user' => $user
        ]);
    }

    return view('water_usage.index', compact('water_usage', 'year', 'month', 'week', 'households'));
}

    private function getAlertMessage($usage, $area = null)
{
    $averageUsage = [
        'kitchen' => 150,
        'bathroom' => 200,
        'garden' => 100,
        'default' => 500
    ];

    $threshold = $area ? ($averageUsage[$area] ?? $averageUsage['default']) : $averageUsage['default'];

    if ($usage < $threshold) {
        return ['message' => 'Good usage', 'is_alert' => false];
    } elseif ($usage <= $threshold * 1.4) { // Fixed condition
        return ['message' => 'High usage alert', 'is_alert' => true];
    } else {
        return ['message' => 'Leakage or maintenance alert', 'is_alert' => true];
    }
}
    public function alerts(Request $request)
{
    $year = $request->input('year', Carbon::now()->year);
    $month = $request->input('month', Carbon::now()->month);
    $week = $request->input('week', Carbon::now()->weekOfMonth);

    $users = User::where('id', '!=', 0)
               ->with(['sensorReadings' => function($query) use ($year, $month, $week) {
                   $query->whereYear('date', $year)
                         ->whereMonth('date', $month);

                   if ($week) {
                       $startDate = Carbon::createFromDate($year, $month, 1)
                           ->startOfWeek()
                           ->addWeeks($week - 1);
                       $endDate = $startDate->copy()->endOfWeek();
                       $query->whereBetween('date', [$startDate, $endDate]);
                   }
               }])->get();

    $water_usage = collect();

    foreach ($users as $user) {
        $totalUsage = $user->sensorReadings->sum('usage');

        // Ensure getAlertMessage() always returns a valid array
        $alertData = $this->getAlertMessage($totalUsage) ?? [
            'message' => 'No alert data available',
            'is_alert' => false
        ];

        $water_usage->push((object)[
            'date_range' => $week ?
                Carbon::createFromDate($year, $month, 1)
                    ->startOfWeek()
                    ->addWeeks($week - 1)
                    ->format('d M') . ' - ' .
                Carbon::createFromDate($year, $month, 1)
                    ->startOfWeek()
                    ->addWeeks($week - 1)
                    ->endOfWeek()
                    ->format('d M, Y') :
                Carbon::createFromDate($year, $month, 1)->format('F Y'),
            'usages' => $totalUsage,
            'alert' => $alertData['message'], // Now guaranteed to exist
            'is_alert' => $alertData['is_alert'], // Now guaranteed to exist
            'user' => $user
        ]);
    }

    return view('water_usage.alerts', compact('water_usage', 'year', 'month', 'week'));
}
public function sendAlert(Request $request, $userId)
{
    $user = User::findOrFail($userId);
    $usage = $request->input('usage');
    $alertType = $request->input('alert_type');

    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USERNAME');
        $mail->Password   = env('MAIL_PASSWORD');
        $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
        $mail->Port       = env('MAIL_PORT', 587);

        // Timeout settings
        $mail->Timeout = 30; // 30 seconds
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Recipients
        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'Water Monitoring System'));
        $mail->addAddress($user->email);

        // Content
        if ($alertType === 'alert') {
            $mail->Subject = 'Water Usage Alert';
            $mail->Body = "Dear {$user->firstname},\n\n" .
                         "Your water usage of {$usage}L has triggered an alert: " .
                         $this->getAlertMessage($usage) . "\n\n" .
                         "Please review your water consumption.\n\n" .
                         "Water Monitoring System";
        } else {
            $mail->Subject = 'Water Usage Report';
            $mail->Body = "Dear {$user->firstname},\n\n" .
                         "Here is your weekly water usage report: {$usage}L\n\n" .
                         "Status: " . $this->getAlertMessage($usage) . "\n\n" .
                         "Water Monitoring System";
        }

        $mail->send();
        return back()->with('success', "Alert sent to {$user->email} successfully!");
    } catch (Exception $e) {
        Log::error("Email sending failed: " . $e->getMessage());
        return back()->with('error', "Failed to send email. Please try again later.");
    }
}

    // app/Http/Controllers/WaterUsageController.php
    // Controller (WaterUsageController.php)
// Controller (WaterUsageController.php)
public function usagePattern()
{
    // Expanded bounds to include sabbatical areas and Great North Road
    $users = User::with(['sensorReadings' => function($query) {
        $query->where('date', '>=', now()->subDays(7))
              ->selectRaw('user_id, SUM(usage) as total_usage')
              ->groupBy('user_id');
    }])
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->whereBetween('latitude', [-14.300, -14.280])  // Expanded bounds
    ->whereBetween('longitude', [28.545, 28.565])   // Includes Great North Road
    ->get();

    $mapData = $users->map(function ($user) {
        return [
            'lat' => $user->latitude,
            'lng' => $user->longitude,
            'label' => $user->firstname . ' ' . $user->lastname,
            'address' => $user->address,
            'usage' => $user->homeUsages->first()->total_usage ?? 0,
            'house_number' => $user->house_number ?? 'N/A',
            'area_type' => $user->area_type ?? 'residential', // sabbatical/residential/commercial
            'building_image' => $user->building_image ?? null
        ];
    });

    return view('water_usage.pattern', [
        'mapData' => $mapData,
        'center' => ['lat' => -14.292, 'lng' => 28.555], // Centered on campus
        'important_locations' => [
            'Main Campus' => [-14.292117, 28.555248],
            'Sabbatical Area' => [-14.2945, 28.553],
            'Great North Road' => [-14.293, 28.550]
        ]
    ]);
}


public function charts(Request $request)
{
    $filteredUserId = $request->input('user_id'); // from dropdown or search

    // Daily usage - filter if user ID is provided
    $usageByDayQuery = HomeUsage::whereHas('user', function ($query) {
        $query->where('is_admin', false);
    });

    if ($filteredUserId) {
        $usageByDayQuery->where('user_id', $filteredUserId);
    }

    $usageByDay = $usageByDayQuery
        ->selectRaw('DATE(date) as day, SUM(`usage`) as total_usage')
        ->whereDate('date', '>=', Carbon::now()->subDays(7))
        ->groupBy('day')
        ->orderBy('day')
        ->get();

    // Top consumers, only show filtered if specified, otherwise all
    $usersQuery = User::where('is_admin', false);

    if ($filteredUserId) {
        $usersQuery->where('id', $filteredUserId);
    }

    $topConsumers = $usersQuery
        ->with(['sensorReadings' => function ($query) {
            $query->whereDate('date', '>=', Carbon::now()->subDays(7))
                ->selectRaw('user_id, SUM(`usage`) as total_usage')
                ->groupBy('user_id');
        }])
        ->get()
        ->map(function ($user) {
            $totalUsage = $user->sensorReadings->sum('total_usage');
            return [
                'name' => $user->firstname . ' ' . $user->lastname,
                'usage' => $totalUsage,
                'area' => $user->area_type ?? 'Unknown',
            ];
        })
        ->filter(fn($user) => $user['usage'] > 0)
        ->sortByDesc('usage')
        ->take(5)
        ->values();

    $normalUsage = $usageByDay->sum(fn($item) => $item->total_usage <= 500 ? $item->total_usage : 0);
    $excessiveUsage = $usageByDay->sum(fn($item) => $item->total_usage > 500 ? $item->total_usage : 0);

    return view('water_usage.charts', [
        'usageByDay' => $usageByDay,
        'topConsumers' => $topConsumers,
        'normalUsage' => $normalUsage,
        'excessiveUsage' => $excessiveUsage,
        'selectedUserId' => $filteredUserId,
        'users' => User::where('is_admin', false)->get(), // for dropdown
    ]);
}

    // leak detection code here
    public function detectLeaks()
{
    // First get average usages per user
    $avgUsages = HomeUsage::whereHas('user', fn($q) => $q->where('is_admin', false))
        ->selectRaw('user_id, AVG(`usage`) as avg_usage')
        ->where('date', '>=', now()->subDays(7))
        ->groupBy('user_id')
        ->get()
        ->keyBy('user_id');

    // Then find users with usage spikes
    $leakSuspects = HomeUsage::whereHas('user', fn($q) => $q->where('is_admin', false))
        ->where('date', '>=', now()->subDays(7))
        ->with('user')
        ->get()
        ->groupBy('user_id')
        ->map(function($readings, $userId) use ($avgUsages) {
            $avg = $avgUsages->get($userId)->avg_usage ?? 0;
            $spikes = $readings->filter(fn($r) => $r->usage > ($avg * 3))->count();

            return $spikes > 2 ? [
                'user' => $readings->first()->user->name,
                'avg_usage' => round($avg, 2),
                'spike_count' => $spikes,
                'last_reading' => $readings->last()->usage
            ] : null;
        })
        ->filter()
        ->values();

    return view('water_usage.leaks', [
        'leakSuspects' => $leakSuspects,
        'continuousFlow' => [] // Add your continuous flow logic here
    ]);
}

// code for the usage history page
public function history(Request $request)
{
    // Get filter parameters
    $month = $request->input('month', date('m'));
    $year = $request->input('year', date('Y'));
    $userId = $request->input('user_id');

    // Base query - using backticks around the reserved keyword
    $query = HomeUsage::with('user')
        ->whereHas('user', function($q) {
            $q->where('is_admin', false);
        })
        ->selectRaw('
            user_id,
            DATE(date) as usage_date,
            SUM(`usage`) as total_usage,
            AVG(`usage`) as avg_usage,
            MAX(`usage`) as peak_usage,
            MIN(`usage`) as min_usage
        ')
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->groupBy('user_id', 'usage_date')
        ->orderBy('usage_date', 'desc');

    // Filter by user if selected
    if ($userId) {
        $query->where('user_id', $userId);
    }

    // Get usage data
    $usageData = $query->get()
        ->groupBy('user_id')
        ->map(function($userUsage) {
            return [
                'user' => $userUsage->first()->user,
                'usage_days' => $userUsage->map(function($day) {
                    return [
                        'date' => $day->usage_date,
                        'total_usage' => $day->total_usage,
                        'avg_usage' => round($day->avg_usage, 2),
                        'peak_usage' => $day->peak_usage,
                        'min_usage' => $day->min_usage,
                        'is_high' => $day->total_usage > 500,
                        'is_leak' => $day->peak_usage > 100,
                        'is_optimal' => $day->avg_usage < 50
                    ];
                })
            ];
        });

    // Get all household users for filter dropdown
    $householdUsers = User::where('is_admin', false)
        ->orderBy('firstname')
        ->get();

    return view('water_usage.history', [
        'usageData' => $usageData,
        'householdUsers' => $householdUsers,
        'filters' => [
            'month' => $month,
            'year' => $year,
            'user_id' => $userId
        ],
        'months' => collect(range(1, 12))->mapWithKeys(function($m) {
            return [$m => date('F', mktime(0, 0, 0, $m, 1))];
        }),
        'years' => collect(range(date('Y') - 5, date('Y')))->reverse()
    ]);
}
}
