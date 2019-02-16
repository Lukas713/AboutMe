<?php
namespace App\Controllers;

use App\Flash;
use \Core\View;
use App\Models\Posts;
use \App\Auth;
use \App\Paginator;

/**
 * Post controller that talk with Post view and Post model
 */
class Post extends \Core\Controller {

    /**
     * renders index page of posts
     * @return void
     */
    public function index(){
        if(isset($this->routeParams['id'])){    

            if($this->routeParams['id'] < 1){

                $this->routeParams['id'] = 1;

            }else if($this->routeParams['id'] > Paginator::getPageNumber()){
                $this->routeParams['id'] = Paginator::getPageNumber();
            }

            $offset = Paginator::getOffset($this->routeParams['id']);
        }else {
            $offset = Paginator::getOffset();
        }
        $result = Posts::getAll($offset); //invoke Models action
        $result['current'] = $this->routeParams['id'] ?? 1;
        View::render('Post/index.html', [
            'posts' => $result
        ]);
    }

    /**
     * invoke view for adding new record
     * @return void
     */
    public function add(){
        $this->requireLogin();
        $user = Auth::getUser(); //gets user object by he's email from session
        if($user->email != 'lukas.scharmitzer@gmail.com'){
            Flash::addMessage("You dont have permission to do that action", Flash::WARNING);
            $this->index();
            return;
        }
        View::render('Post/add.html');
    }

    /**
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

    /**
     * loads page for editing record
     * @return void
     */
    public function edit() {
        $this->requireLogin();

        $user = Auth::getUser();    //gets user object by he's email from session
        if($user->email != 'lukas.scharmitzer@gmail.com'){
            Flash::addMessage("You dont have permission to do that", Flash::WARNING);
            $this->index();
            return;
        }
        $result = Posts::load($this->routeParams['id']); //invoke load() method from Model
        View::render('Post/edit.html', [
            'infos' => $result
        ]);
    }

    /**
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

    /**
     * renders view for successfully operation on database
     * @return void
     */
    public function success(){
        View::render('Post/success.html');
    }
}