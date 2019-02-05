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
        $this->before();

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
        $this->before();

        if(!isset($_POST['submit'])){
            $this->redirect('/image/index');
        }
        $image_m = new Images($_POST);
        if(!$image_m->delete()){
            Flash::addMessage("Something went wrong, please try again", Flash::INFO);
            View::render('Image/index.html');
            return;
        }
        Flash::addMessage("Successfully deleted");
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

        if(!$this->compress($_FILES['image']['tmp_name'], $record['path'], 90)){
            $this->redirect('/image/index');
        }
        Flash::addMessage("Successful upload");
        $this->redirect('/image/index');
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

    /**
     * takes image infos, creates new image on searched destination
     * and sets quality of the image to 90/100
     *
     * @param string, $source as path to image (temp folder on sevrer)
     * @param string, $destination as path to new place where image will be placed
     * @param int, $quality as quality of image (third parameter of imagejpeg, 0/100)
     *
     * @return bool, true if image is successfully created, false otherwise
     */
    protected function compress($source, $destination, $quality){
        $info = getimagesize($source);  //get infos about image (height, width...)
        //Create a new image from file or URL
        if($info['mime'] == 'image/jpeg'){
            $image = imagecreatefromjpeg($source);

        }else if($info['mime'] == 'image/png'){
            $image = imagecreatefrompng($source);

        }else if($info['mime'] == 'image/gif'){
            $image = imagecreatefromgif($source);
        }
        if(!$image){
            Flash::addMessage("Something went wrong, please try again", Flash::INFO);
            return false;
        }
        imagejpeg($image, $destination, $quality); //Output image to browser or file
        return true;
    }
}