<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 20/02/2019
 * Time: 10:50
 */

namespace App;

require '../vendor/autoload.php';

/**
 * class that sends email with SendGrid API
 **/
class Mail {
    public static function send($to, $subject, $text){
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("lukas.scharmitzer@gmail.com");
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent(
            "text/html", $text
        );
        $sendgrid = new \SendGrid(Config::SENDGRID_API_KEY);
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }
}