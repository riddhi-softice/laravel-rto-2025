<?php

namespace App\Jobs;

use App\Models\ApplicationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendDailyNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificationData;
   
    public function __construct(array $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    public function handle()
    {
        // Log::info("Notification sent....");
        
        try {
            // Send the notification via OneSignal or another service
            ApplicationNotification::sendOneSignalNotification($this->notificationData);
            // Log::info("Notification sent: " . $this->notificationData['notification_title']);

        } catch (\Exception $e) {
            Log::error("Error in SendDailyNotification job: " . $e->getMessage());
            // Optionally, rethrow the exception if you want the job to be retried:
        }
    }
}
