<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\HomeUsage;
use Carbon\Carbon;

class DistributeUsageFromRaw extends Command
{
    protected $signature = 'distribute:home-usage';
    protected $description = 'Assign raw home_usage data to kitchen area and a user';

    public function handle()
    {
        Log::info("🚰 DistributeUsageFromRaw started at " . now());

        // Fetch unprocessed rows (missing user_id or area)
                $rawRows = DB::table('sensor')
            ->whereNull('user_id')
            ->whereNull('area') // 👈 Ensure both are null
            ->get();

        if ($rawRows->isEmpty()) {
            Log::info("No raw home_usage rows found.");
            return 0;
        }

        foreach ($rawRows as $row) {
    $userId = 1;
    $date = Carbon::parse($row->created_at)->toDateString();

    // Avoid duplicate usage
    HomeUsage::updateOrInsert(
        [
            'user_id' => $userId,
            'area' => 'kitchen',
            'date' => $date,
        ],
        [
            'usage' => $row->total_used,
            'flow_rate' => $row->flow_rate,
            'total_used' => $row->total_used,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    DB::table('sensor')->where('id', $row->id)->delete();
}
    }
}
