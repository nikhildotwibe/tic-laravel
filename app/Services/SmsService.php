<?php



namespace App\Services;


class SmsService
{
    public function send(string $to, string $message, $templateID)
    {
        $message = urlencode($message);
        $route = "T";
        $url = env('SMS_GATEWAY_URL') . "?uname=" . env('SMS_GATEWAY_UNAME') . "&pwd=" . env('SMS_GATEWAY_PASSWORD') . "&senderid=" . env('SMS_GATEWAY_SENDER_ID') . "&to=" . $to . "&msg=" . $message . "&route=$route&peid=" . env('SMS_GATEWAY_PE_ID') . "&tempid=" . $templateID;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch,
            CURLOPT_RETURNTRANSFER,
            1
        );
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
