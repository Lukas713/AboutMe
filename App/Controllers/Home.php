<?php

namespace App\Controllers;

use \Core\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Home controller that renders front page
 */
class Home extends \Core\Controller {

    /**
     * renders index page
     * @return void
     */
    public function index(){
        View::render('Home/index.html');
    }


    protected function before()
    {
        //i am invoked before index
    }
    protected function after()
    {
       //i am invoked after index
    }

}