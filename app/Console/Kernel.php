<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;

class Kernel extends ConsoleKernel
{
    protected $commands = [
    ];

    protected function schedule(Schedule $schedule)
    {
        // // $schedule->command('notify:articles')->everyMinute();
        // $schedule->command('notify:articles')->dailyAt('09:00');
        // $schedule->command('notify:articles')->dailyAt('21:00');
        
        // $schedule->command('store:commonprices')->dailyAt('00:00');
        
        // $notifications = DB::table('app_notifications')->where(['type'=>'other','notification_type'=>'1'])->pluck('notification_data');
        // foreach ($notifications as $notification) {
        //     $notification_data = json_decode($notification);
        //     $notification_time = $notification_data->notification_time;

        //     // Assuming you want to schedule the same command for all notifications
        //     $schedule->command('other:notification')->dailyAt($notification_time);
        // }
        
        // $schedule->command('ipo:fetch-and-store')
        // // ->everyMinute();
        // ->hourly()
        // ->timezone('Asia/Kolkata') // Adjust timezone if necessary
        // ->between('8:00', '16:00'); // Run between 8 AM and 4 PM

        // 10:30AM, 2:30PM, 6:30PM, 10:30PM
        $schedule->command('notifications:dailysend')->timezone('Asia/Kolkata')->dailyAt('10:30');
        $schedule->command('notifications:dailysend')->timezone('Asia/Kolkata')->dailyAt('14:30');
        $schedule->command('notifications:dailysend')->timezone('Asia/Kolkata')->dailyAt('18:30');
        $schedule->command('notifications:dailysend')->timezone('Asia/Kolkata')->dailyAt('22:30');
        
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
