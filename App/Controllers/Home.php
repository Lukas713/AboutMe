<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;

/*
 * Home controller that renders front page
 */
class Home extends \Core\Controller {

    /*
     * renders index page
     * @return void
     */
    public function index(){
        View::render('Home/index.html');
    }
    protected function before()
    {
        echo 'I am method that is invoked before' . '<hr>';
    }
    protected function after()
    {
        echo '<hr>' . 'I am method that is invoked after';
    }
}