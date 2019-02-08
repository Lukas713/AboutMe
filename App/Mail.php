<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 08/02/2019
 * Time: 14:17
 */

namespace App;

use Mailgun\Mailgun;

class Mail
{
    /**
     * Send message with php MailGun
     *
     * @param string, $to is Recipient
     * @param string, $subject is title of message
     * @param string, $text is content of message
     * @param string, HTML content of the message
     *
     * @return mixed
     */
    public static function send($to, $subject, $text, $html){
        // First, instantiate the SDK with your API credentials
        $mg = Mailgun::create('7950b39419372ec9fdd6c74df9e95755-b9c15f4c-58dd1717');
        //// Now, compose and send your message.
        // $mg->messages()->send($domain, $params);
        $mg->messages()->send("sandbox8790d90252fa4807b73db1b0c8f4a46f.mailgun.org", [
            'from'    => 'lukas.scharmitzer@gmail.com',
            'to'      => $to,
            'subject' => $subject,
            'text'    => $text,
            'html'    => $html
        ]);
    }
}