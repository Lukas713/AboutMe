<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 04/02/2019
 * Time: 15:53
 */

namespace App;

/* class that cares about flash messages */
class Flash
{
    /**
     * success message
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * info message
     * @var string
     */
    const INFO = 'info';

    /**
     * warning message
     * @var string
     */
    const WARNING = 'warning';


    /**
     * add a flash message into the $_SESSION super global
     *
     * @param string, message text
     * @param string, type of message
     *
     * @return void
     */
    public static function addMessage($message, $type = self::SUCCESS){
        /* if there is no flash message key */
       if(!isset($_SESSION['flashMessage'])){
           $_SESSION['flashMessage'] = [];
       }
       $_SESSION['flashMessage'][] = [
           'body' => $message,
           'type' => $type
       ];
    }

    /**
     * gets message from $_SESSION super global array
     * @return string
     */
    public static function getMessage(){
        if(isset($_SESSION['flashMessage'])){
            //set, unset, return
            $message = $_SESSION['flashMessage'];
            unset($_SESSION['flashMessage']);
            return $message;
        }
    }
}