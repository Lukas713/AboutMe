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

/* class for logged users only */
class Image extends Authenticated
{

    /*
     * renders index page for images
     *
     * @return void
     */
    public function index(){
        $this->requireLogin();

        View::render('Image/index.html');
    }

    public function insert(){
        View::render('Image/index.html');

        if(isset($_POST['submit'])){
            print_r($_FILES);
            echo '<hr>'; 
            print_r($_POST);
        }
    }
}