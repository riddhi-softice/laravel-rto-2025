<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use App\Jobs\SendDailyNotification;


class Kernel extends ConsoleKernel
{
    protected $commands = [
    ];

    /*  protected function schedule_old(Schedule $schedule)
    {
        // $schedule->command('notify:articles')->everyMinute();
        $schedule->command('notify:articles')->dailyAt('09:00');
        $schedule->command('notify:articles')->dailyAt('21:00');
        $schedule->command('store:commonprices')->dailyAt('00:00');
        
        $notifications = DB::table('app_notifications')->where(['type'=>'other','notification_type'=>'1'])->pluck('notification_data');
        foreach ($notifications as $notification) {
            $notification_data = json_decode($notification);
            $notification_time = $notification_data->notification_time;

            // Assuming you want to schedule the same command for all notifications
            $schedule->command('other:notification')->dailyAt($notification_time);
        }
        
        $schedule->command('ipo:fetch-and-store')
        // ->everyMinute();
        ->hourly()
        ->timezone('Asia/Kolkata') // Adjust timezone if necessary
        ->between('8:00', '16:00'); // Run between 8 AM and 4 PM
    }*/
    
    protected function schedule_cron(Schedule $schedule)
    { 
        // $schedule->command('notifications:dailysend')->everyMinute();
        // 10:30AM, 2:30PM, 6:30PM, 10:30PM
        $schedule->command('notifications:dailysend')->timezone('Asia/Kolkata')->dailyAt('10:30');
        $schedule->command('notifications:dailysend')->timezone('Asia/Kolkata')->dailyAt('14:30');
        $schedule->command('notifications:dailysend')->timezone('Asia/Kolkata')->dailyAt('18:30');
        $schedule->command('notifications:dailysend')->timezone('Asia/Kolkata')->dailyAt('22:30');
        
        $schedule->command('ipo:fetch-and-store')
        ->hourly()
        ->timezone('Asia/Kolkata') // Adjust timezone if necessary
        ->between('8:00', '16:00'); // Run between 8 AM and 4 PM
        
        $schedule->command('store:commonprices')->dailyAt('00:00');
    }
    
    protected function schedule(Schedule $schedule)
    {
        // Define the notifications
        $notifications = [
            [
                'title' => 'Quick DL Access!',
                'description' => '📥 Your Driving License is just a click away. Download it anytime, anywhere!'
            ],
            [
                'title' => '🪪 Driving License at Your Fingertips',
                'description' => '❌ No more physical copies! Enter details to get your official Driving License instantly.'
            ],
        ];
    
        // Get a random notification
        $randomNotification = $notifications[array_rand($notifications)];
    
        // Prepare data for sending notification
        $notificationSendData = [
            'notification_title' => $randomNotification['title'],
            'notification_description' => $randomNotification['description'],
            'notification_image' => asset('public/img/logo.png'),
            'notification_url' => "",
        ];
        
        $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->everyMinute();
     
        // Schedule jobs for each time slot
        // $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('10:30');
        // $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('14:30');
        // $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('18:30');
        // $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('22:30');
    }
    
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
