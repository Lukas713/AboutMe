<?php
namespace App\Controllers;
use \Core\View;
use App\Models\Posts;
class Post extends \Core\Controller {
    /* show the index page
        @return void
    */
    public function index(){
        View::render('Post/index.html');
    }
    /*
     * show addNew page
     * @return void
     * */
    public function addNew(){
        echo 'Hello World, I am addNew() action inside Post controller';
    }
    public function edit() {
        echo 'Hello World, I am edit() action inside Post controller and my parameters are: ';
        echo '<hr>';
        print_r($this->routeParams);
        echo '<hr>';
    }
    protected function before()
    {
        echo 'I am invoked before' . '<hr>';
    }
    protected function after()
    {
        echo '<hr>' . 'I am invoked after';
    }
}