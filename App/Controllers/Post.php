<?php
namespace App\Controllers;

use \Core\View;
use App\Models\Posts;

/*
 * Post controller that talk with Post view and Post model
 */
class Post extends \Core\Controller {

    /*
     * renders index page of posts
     * @return void
     */
    public function index(){
        $result = Posts::getAll(); //invoke Models action

        View::render('Post/index.html', [
            'posts' => $result
        ]);
    }

    /*
     * invoke view for adding new record
     * @return void
     */
    public function add(){
        View::render('Post/add.html');
    }

    /*
     * invoke models method for adding new record and redirects user
     * @return void
     */
    public function insert(){
        if(isset($_POST['submit'])){
            $post_m = new Posts($_POST);    //instantiate model object

            if(!$post_m->insert()){ //invoke model's method
                echo '<p style="color:red;">Record with that title already exists</p>';
                return;
            }
            $_POST = array();
            exit(header("location: http://" . $_SERVER['HTTP_HOST'] . '/post/success', true, 303));
        }
        exit(header("location: http://" . $_SERVER['HTTP_HOST'] . '/post/add', true, 303));
    }

    /*
     * loads page for editing record
     * @return void
     */
    public function edit() {
        $result = Posts::load($this->routeParams['id']); //invoke load() method from Model
        View::render('Post/edit.html', [
            'infos' => $result
        ]);
    }

    /*
     * invoke method from model that updates record
     * @return void
     */
    public function update(){
        if(isset($_POST['submit'])){
            $post_m = new Posts($_POST);
            $post_m->update();

            exit(header("location: http://" . $_SERVER['HTTP_HOST'] . '/post/success', true, 303));
        }
        exit(header("location: http://" . $_SERVER['HTTP_HOST'] . '/post/index', true, 303));
    }

    /*
     * renders view for successfully operation on database
     * @return void
     */
    public function success(){
        View::render('Post/success.html');
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