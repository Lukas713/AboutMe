<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 04/02/2019
 * Time: 13:37
 */

namespace App\Controllers;

/*
 * Authenticated base controller for classes that requires authentication
 */
abstract class Authenticated extends \Core\Controller
{
    /*
     * invoked before every  inaccessible method
     *
     * @return void
     */
    public function before()
    {
        $this->requireLogin();
    }
}