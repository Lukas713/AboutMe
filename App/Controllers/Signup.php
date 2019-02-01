<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 31/01/2019
 * Time: 09:52
 */

namespace App\Controllers;

use App\Models\Users;
use Core\View;

/*
 * Sign up controller that talks with User model and Signup view
 */
class Signup extends \Core\Controller
{
    /*
     * renders registration page
     * @return void
     */
    public function registration(){
        View::render('Signup/registration.html');
    }

    /*
     * invokes saving in database method from Users model
     * @return void
     */
    public function create(){
        if(isset($_POST['submit'])){
            $user_m = new Users($_POST);    //init object from User model

            if(!$user_m->save()){
                View::render('Signup/registration.html', [
                    "user" => $user_m   //render view with errors as PUBLIC properties
                ]);
                return;
            }
            exit(header('location: http://' . $_SERVER['HTTP_HOST'] . '/signup/success', true, 303));
        }
        exit(header('location: http://' . $_SERVER['HTTP_HOST'] . '/signup/registration', true, 303));
    }

    /*
     * renders page after successfully registration
     * @return void
     */
    public function success(){
        View::render('Signup/success.html');
    }
}