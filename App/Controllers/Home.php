<?php

namespace App\Controllers;
use \Core\View;

/**
 * Home controller that renders front page
 */
class Home extends \Core\Controller {

    /**
     * renders index page
     * @return void
     */
    public function index(){
        \App\Mail::send("lukas.scharmitzer@gmail.com", "First email", "This is first email", '<h1>This is a test</h1>');

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