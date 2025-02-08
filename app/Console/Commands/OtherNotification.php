<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApplicationNotification;
use App\Models\app_notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use DB;

class OtherNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'other:notification';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('H:i');

        $notification_data = app_notification::where(['type'=>'other','notification_type'=>'1'])->pluck('notification_data');
        // $notification_data = DB::connection('mysql3')->table('app_notifications')->where(['type' => 'other', 'notification_type' => '1'])->pluck('notification_data');
        foreach ($notification_data as $notification) {

            $json_notification_data = json_decode($notification);
            $notification_time = $json_notification_data->notification_time;
            if($notification_time == $currentTime) {

                $notificationImage = 'public/' . $json_notification_data->notification_image;
                $defaultImage = 'public/assets/img/logo.png';
                $notificationSendData['notification_title'] = $json_notification_data->notification_title;
                $notificationSendData['notification_url'] = $json_notification_data->notification_url;
                $notificationSendData['notification_description'] = $json_notification_data->notification_description;
                $notificationSendData['notification_image'] =  Storage::exists($notificationImage) ? asset($notificationImage) : asset($defaultImage);
                $send_notification = ApplicationNotification::sendOneSignalNotificationSchedule($notificationSendData);
            }

            // \Log::info("Cron: other notification");
        }
        
         Cache::flush(); 
             
        # remove laravel log file             
        $logFile = storage_path('logs/laravel.log');
        // Check if the log file exists
        if (File::exists($logFile)) {
            // Clear the log file
            File::put($logFile, '');
            // $this->info('Log file cleared successfully.');
        } 
    }
}
