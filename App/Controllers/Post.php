<?php
namespace App\Controllers;

use \Core\View;
use App\Models\Posts;

class Post extends \Core\Controller {

    /* show the index page
        @return void
    */
    public function index(){
        $result = Posts::getAll(); //invoke Models action

        View::render('Post/index.html', [
            'posts' => $result
        ]);
    }

    /*add new page
        @return void
    */
    public function add(){
        View::render('Post/add.html');

        if(isset($_POST['submit'])){
            //check input
            Posts::addNew($_POST);
            $_POST = array();

            header("location: /post/index");
        }
    }

    /*loads page for editing record
     *
     * @return void
     * */
    public function edit() {
        $result = Posts::load($this->routeParams['id']);    //invoke load() method from Model

        View::render('Post/edit.html', [
           'infos' => $result
        ]);

        if(isset($_POST['submit'])){
            //check input
            Posts::edit($_POST);
            $_POST = array();

            header("location: /Post/index");
        }

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