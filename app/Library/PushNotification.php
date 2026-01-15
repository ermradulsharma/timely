<?php

namespace App\Library;

use App\Models\Setting;

class PushNotification
{
    protected static $serverKey = NULL;

    public function __construct() {}

    /**
     * Send push notification using FCM.
     *
     * @param array $notificationData
     * @return mixed
     */
    public static function send(array $notificationData = [])
    {
        $serverKey = config('services.fcm.server_key');

        $settingObj = Setting::where('name', 'push_notification_server_key')->first();

        if ($settingObj) {
            $settings = $settingObj->settings;

            if (isset($settings['push_notification_server_key'])) {
                $serverKey = $settings['push_notification_server_key'];
            }
        }

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = [
            "registration_ids" => [
                $notificationData['device_token']
            ],
            "notification" => [
                "title" => $notificationData['title'] ?? "",
                "body" => $notificationData['message'] ?? "",
                "data1" => $notificationData['data'] ?? "",
                "sendby" => $notificationData['send_by'] ?? "",
                "type" => $notificationData['type'] ?? "",
                "content_available" => 1,
                "badge" => $notificationData['badge'] ?? 1,
                "sound" => "default",
            ],
            "data" => [
                "title" => $notificationData['title'] ?? "",
                "body" => $notificationData['message'] ?? "",
                "data1" => $notificationData['data'] ?? "",
                "sendby" => $notificationData['send_by'] ?? "",
                "type" => $notificationData['type'] ?? "",
                "content_available" => 1,
                "badge" => $notificationData['badge'] ?? 1,
                "sound" => "default",
            ],
            "priority" => 10
        ];

        if (isset($notificationData['metadata']) && !empty($notificationData['metadata'])) {
            $fields['notification']['metadata'] = $notificationData['metadata'];
            $fields['data']['metadata'] = $notificationData['metadata'];
        }

        $fields = json_encode($fields);
        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
