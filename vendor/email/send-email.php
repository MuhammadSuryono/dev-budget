<?php
require_once 'application/config/httpRequest.php';
require_once 'application/config/helper.php';


function sendEmail($msg = '', $subject = '', $email, $attachment = '', $address = "single")
{
    $host = "http://localhost:8081/api/v1/email";

    $httpRequest = new HTTPRequester();
    $url = $host."/send-notification-message";

    if ($address == "multiple") {
        $email = implode(",", $email);
    }

    $body = ["recipients" => $email, "subject" => $subject, "body" => $msg];

    if ($attachment != '') {
        $helper = new Helper();
        $path = $helper . "document/$attachment.pdf";
        array_push($body, ["attachment" => ["filename" => $attachment.".pdf", "url" => $path]]);
    }

    return $httpRequest->HTTPPost($url, $body, "json");


    // $mail = new PHPMailer(true);

    // $mail->IsSMTP(); // enable SMTP
    // $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    // //authentication SMTP enabled
    // $mail->SMTPAuth = true;
    // $mail->SMTPSecure = false; // secure transfer enabled REQUIRED for Gmail

    // $mail->Host = "192.168.8.3";
    // $mail->Port = 25;
    // $mail->Username = "admin.web@mri-research-ind.com";
    // $mail->Password = "w3bminMRI";
    // $mail->SetFrom("admin.web@mri-research-ind.com", "MRINet WebAdmin");
    // // $mail->AddReplyTo("xxx@xxx.com", "Name Replay");
    // $mail->Subject = $subject;
    // $mail->MsgHTML($msg);

    // if ($address == "multiple") {
    //     for ($i = 0; $i < count($email); $i++) {
    //         if ($email[$i]) $mail->AddAddress($email[$i]);
    //     }
    // } else {
    //     $mail->AddAddress($email);
    // }

    // $url = explode('/', __DIR__);
    // if ($attachment) {
    //     $path = "/" . $url[1] . "/" . $url[2] . "/" . $url[3] . "/" . $url[4] . "/document/$attachment.pdf";
    //     $mail->addAttachment($path);
    // }

    // if (!$mail->Send()) {
    //     return "Mailer Error: " . $mail->ErrorInfo;
    // } else {
    //     return "Email telah terikirim kepada $email";
    // }
}
