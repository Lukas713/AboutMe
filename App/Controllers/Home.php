<?php
namespace App\Controllers;
use \Core\View;
class Home extends \Core\Controller {
    /* show index page inside controller
    @return void
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