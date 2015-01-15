<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */

require_once "bootstrap.php";
use Helper\StatusUpdate;
//get all tasks
$myStatus=new StatusUpdate(RallyUserName);

$to = 'xinghuangxu@gmail.com';
$subject = 'Status Update['.date('Ymd').']';
$message = $myStatus->getHtmlReport();
$headers = 'From: xinghuangxu@gmail.com' . "\r\n" .
        'Reply-To: xinghuangxu@gmail.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

$headers  .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo 'Email sent successfully!';
} else {
    die('Failure: Email was not sent!');
}
  


