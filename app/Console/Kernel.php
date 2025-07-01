<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Thêm dòng sau vào đây:
        $schedule->command('qr:auto-refresh')->everyThirtySeconds(); 
        // $schedule->command('qr:auto-refresh')->everyFiveMinutes();
        // $schedule->command('qr:auto-refresh')->everyTenMinutes();
        // $schedule->command('qr:auto-refresh')->everyThirtySeconds(); // Laravel 10+ mới có

    }


    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
