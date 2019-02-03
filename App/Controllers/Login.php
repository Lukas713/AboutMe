<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 02/02/2019
 * Time: 12:31
 */

namespace App\Controllers;

use \Core\View;
use \App\Models\Users;
use http\Client\Curl\User;

/*
 * controller class that talks with Users model and Login view
 */
class Login extends \Core\Controller
{
    /*
     * renders index page of login
     * @return void
     */
    public function index(){
        View::render('Login/index.html');
    }

    /*
     * authorize user and renders login page if false authorisation, home page otherwise
     * @return void
     */
    public function authorize(){
        $user = Users::authenticate($_POST['email'], $_POST['password']); //user is newly constructed object or false value

        if(!$user){
            View::render('Login/index.html', [
                "email" => $_POST['email']
            ]); //if false value, go back to login page
            return;
        }
        /* set session value */
        $_SESSION['userID'] = $user->id . ' - ' . $user->email;
        $this->redirect('/');
    }

    /*
     * destroy session
     * @return void
     */
    public function destroy(){
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
        $this->redirect('/login/index');
    }
}