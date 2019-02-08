<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 04/02/2019
 * Time: 12:11
 */

namespace App;

use App\Models\RememberedLogin;
use \App\Models\Users;

/**
 * Class that cares about authenticating users
 */
class Auth
{
    /**
     * create's session
     *
     * @param object, Users object returned by findByEmail()
     * @param bool, rememberMe value from Login index form (checked remember me or not)
     * @return void
     */
    public static function login($user, $rememberMe){
        session_regenerate_id(true); //create new session and delete's new one
        $_SESSION['userID'] = $user->id . ' - ' . $user->email;

        if(!$rememberMe || !$user->rememberMe()){
            return;
        }
        setcookie('rememberMe', $user->token, $user->expiryDate, '/');
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
     * @return string
     */
    public static function getReturnToPage(){
        return $_SESSION['returnTo'] ?? '/';
    }

    /**
     * finds user by from session and returns object with he's records as properties
     *
     * @return mixed, User object or null if there is no session
     */
    public static function getUser(){
        if(isset($_SESSION['userID'])){
            //if user is logged in
            $session = explode('-', $_SESSION['userID']);
            return Users::findById($session[0]);
            //try with remember cookie
        }else {
            return static::loginFromRememberMedCookie();
        }
    }
    /**
     * invoked when server checks if user is logged OR there is a cookie session
     * takes value from cookie and
     *
     */
    protected static function loginFromRememberMedCookie(){
        $cookie = $_COOKIE['rememberMe'] ?? false;  //cookie value string or false value

        if(!$cookie){
            return null;
        }
        $cookieObject = RememberedLogin::findByToken($cookie);  //fetch cookie record from DB

        if(!$cookieObject){
            return null;
        }
        $user = Users::findById($cookieObject->user);
        static::login($user, false);    //RECREATE session id
        return $user;
    }

}