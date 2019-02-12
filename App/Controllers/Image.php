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

/* class for logged users only */
class Image extends Authenticated
{

    /**
     * renders index page for images
     *
     * @return void
     */
    public function index(){
        $records = Images::getAll();
        View::render('Image/index.html', [
            "images" => $records
        ]);
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