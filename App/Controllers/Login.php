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
        $this->redirect('/');
    }
}