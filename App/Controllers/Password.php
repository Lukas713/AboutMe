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
     * takes token from objects property and finds user record
     * by that password token.
     * Renders VIEW for reset password
     * @return void
     */
    public function reset() {
        $user = $this->getUserOrExit($this->routeParams['token']);

        View::render("Password/resetPassword.html", [
            'token' => $this->routeParams['token'],
            'user' => $user
        ]);
    }

    /**
     * method that is invoked after submiting the password reset form
     * @retun void
     */
    public function requestReset(){
        if(!isset($_POST['submit'])){
            $this->redirect('/');
        }
        if(!Users::startResetPassword($_POST['email'])){
            Flash::addMessage("Something went wrong, please try again", Flash::WARNING);
            $this->redirect("/");
        }
        Flash::addMessage("Successfully! Please check your email");
        $this->redirect("/");
    }

    /**
     * when client submits the form "from" email
     * method checks if client exists
     * invokes method for updating the password
     * @retun void
     */
    public function resetPassword() {
        if(!isset($_POST['submit'])){
            Flash::addMessage("Not this time", Flash::WARNING);
            $this->redirect("/");
        }
        $user = $this->getUserOrExit($_POST['token']);
        if(!$user->resetPassword($_POST['password'])){
            //render resetPassword again with same info
            View::render("Password/resetPassword.html", [
                'token' => $_POST['token'],
                'user' => $user
            ]);
            return;
        }
        Flash::addMessage("Successfully changed password");
        $this->redirect("/");
    }
     /**
      * checks if client exists in database with token
      * @param string
      * @return mixed, user Object or exits if there is no user
      */
    public function getUserOrExit($token){
        $user = Users::findByPasswordToken($token);
        if(!$user){
            Flash::addMessage("Token does not match, please try again", Flash::INFO);
            $this->redirect("/");
        }
        return $user;
    }
}