<?php

namespace App\Console;

use App\Console\Commands\BlogCleaner;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /* Set the command in cron
            1) crontab -e
            2) *    *    *    *    *    php /var/www/html/Laravel/laravel-training/artisan schedule:run >> /dev/null 2>&1

            OR
            run `php artisan schedule:work`
         */
        $schedule->command(BlogCleaner::class)->weekly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
