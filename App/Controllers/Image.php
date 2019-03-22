<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 04/02/2019
 * Time: 12:26
 */

namespace App\Controllers;

use \Core\View;
use \App\Models\Images;
use \App\Flash;
use \App\Paginator;

/* class for logged users only */
class Image extends Authenticated
{

    /**
     * renders index page for images
     *
     * @return void
     */
    public function index(){
        $this->paginator = $this->chechAndSetPagintor();

        if(!isset($this->routeParams['id']) || intval($this->routeParams['id']) < 1){

            $this->redirect("/image/index/1");

        }else if(intval($this->routeParams['id']) > $this->paginator->getPageNumber()){

            $this->redirect("/image/index/" . $this->paginator->getPageNumber());
        }
        $offset = $this->checkRouteId();
        $records = Images::getAll($offset);
        View::render('Image/index.html', [
            "images" => $records,
            "pages" => $this->paginator->getPageNumber(),
            "current" => $this->routeParams['id']
        ]);
    }


    protected function checkRouteId(){
        $offset = $this->paginator->getOffset($this->routeParams['id']);
        return $offset;
    }

    protected function chechAndSetPagintor(){
        if($this->paginator == null || $this->paginator->getModelClassName() != 'App\Models\Images'){
            $model = new Images();
            $this->paginator = new Paginator($model);
        }
        return $this->paginator;
    }

    /**
     * method that requires login user
     * if user submits form for deleting image, Model method is invoked
     * renders view with proper message
     *
     * @return void
     */
    public function delete(){
        if(!isset($_POST['submit'])){
            $this->redirect('/image/index');
        }
        $image_m = new Images($_POST);
        if(!$image_m->delete()){
            Flash::addMessage("Something went wrong, please try again", Flash::INFO);
            View::render('Image/index.html');
            return;
        }
        //remove file from directory
        Flash::addMessage("Successfully deleted");
        $this->index();
    }
}