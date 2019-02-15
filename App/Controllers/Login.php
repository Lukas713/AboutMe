<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 02/02/2019
 * Time: 12:31
 */

namespace App\Controllers;

use App\Flash;
use \Core\View;
use \App\Models\Users;
use \App\Auth;
use http\Client\Curl\User;

/**
 * controller class that talks with Users model and Login view
 */
class Login extends \Core\Controller
{
    /**
     * renders index page of login
     * @return void
     */
    public function index(){
        View::render('Login/index.html');
    }

    /**
     * authorize user and renders login page if false authorisation, home page otherwise
     * @return void
     */
    public function authorize(){
        if(!isset($_POST['submit'])){
            $this->redirect('/login');
        }
        $rememberMe = isset($_POST['rememberMe']);  //user chose remember me or not
        $user = Users::authenticate($_POST['email'], $_POST['password']); //user is newly constructed object or false value
        if(!$user){
            Flash::addMessage('Login was unsuccessful, please try again', Flash::WARNING);
            View::render('Login/index.html', [
                "email" => $_POST['email'],
                "rememberMe" => $rememberMe
            ]); //if false value, go back to login page
            return;
        }
        /* set session value */
        Auth::login($user, $rememberMe); //create's session
        Flash::addMessage('Welcome ' . $user->email);   //add flash message
        $this->redirect(Auth::getReturnToPage()); //redirects user to requested page or home page
    }

    /**
     * destroy session and redirects to method that shows logout message
     *
     * @return void
     */
    public function destroy(){
        Auth::logout(); //destroys session
        $this->redirect('/login/getLogoutMessage');
    }

    /**
     * creates message with new session after destroying last one
     * redirects to login page with message
     *
     * @return void
     */
    public function getLogoutMessage(){
        Flash::addMessage("Bye, You have been successfully logged out", Flash::SUCCESS);
        $this->redirect('/login');
    }
}