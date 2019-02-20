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
     * method that is invoked after submit the form
     * for password reset
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
}