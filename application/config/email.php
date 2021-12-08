<?php

require_once "httpRequest.php";

class Email {
    private $host = "http://192.168.8.2:8081/api/v1/email";

    public function __construct()
    {
        
    }

    public function sendEmail($msg = '', $subject = '', $email, $attachment = '', $address = "single")
    {
        $httpRequest = new HTTPRequester();

        $url = $this->host."/send-notification-message";

        if ($address == "multiple") {
            $email = implode(",", $email);
        }

        $body = ["recipients" => $email, "subject" => $subject, "body" => $msg];

        if ($attachment != '') {
            $helper = new Helper();
            $path = $helper . "document/$attachment.pdf";
            array_push($body, ["attachment" => ["filename" => $attachment.".pdf", "url" => $path]]);
        }

        return $httpRequest->HTTPPost($url, $body);
    }


}