<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 29/01/2019
 * Time: 08:36
 */

namespace Core;

use App\Config;
use PDO;

abstract class Model
{
    private function __construct()
    {

    }
    
    protected static function connect() {

        static $conn = null;

        if($conn == null){

            $dns = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            $conn = new PDO($dns, Config::DB_USER, Config::DB_PASSWORD);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $conn;
    }
}