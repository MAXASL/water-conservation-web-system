<?php

namespace App\Http\Controllers;

use App\Models\HomeUsage;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index0(Request $request)
{
    // Get authenticated user ID
    $userId = Auth::id();

    if (!$userId) {
        return redirect()->route('login')->with('error', 'Please login to view usage');
    }

    // Get filter values
    $year = $request->input('year', now()->year);
    $month = $request->input('month', now()->month);
    $week = $request->input('week');
    $day = $request->input('day');

    // Build query with user constraint
    $query = HomeUsage::where('user_id', $userId)
                      ->whereYear('date', $year)
                      ->whereMonth('date', $month);

    // Week filter
    if ($week) {
        $startDate = Carbon::create($year, $month, 1)->startOfWeek()->addWeeks($week - 1);
        $endDate = $startDate->copy()->endOfWeek();
        $query->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
    }

    // Day filter
    if ($day) {
        $query->whereRaw("DAYNAME(date) = ?", [$day]);
    }

    // Get latest records per area for this user
    $subQuery = clone $query;
    $latestRecords = $subQuery->select('area', DB::raw('MAX(created_at) as latest_time'))
                              ->groupBy('area');

    $latestUsages = HomeUsage::select('sensor.*')
        ->joinSub($latestRecords, 'latest', function ($join) {
            $join->on('sensor.area', '=', 'latest.area')
                 ->on('sensor.created_at', '=', 'latest.latest_time');
        })
        ->where('sensor.user_id', $userId)
        ->get();

    // Prepare result
    $areas = ['kitchen', 'bathroom', 'garden'];
    $totalUsage = array_fill_keys($areas, 0);

    foreach ($latestUsages as $usage) {
        $area = strtolower($usage->area);
        if (in_array($area, $areas)) {
            $totalUsage[$area] = $usage->usage;
        }
    }

    // Average usage thresholds
    $averageUsage = [
        'kitchen' => 100,
        'bathroom' => 200,
        'garden' => 100,
    ];

    // ===== NEW PAYMENT CALCULATION CODE =====
        $ratePerLiter = 0.20; // ZMW 0.20 per liter (20 ngwee)
        $dueDate = now()->addDays(15)->format('Y-m-d');
        $totalAmountDue = array_sum($totalUsage) * $ratePerLiter;

    // Calculate payment for each area
    foreach ($totalUsage as $area => $usage) {
        $totalAmountDue += $usage * $ratePerLiter;
    }

    // Create payment record if doesn't exist
    $payment = Payment::firstOrCreate(
        ['user_id' => $userId, 'due_date' => $dueDate],
        [
            'amount_due' => $totalAmountDue,
            'status' => 'pending',
            'rate_per_liter' => $ratePerLiter
        ]
    );

    // Get payment summary for display
    $paymentSummary = [
        'total_due' => $totalAmountDue,
        'due_date' => $dueDate,
        'days_remaining' => now()->diffInDays($dueDate),
        'payment_link' => route('payment.show', $payment->id),
        'rate_per_liter' => $ratePerLiter
    ];
    // ===== END PAYMENT CODE =====


    // ----- Goals -----
$dailyGoal = 100;
$weeklyGoal = 700;
$monthlyGoal = 3000;

// ----- Calculate usage totals -----

// Daily Usage (filtered day if selected, otherwise today's usage)
$dailyUsageQuery = HomeUsage::where('user_id', $userId)
    ->whereYear('date', $year)
    ->whereMonth('date', $month);

if ($day) {
    $dailyUsageQuery->whereRaw("DAYNAME(date) = ?", [$day]);
} else {
    $dailyUsageQuery->whereDate('date', now()->toDateString());
}
$dailyUsage = $dailyUsageQuery->sum('usage');

// Weekly Usage (based on selected week)
$weeklyUsageQuery = HomeUsage::where('user_id', $userId)
    ->whereYear('date', $year)
    ->whereMonth('date', $month);

if ($week) {
    $startDate = Carbon::createFromDate($year, $month, 1)->startOfWeek()->addWeeks($week - 1);
    $endDate = $startDate->copy()->endOfWeek();
    $weeklyUsageQuery->whereBetween('date', [$startDate, $endDate]);
}
$weeklyUsage = $weeklyUsageQuery->sum('usage');

// Monthly Usage (entire selected month)
$monthlyUsage = HomeUsage::where('user_id', $userId)
    ->whereYear('date', $year)
    ->whereMonth('date', $month)
    ->sum('usage');

// ----- Progress Percentages -----
$dailyProgress = ($dailyUsage / $dailyGoal) * 100;
$weeklyProgress = ($weeklyUsage / $weeklyGoal) * 100;
$monthlyProgress = ($monthlyUsage / $monthlyGoal) * 100;


    // Alerts for high usage (debugging)
$alerts = [];
$alertDataForEmail = [];

foreach ($totalUsage as $area => $usage) {
    if ($usage > 0 && $usage > $averageUsage[$area] * 1.2) {
        $alertMessage = "⚠️ High water usage detected in " . ucfirst($area) . " on {$day} Used: {$usage} litres (Threshold: " . ($averageUsage[$area] * 1.2) . " litres)";
        $alerts[] = $alertMessage;

        $alertDataForEmail[] = [
            'area' => ucfirst($area),
            'usage' => $usage,
            'threshold' => $averageUsage[$area] * 1.2,
            'day' => $day,
        ];
    }
}

// Send email if alerts exist and user is logged in
if (!empty($alertDataForEmail) && Auth::check()) {
    $this->sendWaterAlertEmail($alertDataForEmail, Auth::user());
}
        // Fetch water-saving recommendations
$tips = [
    [
        "title" => "Fix leaks immediately",
        "description" => "A single dripping faucet can waste up to 20 liters of water per day. Regularly inspect and repair leaks in pipes, faucets, and toilets."
    ],
    [
        "title" => "Reduce shower time",
        "description" => "Shorten your showers to five minutes or less. Consider installing low-flow showerheads to minimize water usage."
    ],
    [
        "title" => "Optimize garden irrigation",
        "description" => "Water your garden early in the morning or late in the evening to prevent water loss due to evaporation. Use drip irrigation systems for efficiency."
    ],
    [
        "title" => "Turn off taps when not in use",
        "description" => "When brushing your teeth, washing dishes, or scrubbing your hands, turn off the tap to save water."
    ],
    [
        "title" => "Use water-efficient appliances",
        "description" => "Choose dishwashers and washing machines that have water-saving settings. Run them only with full loads to maximize efficiency."
    ],
    [
        "title" => "Harvest and reuse rainwater",
        "description" => "Collect rainwater for watering plants, washing outdoor surfaces, and other non-potable uses."
    ],
    [
        "title" => "Use a bucket instead of a hose",
        "description" => "When washing your car or cleaning outdoor areas, use a bucket instead of a running hose to reduce water waste."
    ],
    [
        "title" => "Install water-efficient fixtures",
        "description" => "Upgrade to water-saving faucets, dual-flush toilets, and aerators to reduce water consumption."
    ],
    [
        "title" => "Check for hidden leaks",
        "description" => "Regularly inspect your plumbing system for leaks that may go unnoticed, such as silent toilet leaks."
    ],
    [
        "title" => "Reuse water where possible",
        "description" => "Instead of discarding used water from cooking (such as rinsing vegetables), repurpose it for watering plants or other household tasks."
    ]
];


/*
        // Fetch real-time weather data
        $apiKey = "a48bfc5fb86e885bcfb575706eba5408";
        $city = "Kabwe";
        $weatherResponse = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'q' => $city,
            'appid' => $apiKey,
            'units' => 'metric',
        ]);

        if ($weatherResponse->successful()) {
            $weatherData = $weatherResponse->json();
            $temperature = $weatherData['main']['temp'] . "°C";
            $weatherCondition = ucfirst($weatherData['weather'][0]['description']);
        } else {
            $temperature = "N/A";
            $weatherCondition = "Unable to fetch weather data";
        }

        // Generate weather-based water-saving recommendations
        $weatherRecommendation = "Always use water efficiently!";
        if (strpos(strtolower($weatherCondition), 'rain') !== false) {
            $weatherRecommendation = "It's raining today! Avoid watering your garden.";
        } elseif (strpos(strtolower($weatherCondition), 'hot') !== false || $temperature > 30) {
            $weatherRecommendation = "It's hot today—consider using water-efficient irrigation!";
        } elseif (strpos(strtolower($weatherCondition), 'cold') !== false) {
            $weatherRecommendation = "Cold weather—reduce unnecessary water usage.";
        }*/

        return view('homee', [
            'totalUsage' => $totalUsage,
            'averageUsage' => $averageUsage,
            'tips'=> $tips,
            'alerts' => $alerts,
            'day'=>$day,
            //'temperature' => $temperature,
            //'weatherCondition' => $weatherCondition,
           // 'weatherRecommendation' => $weatherRecommendation,
            'selectedDay'=> $day,
            'selectedYear'=> $year,
            'selectedMonth'=> $month,
            'selectedWeek'=> $week,
            'dailyUsage' => array_sum($totalUsage),
            'weeklyUsage' => $weeklyUsage,
            'monthlyUsage' => HomeUsage::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('usage'),
            'dailyProgress' => $dailyProgress,
            'weeklyProgress' => $weeklyProgress,
            'monthlyProgress' => $monthlyProgress,
            'paymentSummary' => $paymentSummary,
            'payment' => $payment

        ]);
    }

    public function homee() {
        return view('homee');  // Corresponds to resources/views/homee.blade.php
    }


    //incentives code here


    public function getProgressData(Request $request)
{
    $userId = 1; // Or Auth::id()
    $year = $request->input('year', now()->year);
    $month = $request->input('month', now()->month);
    $week = $request->input('week');
    $day = $request->input('day');

    // Same filtering logic as in your index0 method
    $query = HomeUsage::where('user_id', $userId)
        ->whereYear('date', $year)
        ->whereMonth('date', $month);

    if ($day) {
        $query->whereRaw("LOWER(DAYNAME(date)) = ?", [strtolower($day)]);
    }

    if ($week) {
        $startDate = Carbon::create($year, $month, 1)->startOfWeek()->addWeeks($week - 1);
        $endDate = $startDate->copy()->endOfWeek();
        $query->whereBetween('date', [$startDate, $endDate]);
    }

    $usageData = $query->get();

    $totalUsage = $usageData->sum('usage');

    return response()->json([
        'dailyUsage' => $totalUsage, // Assuming you're viewing one day
        'weeklyUsage' => $totalUsage,
        'monthlyUsage' => $totalUsage,
        'dailyProgress' => $totalUsage / 100 * 100,
        'weeklyProgress' => $totalUsage / 700 * 100,
        'monthlyProgress' => $totalUsage / 3000 * 100,
    ]);
}


// sending of email to login user automatically
private function sendWaterAlertEmail($alerts, $user)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'maxaslukwesa0@gmail.com'; // Your email
        $mail->Password = 'jrqncounfrllhdgr'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('maxaslukwesa0@gmail.com', 'Water Monitoring System');
        $mail->addAddress($user->email); // Send to logged-in user
        $mail->Subject = '🚨 High Water Usage Alert';

        // Build email body from alerts
        $emailBody = "Dear {$user->first_name},\n\n";
        $emailBody .= "We've detected high water usage in your household:\n\n";

        foreach ($alerts as $alert) {
            $emailBody .= "⚠️ {$alert['area']} Usage:\n";
            $emailBody .= "- Used: {$alert['usage']} litres\n";
            $emailBody .= "- Threshold: {$alert['threshold']} litres\n";
            $emailBody .= "- Day: {$alert['day']} (day {$alert['day']})\n\n";
        }

        $emailBody .= "Please check for leaks or reduce usage.\n\n";
        $emailBody .= "Thank you for conserving water!\n";
        $emailBody .= "The Water Conservation Team";

        $mail->Body = $emailBody;
        $mail->send();

        Log::info("Water alert email sent to {$user->email}");
    } catch (Exception $e) {
        Log::error("Failed to send water alert email: {$mail->ErrorInfo}");
    }
}

public function incentives()
    {
        return view('incentive');
    }

// sensor inserting data into database here
public function store(Request $request)
{
    $request->validate([
        'flow_rate' => 'required|numeric',
        'total_used' => 'required|numeric',
    ]);

    $flowRate = floatval($request->input('flow_rate'));
    $totalUsed = floatval($request->input('total_used'));
    $userId = Auth::id();

    if ($flowRate == 0 && $totalUsed == 0) {
        return response()->json(['status' => 'ignored', 'message' => 'Zero flow']);
    }

    // Distribution logic
    $distribution = [
        'kitchen' => 0.4,
        'bathroom' => 0.4,
        'garden' => 0.2,
    ];

    foreach ($distribution as $area => $percent) {
        $splitUsed = $totalUsed * $percent;

        HomeUsage::create([
            'user_id' => $userId,
            'area' => $area,
            'usage' => $splitUsed,
            'flow_rate' => $flowRate,
            'total_used' => $splitUsed, // ✅ Use split value here
            'date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return response()->json([
        'status' => 'stored',
        'user_id' => $userId,
        'flow_rate' => $flowRate,
        'total_used' => $totalUsed,
    ]);
}



// real-time API UPDATE


// --- FILE: updateUsage FUNCTION ---
public function updateUsage()
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $today = now()->toDateString();

    // Get the latest entry for each area for today
    $latestIds = DB::table('sensor')
        ->select(DB::raw('MAX(id) as id'))
        ->where('user_id', $userId)
        ->whereDate('date', $today)
        ->groupBy('area')
        ->pluck('id');

    // Get the latest usage records using those IDs
    $latestUsages = DB::table('sensor')
        ->whereIn('id', $latestIds)
        ->get();

    foreach ($latestUsages as $usageEntry) {
        DB::table('sensor')->updateOrInsert(
            [
                'user_id' => $userId,
                'date' => $today,
                'area' => $usageEntry->area
            ],
            [
                'usage'      => $usageEntry->usage,
                'total_used' => $usageEntry->total_used,
                'flow_rate'  => $usageEntry->flow_rate,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Usage data updated based on latest entry per area for today.',
    ]);
}

// summary code here
public function summary(Request $request)
{
    // Get the currently authenticated user
    $user = Auth::user();

    // Default to current month/year
    $month = $request->input('month', date('m'));
    $year = $request->input('year', date('Y'));

    // Base query scoped to current user
    $query = HomeUsage::with('user')
        ->where('user_id', $user->id) // ✅ Filter to the current user
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

    // Get usage data grouped and formatted
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

    return view('summary', [
        'usageData' => $usageData,
        'filters' => [
            'month' => $month,
            'year' => $year,
        ],
        'months' => collect(range(1, 12))->mapWithKeys(function($m) {
            return [$m => date('F', mktime(0, 0, 0, $m, 1))];
        }),
        'years' => collect(range(date('Y') - 5, date('Y')))->reverse()
    ]);
}
}


