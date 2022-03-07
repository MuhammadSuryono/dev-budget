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

        $url = "/send-notification-message";
        $body = ["msisdn" => $msisdn, "message" => $message];
        return $httpRequest->HTTPPost($url, $body, "form", $this->host);
    }

    public function sendDocumentMessage($msisdn, $message, $document)
    {
        $httpRequest = new HTTPRequester();

        $url = $this->host."/send-notification-document";
        $body = ["msisdn" => $msisdn, "message" => $message, "document_link" => $document];

        return $httpRequest->HTTPPost($url, $body);
    }
}