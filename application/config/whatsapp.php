<?php

require_once "httpRequest.php";

class Whastapp {
    private $host = "http://192.168.10.240:8080/api/v1/whatsapp";

    public function __construct()
    {
        
    }

    public function sendMessage($msisdn, $message)
    {
        $httpRequest = new HTTPRequester();

        $url = $this->host."/send-notification-message";
        $body = ["msisdn" => $msisdn, "message" => $message];
        var_dump($httpRequest->HTTPPost($url, $body));
        exit();
//        return $httpRequest->HTTPPost($url, $body);
    }

    public function sendDocumentMessage($msisdn, $message, $document)
    {
        $httpRequest = new HTTPRequester();

        $url = $this->host."/send-notification-document";
        $body = ["msisdn" => $msisdn, "message" => $message, "document_link" => $document];

        return $httpRequest->HTTPPost($url, $body);
    }
}