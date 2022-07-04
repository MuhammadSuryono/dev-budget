<?php

require_once "httpRequest.php";

class Email {
    private $host = "http://180.211.92.131/api/v1/helper";

    public function __construct()
    {
        
    }

    public function sendEmail($msg = '', $subject = '', $email, $attachment = '', $address = "single")
    {
        $httpRequest = new HTTPRequester();

        $url = "/email/send-notification-message";

        if ($address == "multiple") {
            $email = implode(",", $email);
        }

        $body = ["recipients" => $email, "subject" => $subject, "body" => $msg];

        if ($attachment != '') {
            $helper = new Helper();
            $path = $helper . "document/$attachment.pdf";
            array_push($body, ["attachment" => ["filename" => $attachment.".pdf", "url" => $path]]);
        }
        $res = $httpRequest->HTTPPost($url, $body, 'form', $this->host);
        //Something to write to txt log
        // $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
        // "Attempt: ".(json_encode($res)).PHP_EOL.
        // "Body: ".(json_encode($body)).PHP_EOL.
        // "User: LOG".PHP_EOL.
        // "-------------------------".PHP_EOL;
        // //Save string to log, use FILE_APPEND to append.
        // file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);

        return $res; 
    }



}