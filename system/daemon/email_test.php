<?php


require_once dirname(__FILE__, 2) . "/tools/phpmailer/Exception.php";
require_once dirname(__FILE__, 2) . "/tools/phpmailer/PHPMailer.php";
require_once dirname(__FILE__, 2) . "/tools/phpmailer/SMTP.php";


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$kiw_temp['host'] = 'smtp.mailtrap.io';
$kiw_temp['port'] = '2525';
$kiw_temp['auth'] = 'tls';
$kiw_temp['user'] = 'ecb73b691965e4';
$kiw_temp['password'] = '49610f75776245';
$kiw_temp['from_email'] = 'no-reply@gmail.com';
$kiw_temp['from_name'] = 'adib';

$omy_email = new PHPMailer(false);

$omy_email->Timeout = 10;
$omy_email->SMTPDebug = 0;

$omy_email->Host = trim($kiw_temp['host']);
$omy_email->Port = trim($kiw_temp['port']);


if (!empty($kiw_temp['auth']) && $kiw_temp['auth'] != "none") {

    $omy_email->SMTPSecure = trim($kiw_temp['auth']);

}


if (!empty($kiw_temp['user']) && !empty($kiw_temp['password'])) {

    $omy_email->SMTPAuth = true;

    $omy_email->Username = trim($kiw_temp['user']);
    $omy_email->Password = trim($kiw_temp['password']);

}


$omy_email->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);


try {


    $omy_email->isSMTP();
    $omy_email->setFrom(trim($kiw_temp['from_email']), trim($kiw_temp['from_name']));
    $omy_email->ClearAllRecipients();
    $omy_email->addAddress("adib@gmail.com", "amirul");
    $omy_email->addAddress("adib@gmail.com2", "amirul2");

    $omy_email->addReplyTo(trim($kiw_temp['from_email']));
    $omy_email->isHTML(true);

    $omy_email->Subject = "test";
    $omy_email->Body = "asdasd";

    // send the email to smtp server

    $omy_email->send();

    if ($omy_email->ErrorInfo == "") {

        $kiw_status = "succeed";

    } else $kiw_status = $omy_email->ErrorInfo;


} catch (Exception $e){


    $omy_email->Body = "";
    $omy_email->clearAllRecipients();
    $omy_email->clearAttachments();
    $omy_email->smtpClose();

    $kiw_status = $e->getMessage();


}



var_dump($kiw_status);