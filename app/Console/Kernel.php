<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\UpdateHomeUsage;

class Kernel extends ConsoleKernel
{
    protected $commands = [
    \App\Console\Commands\DistributeUsageFromRaw::class,
];

protected function schedule(Schedule $schedule)
{
    $schedule->command('distribute:home-usage')->everyMinute();
}

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
