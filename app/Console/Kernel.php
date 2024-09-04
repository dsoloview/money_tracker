<?php

namespace App\Console;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use App\Services\Newsletter\Sender\NewsletterSender;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('fetch:exchange-rates')->daily();
        $schedule->command('telescope:prune')->daily();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->call(function () {
            $newsletterSender = app(NewsletterSender::class);
            $newsletterSender->send(NewsletterPeriodsEnum::DAILY);
        })->dailyAt('12:00');
        $schedule->call(function () {
            $newsletterSender = app(NewsletterSender::class);
            $newsletterSender->send(NewsletterPeriodsEnum::WEEKLY);
        })->weeklyOn(1, '13:00');
        $schedule->call(function () {
            $newsletterSender = app(NewsletterSender::class);
            $newsletterSender->send(NewsletterPeriodsEnum::MONTHLY);
        })->monthlyOn(1, '14:00');
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
