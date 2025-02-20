<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ApplicationNotification;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SendArticleNotifications extends Command
{
    protected $signature = 'notify:articles';
    protected $description = 'Send article notifications to users';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle()
    {
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('H:i');

        $articles = DB::connection('mysql2')
            ->table('articles')
            ->select('title', 'image_url', 'today_date')
            ->orderBy('today_date', 'desc')
            ->take(3)
            ->get();

        if ($articles->isEmpty()) {
            return;
        }
        if ($currentTime >= '00:00' && $currentTime < '09:00') {
            $article = $articles[0]; // First article (9 AM)
        } elseif ($currentTime >= '18:00' && $currentTime < '21:00') {
            $article = $articles[1]; // Second article (9 PM)
        } else {
            $article = $articles[2]; // Default case
        }
        if (!is_null($article)) {
            $imageUrl = empty($article->image_url) ? asset('img/No-Image-Placeholder.svg') : 'https://apps.videoapps.club/common_news/' . $article->image_url;

            $notificationSendData = [
                'notification_title' => $article->title,
                'notification_description' => $article->title,
                'notification_image' => $imageUrl,
            ];
            $send_notification = ApplicationNotification::sendOneSignalNotificationSchedule($notificationSendData);
        }
    }

    /*public function handle()  # ONE SIGNAL NOTIFICATION
    {
        $response = Http::get('https://apps.videoapps.club/common_news/new_list_articles.php');
        $articles = $response->json();

        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('H:i');
        if ($currentTime >= '00:00' && $currentTime < '09:00') {
            $article = $articles['articles'][0];   # 9 AM
        } elseif ($currentTime >= '18:00' && $currentTime < '21:00') {
             $article = $articles['articles'][1];   # 9 PM
        }else{
             $article = $articles['articles'][2];
        }

        if (!is_null($article)) {

            $imageUrl = ($article['image_url'] == null) ? asset('img/No-Image-Placeholder.svg') : asset($article['image_url']);
            $notificationSendData['notification_title'] =$article['title'];
            $notificationSendData['notification_description'] = $article['title'];
            $notificationSendData['notification_image'] = $imageUrl;

            $send_notification = ApplicationNotification::sendOneSignalNotificationSchedule($notificationSendData);
            // \Log::info("Cron: article notification sent");
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

    }*/

}
