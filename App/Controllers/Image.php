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
        $this->requireLogin();

        View::render('Image/index.html');
    }

    /**
     * stops if form is submitted, if image is not sent and if there is errors
     * sends array to method that validates image format
     * creates object from model and invokes method that inserts record inside database
     * moves file from temp into new location
     *
     * @return void
     */
    public function insert(){
        if(!isset($_POST['submit'])){
            $this->redirect('image/index');
        }
        if(!isset($_FILES['image']) || $_FILES['image']['error'] > 0){
            Flash::addMessage("Something went wrong, please try again", Flash::INFO);
            View::render('Image/index.html');
            return;
        }
        if(!$this->validateImage($_FILES)){
            View::render('Image/index.html');
            return;
        }
        $file_m = new Images($_FILES['image']); //create models object
        $record = $file_m->insert($_POST['title']); //invoke model method that inserts file in database
        move_uploaded_file($_FILES['image']['tmp_name'], $record['path']); //move from temp into images directory
        Flash::addMessage("Successful upload");
        View::render('Image/index.html');
        return;
    }

    /**
     * checks format of the image
     * @param array
     * @return bool
     */
    protected function validateImage($file){

        if(!exif_imagetype($file['image']['tmp_name'])){
            Flash::addMessage("Image format is wrong", Flash::INFO);
            return false;
        }
        return true;
    }
}