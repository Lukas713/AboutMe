<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 09/02/2019
 * Time: 11:47
 */

namespace App\Controllers;

use Core\View;

class Profile extends Authenticated
{
    public function index(){
        View::render("Profile/index.html");
    }
}