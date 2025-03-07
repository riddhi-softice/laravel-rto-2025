<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use App\Jobs\SendDailyNotification;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    protected $commands = [
    ];

    /* protected function schedule_old(Schedule $schedule)
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
         $schedule->command('ipo:fetch-and-store')
        ->hourly()
        ->timezone('Asia/Kolkata') // Adjust timezone if necessary
        ->between('8:00', '16:00'); // Run between 8 AM and 4 PM
        
        $schedule->command('store:commonprices')->dailyAt('00:00');
        
        
        // Define the notifications
         $notifications = [
            [
                'title' => 'Quick DL Access! 🪪',
                'description' => '📥 Your Driving License is just a click away. Download it anytime, anywhere!'
            ],
            [
                'title' => '🪪 Driving License at Your Fingertips',
                'description' => '❌ No more physical copies! Enter details to get your official Driving License instantly.'
            ],
            [
                'title' => '✅ Download Your Driving License Today!',
                'description' => 'Your Driving License is just a few taps away to go with digitally. Quick, secure, and convenient! 🪪'
            ],
            [
                'title' => '🚗 RC at Your Fingertips!',
                'description' => 'Check your RC details now!  🎫'
            ],
            [
                'title' => '🏦 Need a Loan? We\'ve Got You!',
                'description' => 'Find the best deals today!  🏛️'
            ],
            [
                'title' => '🚗 Download Your RC Book!',
                'description' => 'Download your RC Book in one click. Tap here to get started now!  🎫'
            ],
            [
                'title' => 'Your RC Book is Ready! 📄',
                'description' => '⬇️ Download your Vehicle RC Book instantly in one click now.'
            ],
            [
                'title' => '🛠️ Quick Fix? Nearby Garages!',
                'description' => 'Tap to find trusted services!  📄'
            ],
            [
                'title' => '📋 Clear Challans in Seconds!',
                'description' => 'Stay worry-free. Click now!  ⬇️'
            ],
            [
                'title' => 'Stay RTO Smart!📄',
                'description' => '🔎 Get quick access to all RTO services like RC, DL, and challans in one place.'
            ],
            [
                'title' => '📊 Plan Your Car Loan Smartly!',
                'description' => 'Use our Loan Calculator to find the best car loan options. Check it out now! 🏛️'
            ],
            [
                'title' => '💰Find the Best Loan Deals! 📈',
                'description' => 'Use our Vehicle Loan Calculator to plan your finances better.🏦'
            ],
            [
                'title' => 'Today’s Fuel Prices Are In! 📉',
                'description' => '🚗💨 Check the latest petrol and diesel prices in your city now.'
            ],
            [
                'title' => '🏷️ Buy Pre-Owned Cars!',
                'description' => '🔎 Looking for a car? Get pre-owned vehicles at guaranteed lowest prices.'
            ],
            [
                'title' => '🚗 Upgrade Your Vehicle! 🔄',
                'description' => 'Buy pre-owned cars at the lowest price, guaranteed! ☑️'
            ],
            [
                'title' => 'Upgrade Your Ride Today! 🚗',
                'description' => 'Ready for a new car? 🚘 Trade in your old one and get the best price today! 🤑'
            ],
            [
                'title' => '🔥 Don’t Miss Out – Affordable Car Deals!',
                'description' => 'Looking for a car upgrade? 🚗 Explore affordable options for your next vehicle today! 🌟'
            ],
            [
                'title' => '🌟 Find Your Dream Car – Explore Now!',
                'description' => 'Dreaming of a new car? 🚗 Let us help you find the perfect match. Search and compare! 🔎'
            ],
            [
                'title' => '🔥 Don’t Miss Out – Affordable Car Deals!',
                'description' => 'Looking for a car upgrade? 🚗 Explore affordable options for your next vehicle today! 🌟'
            ],
            [
                'title' => 'Affordable Car Insurance! 🚘',
                'description' => '🦾 Protect your car with insurance starting at ₹1 per day. Don\'t miss out!'
            ],
            [
                'title' => '🏍️ Low-Cost Bike Insurance!',
                'description' => '🛵💨 Get insurance for your bike with rates starting at just ₹1/day. Secure your ride today!'
            ],
            [
                'title' => 'Affordable Bike Insurance is Here! 🏍️',
                'description' => '🏍️💨 Get insurance with the lowest interest rates now.'
            ],
            [
                'title' => '🚘 Insure Your Car Smartly! 💸',
                'description' => '🏎️💨 Get car insurance starting at just ₹1 per day. Limited offer!'
            ],
            [
                'title' => 'Save on Insurance Today! 💰',
                'description' => '💸 Get up to 15% off on car and bike insurance. Limited-time offer!'
            ],
            [
                'title' => '🛡️ Protect Your Ride!',
                'description' => '🏍️💨 Your vehicle insurance is expiring soon. Renew now and stay covered.'
            ],
            [
                'title' => '🚗 Is Your Insurance Active?',
                'description' => 'Check and renew in one tap! 🔥'
            ],
            [
                'title' => 'Know Your Vehicle’s Mileage! 📊',
                'description' => '⏲ Calculate your vehicle’s perfect mileage with our smart tool.'
            ],
            [
                'title' => 'Pending Challans Alert! ⚠️',
                'description' => '🧾 Check and clear your pending challans now to avoid penalties. It’s quick and easy!'
            ],
            [
                'title' => '🚦Check Your Challans Now!',
                'description' => 'Check and pay your challans effortlessly using our app. 🧾'
            ],
            [
                'title' => '📊 Save Fuel, Save Money!',
                'description' => 'Use the mileage tool today!'
            ],
            [
                'title' => '🚦 Stay License-Ready!',
                'description' => '🪪  Track your license status now!'
            ],
            [
                'title' => '⛽ Fuel Up Nearby!',
                'description' => 'Locate petrol pumps instantly!  ⛽'
            ],
            [
                'title' => '💳 Loan Deals You’ll Love!',
                'description' => 'Tap for top offers now!  📄'
            ],
            [
                'title' => '🔍 All RTO Info, One Tap!',
                'description' => 'RC, challans, insurance & more!  🌟'
            ],
            [
                'title' => '🔓 Unlock Your Vehicle Secrets!',
                'description' => 'RC, insurance, and challan info—just a tap away!'
            ],
            [
                'title' => '📜 Got Fines? Check Now!',
                'description' => 'Clear your challans and drive worry-free today!'
            ],
            [
                'title' => '🚘 Know Your Car Inside Out!',
                'description' => 'Access RC, mileage, and more instantly!'
            ],
            [
                'title' => '⛽ Where’s the Closest Pump?',
                'description' => 'Find nearby fuel stations in seconds!'
            ],
            [
                'title' => '📋 Your License, Your Power!',
                'description' => 'Check your driving license status or apply now!'
            ],
            [
                'title' => '💰 Big Savings on Loans!',
                'description' => 'Exclusive vehicle loan offers just for you!'
            ],
            [
                'title' => '⚙️ Ready for a Service?',
                'description' => 'Locate top garages and service centers nearby!'
            ],
            [
                'title' => '🌟 Your Mileage Guru Awaits!',
                'description' => 'Calculate fuel efficiency like a pro!'
            ],
            [
                'title' => '📞 Instant Help for Your Ride!',
                'description' => 'Get emergency contacts for insurance and more!'
            ],
            [
                'title' => '🛡️ Stay Insured, Stay Secure!',
                'description' => 'Check and renew your vehicle insurance now!'
            ],
        ];
    
        // Get a random notification
        // $randomNotification = $notifications[array_rand($notifications)];

        $sentNotifications = Cache::get('sent_notifications', []);
        // Filter out already sent notifications today
        $remainingNotifications = array_filter($notifications, function ($notification) use ($sentNotifications) {
            return !in_array($notification['title'], $sentNotifications);
        });
        // If all notifications were sent, reset the cache for the next day
        if (empty($remainingNotifications)) {
            Cache::forget('sent_notifications');
            $remainingNotifications = $notifications;
            $sentNotifications = [];
        }
    
        $randomNotification = $remainingNotifications[array_rand($remainingNotifications)];
        
        $defaultImage = 'public/img/logo.png';
        $notificationSendData = [
            'notification_title' => $randomNotification['title'],
            'notification_description' => $randomNotification['description'],
            'notification_image' => asset($defaultImage),
            'notification_url' => "",
        ];

        // $schedule->job(new SendDailyNotification($notificationSendData))->everyMinute();
        // $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('11:25');
        //  \Log::info("Notification sent kernel: ".$notificationSendData['notification_title']);

        // Schedule jobs for each time slot // 10:30AM, 2:30PM, 6:30PM, 10:30PM
        $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('10:30');  
        $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('14:30');
        $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('18:30');
        $schedule->job(new SendDailyNotification($notificationSendData))->timezone('Asia/Kolkata')->dailyAt('22:30');
        
        $sentNotifications[] = $randomNotification['title'];
        Cache::put('sent_notifications', $sentNotifications, now()->endOfDay());
    }
    
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
    
}
