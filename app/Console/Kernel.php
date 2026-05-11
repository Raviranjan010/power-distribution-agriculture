<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Mark overdue bills daily at midnight
        $schedule->call(function () {
            \App\Models\Bill::where('status', 'pending')
                ->where('due_date', '<', now()->startOfDay())
                ->update(['status' => 'overdue']);
        })->daily()->description('Mark overdue bills');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
