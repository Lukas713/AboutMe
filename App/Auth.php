<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 04/02/2019
 * Time: 12:11
 */

namespace App;

use \App\Models\Users;

/**
 * Class that cares about authenticating users
 */
class Auth
{
    /**
     * create's session
     * @param object
     * @return void
     */
    public static function login($user){
        session_regenerate_id(true); //create new session and delete's new one
        $_SESSION['userID'] = $user->id . ' - ' . $user->email;
    }

    /**
     * destroys session
     * @return void
     */
    public static function logout(){
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        // Finally, destroy the session.
        session_destroy();
    }

    /**
     * set requested URL
     * @return void
     */
    public static function rememberRequestedURI(){
        $_SESSION['returnTo'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * get requested page or home page
     * @return void
     */
    public static function getReturnToPage(){

        if(!isset($_SESSION['returnTo'])){
            return '/';
        }
        return $_SESSION['returnTo'];
    }

    /**
     * finds user by id from session
     *
     * @return user object or null if there is no session
     */
    public static function getUser(){
        if(isset($_SESSION['userID'])){
            $session = explode('-', $_SESSION['userID']);
            return Users::findById($session[0]);
        }
    }

}