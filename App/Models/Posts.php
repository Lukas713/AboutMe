<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 29/01/2019
 * Time: 10:24
 */

namespace App\Models;

use PDO;

class Posts extends \Core\Model
{

    /*selects all record from database

    @return assoc array
    */
    public static function getAll(){
        try {
            $conn = static::connect();

            $stmt = $conn->query("SELECT * from project");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;

        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }

    /*inserts new record into database
        @param assoc array, POST array of input values
        @return void
    */
    public static function addNew($params = []){

        if(!self::doesRecordExist($params['title'])){

            $conn = static::connect();
            $stmt = $conn->prepare("INSERT into project (id, title, branch, link, description) 
                                             VALUES (null, :title, :branch, :link, :description)");
            $stmt->execute(array(
                "title" => $params['title'],
                "branch" => $params['branch'],
                "link" => $params['link'],
                "description" => $params['description']
            ));
            return;
        }
        echo 'error, već postoji';
    }

    /*method that checks if record with same title already exists

    @param string, Record title
    @return bool
    */
    protected static function doesRecordExist($title){

        $conn = static::connect();
        $stmt = $conn->prepare("SELECT id from project where title = :title");
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->rowCount() == 0){
            return false;
        }
        return true;
    }
}