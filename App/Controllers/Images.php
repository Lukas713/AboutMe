<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 04/02/2019
 * Time: 12:26
 */

namespace App\Controllers;

use \Core\View;
use App\Flash;

/* class for logged users only */
class Images extends Authenticated
{

    /*
     * renders index page for images
     *
     * @return void
     */
    public function index(){
        $this->requireLogin();

        View::render('Shelf/index.html');

    }
}