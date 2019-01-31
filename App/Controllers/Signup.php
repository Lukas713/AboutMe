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

class Signup extends \Core\Controller
{
    /*renders registration page
        @return void
     */
    public function index(){
        View::render('Signup/register.html');
    }

    /*invokes saving in database method from Users model
        @return void
    */
    public function create(){
        if(isset($_POST['submit'])){
            $user_m = new Users($_POST);

            if(!$user_m->save()){
                View::render('Signup/register.html', [
                    "user" => $user_m
                ]);
                return;
            }
            header('location: http://' . $_SERVER['HTTP_HOST'] . '/signup/success', true, 303);
            exit;
        }
    }

    public function success(){
        View::render('Signup/success.html');
    }
}