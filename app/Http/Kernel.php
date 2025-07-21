<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\UpdateHomeUsage;

class Kernel extends ConsoleKernel
{

protected $routeMiddleware = [
    // ...
    'auth' => \App\Http\Middleware\AuthMiddleware::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class, // Add this line
];

}
