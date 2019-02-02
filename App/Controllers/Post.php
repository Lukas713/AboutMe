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
                View::render('Post/add.html', [
                    "admin" => $post_m
                ]);
                return;
            }
            $_POST = array();
            $this->redirect('/post/success');
        }
        $this->redirect('/post/add');
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

            if(!$post_m->update()){
                $this->redirect('/post/edit/');
            }
            $this->redirect('/post/success');
        }
        $this->redirect('/post/index');
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