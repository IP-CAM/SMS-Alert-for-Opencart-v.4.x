<?php

namespace Opencart\Admin\Model\Extension\SmsAlert\Service;

/**
 * Class ModelExtensionSmsAlertHelper
 */
class Helper extends \Opencart\System\Engine\Model
{

    public CONST API_URL = 'https://smsalert.mobi/api/v2/message/send';

    public function sendTestSMS($to, $body): bool
    {
        $apiKey   = $this->model_setting_setting->getValue('smsalert_apiKey');
        $username = $this->model_setting_setting->getValue('smsalert_username');

        if (!empty($apiKey)) {
            try {
                return $this->request($username, $apiKey, $to, $body);
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    protected function request($username, $apiKey, $phoneNumber, $message)
    {
        // Create a new cURL resource
        $ch = curl_init(self::API_URL);

        // Setup request to send json via POST
        $data = [
            'phoneNumber' => $phoneNumber,
            'message'     => $message
        ];

        $payload = json_encode($data);
        $token   = base64_encode($username . ':' .$apiKey);
        $headers = ['Authorization: Basic ' . $token, 'Content-Type:application/json'];

        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the POST request
        curl_exec($ch);

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            if ($info['http_code'] != 200) {

                // Close cURL resource
                curl_close($ch);
                return false;
            }
        }

        // Close cURL resource
        curl_close($ch);

        return true;
    }
}