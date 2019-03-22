<?php

namespace App\Controllers;

use \Core\View;
use \App\Mail;

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

    public function returner() {
        echo $this->routeParams['id'];
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