<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 09/02/2019
 * Time: 11:47
 */

namespace App\Controllers;

use App\Models\Users;
use \Core\View;
use \App\Models\Images;
use \App\Flash;
use \App;

class Profile extends Authenticated
{
    /**
     * renders clients profile page
     *
     * @return void
     */
    public function index(){
        View::render("Profile/index.html");
    }

    /**
     * checks id fro url, gets records and renders view
     *
     * @return void
     */
    public function album(){
        $this->paginator = $this->chechAndSetModel('App\Models\Images');
        $email = explode(" - ", $_SESSION['userID']);

        if(!isset($this->routeParams['id']) || intval($this->routeParams['id']) < 1){
            $this->redirect("/profile/album/" . 1);

        }else if(intval($this->routeParams['id']) > $this->paginator->getPageNumber($email[0])){

            $this->redirect("/profile/album/" . $this->paginator->getPageNumber($email[0]));
        }
        $offset = $this->paginator->getOffset($this->routeParams['id']);
        $records = Images::usersImageOnly($offset);
        View::render("Profile/album.html", [
            "images" => $records,
            "pages" => $this->paginator->getPageNumber($email[0]),
            "current" => $this->routeParams['id']
        ]);
    }



    protected function chechAndSetModel($argument = null){
        if($this->paginator == null || $this->paginator->getModelClassName() != "App\Paginator"){
            $model = new $argument;
            $this->paginator = new App\Paginator($model);
        }
        return $this->paginator;
    }

    /**
     * stops if form is submitted, if image is not sent and if there is errors.
     * sends array to method that validates image format.
     * creates object from model and invokes method that inserts record inside database.
     * moves file from temp into new location.
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
        $record = $file_m->insert($_POST['title']);
        if(!$record){ //invoke model method that inserts file in database
            View::render('Image/index.html', [
                "records" => $file_m
            ]);
            return;
        }
        if(!$this->compress($_FILES['image']['tmp_name'], $record['path'])){
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
    protected function compress($source, $destination){
        $info = getimagesize($source);  //get infos about image (height, width...)
        //Create a new image from file or URL
        if($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg'){
            $image = imagecreatefromjpeg($source);
            $suffix = 'jpeg';

        }else if($info['mime'] == 'image/png' || $info['mime'] == 'image/PNG'){
            $image = imagecreatefrompng($source);
            $suffix = 'png';

        }else if($info['mime'] == 'image/gif'){
            $image = imagecreatefromgif($source);
            $suffix = 'jpeg';
        }
        if(!$image){
            Flash::addMessage("Something went wrong, please try again", Flash::INFO);
            return false;
        }
        $this->createThumbnail($image, $suffix, $destination);
        return true;
    }

    /**
     * creates Thumbnail using GD
     * @param string, $Image is image source
     * @param string, $suffix is image extension
     * @param string, $destination is a location where file will be located
     *
     * @return void
     */
    protected function createThumbnail($image, $suffix, $destination){
        $width = imagesx($image);   //get width of image
        $height = imagesy($image);  //get height of image
        $desiredHeight = floor($height * (400 / $width));
        /* create a new, "virtual" image */
        $virtualImage = imagecreatetruecolor(400, $desiredHeight);
        /* copy source image at a resized size */
        imagecopyresampled($virtualImage, $image, 0, 0, 0, 0, 400, $desiredHeight, $width, $height);

        if($suffix == 'png' || $suffix == 'PNG'){
            imagepng($virtualImage, $destination);
        }else {
            imagejpeg($virtualImage, $destination);
        }
    }

    /**
     * gets user's ide from session,
     * creates model object and invoke update method
     * @return void
     */
    public function update(){
       if(isset($_POST['update'])){
           $userID = explode(" - ", $_SESSION['userID']);
           $_POST['id'] = $userID[0];
           $user_m = new Users($_POST);

            if(isset($_FILES['file'])){
                $_POST['image'] = $_FILES['file'];
                $image_m = new Images($_POST['image']);
                $image = $image_m->insert($_POST['profileTitle']);
                if(!$image || !$this->compress($_POST['image']['tmp_name'], $image['path'])){
                    echo 'profileTitle';
                    return;
                }
                $imagePath = explode("img/", $image['path']);
                $user_m->setProfileInDatabase($imagePath[1]);
            }
            $user_m->update();
            echo 'good job';
        }
    }

    public function remove(){

        if(!isset($_POST['id'])){
            $this->redirect("/profile/album");
        }
        $image_m = new Images($_POST);
        $image_m->delete();

        echo "good job";
    }
}