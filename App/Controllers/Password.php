<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 20/02/2019
 * Time: 12:02
 */

namespace App\Controllers;

use Core\View;
use App\Models\Users;
use App\Flash;

class Password extends \Core\Controller
{
    /**
     * reset password index page
     * @return void
     */
    public function index(){
        View::render("Password/index.html");
    }

    /**
     * method that is invoked after submiting the password reset form
     * @retun void
     */
    public function requestReset(){
        if(!isset($_POST['submit'])){
            $this->redirect('/');
        }
        Users::resetPassword($_POST['email']);
        Flash::addMessage("Successfully! Please check your email");
        $this->redirect("/");
    }

    public function reset() {
        $token = $this->routeParams['token'];

        $user = Users::findByPasswordToken($token);
        if(!$user){
            Flash::addMessage("Token does not match", Flash::INFO);
            $this->redirect("/"); 
        }
        View::render("Password/resetPassword.html", [
            'user' => $user
        ]);
    }
}