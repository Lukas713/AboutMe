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
use \App\Auth;
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
        if(!isset($_POST['submit'])){
            $this->redirect('/login');
        }
        $user = Users::authenticate($_POST['email'], $_POST['password']); //user is newly constructed object or false value

        if(!$user){
            View::render('Login/index.html', [
                "email" => $_POST['email'],
                "errors" => 'Wrong email/password'
            ]); //if false value, go back to login page
            return;
        }
        /* set session value */
        Auth::login($user); //create's seassion
        $this->redirect(Auth::getReturnToPage()); //redirects user to requested page or home page
    }

    /*
     * destroy session
     * @return void
     */
    public function destroy(){
        Auth::logout(); //destroys session
        $this->redirect('/login');
    }
}