<?php

/**
 * @author Leonx
 * @copyright 2015 NetApp, Inc.
 * @version 1.0.0
 */
class EmailTest extends PHPUnit_Framework_TestCase {

    public function testSendEmail() {
        $to = 'xinghuangxu@gmail.com';
        $subject = 'Fake sendmail test';
        $message = 'If we can read this, it means that our fake Sendmail setup works!';
        $headers = 'From: xinghuangxu@gmail.com' . "\r\n" .
                'Reply-To: xinghuangxu@gmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            echo 'Email sent successfully!';
        } else {
            die('Failure: Email was not sent!');
        }
    }

}
