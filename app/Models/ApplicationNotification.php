<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class ApplicationNotification extends Model
{
    public static function sendOneSignalNotificationSchedule($notificationData) {
        # old web
        // $appId = "d1880f77-f3ab-4887-bb53-b271ca70f9ab";
        // $apiKey = "Yjk2Njg3NjAtY2YxNS00ZmM5LWE2OWItNDViNjUyYTdhNWMy";

        $appId ='f90c52ea-1e7d-4dc1-97cc-f2fce7643146';
        $apiKey = 'NGUyNjZiZjAtMzFiZS00MWM5LThkOWEtODk3ZThhN2Y1ZjIx';
        $notification_title = $notificationData['notification_title'];
        $notification_message = $notificationData['notification_description'];
        $notification_image = $notificationData['notification_image'];
        // $player_ids = $notificationData['player_ids']; # Array of specific player IDs
        # Chunk the player IDs into smaller batches to send notifications in chunks
        // $chunks = array_chunk($player_ids, 200); # Chunk size of 200 IDs per request

        $client = new Client();
        // foreach ($chunks as $chunk) {
            $response = $client->post("https://onesignal.com/api/v1/notifications", [
                'headers' => [
                    'Authorization' => 'Basic ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'app_id' => $appId,
                    'contents' => ['en' => $notification_message],
                    'headings' => ['en' => $notification_title],
                    'big_picture' => $notification_image,
                    'large_icon' => $notification_image,
                    'chrome_web_image' => $notification_image,
                    // 'include_player_ids' => $player_ids,
                    'included_segments' => ['All'],
                ],
            ]);
        // }
    }

    public static function sendOneSignalNotification($notificationData) {  # to all users

        $appId ='f90c52ea-1e7d-4dc1-97cc-f2fce7643146';
        $apiKey = 'NGUyNjZiZjAtMzFiZS00MWM5LThkOWEtODk3ZThhN2Y1ZjIx';

        $notification_title = $notificationData['notification_title'];
        $notification_url = $notificationData['notification_url'];
        $notification_image = $notificationData['notification_image'];
        $notification_message = $notificationData['notification_description'];

        $client = new Client();
        $response = $client->post("https://onesignal.com/api/v1/notifications", [
            'headers' => [
                'Authorization' => 'Basic ' . $apiKey,
            ],
            'json' => [
                'app_id' => $appId,
                'contents' => ['en' => $notification_message],
                'headings' => ['en' => $notification_title],
                'url' => $notification_url,
                'big_picture' => $notification_image,
                'large_icon' => $notification_image,
                'chrome_web_image' => $notification_image, //'https://picsum.photos/600'
                'included_segments' => ['All'],
            ],
        ]);
        return $response;
    }

}
