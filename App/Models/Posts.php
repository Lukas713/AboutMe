<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 29/01/2019
 * Time: 10:24
 */

namespace App\Models;

use mysql_xdevapi\Exception;
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
        try {
            //check input
            if(!self::recordExist($params['title'])){

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
        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }

    /*change records inside database

    @param assoc array, $_POST array with input fields
    @return void
    */
    public static function edit($params = []){
        $conn = static::connect();

        $stmt = $conn->prepare("UPDATE project SET title = :title, branch = :branch, link = :link, 
                            description = :description where id = :id");
        $stmt->execute(array(
            "title" => $params['title'],
            "branch" => $params['branch'],
            "link" => $params['link'],
            "description" => $params['description'],
            "id" => $params['id']
        ));
    }

    /*load project information

    @param int, project ID
    @return void
    */
    public static function load($id){
        try{
            $conn = static::connect();

            $stmt = $conn->prepare("SELECT * FROM project where id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;

        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }


    /*method that checks if record with same title already exists

    @param string, Record title
    @return bool
    */
    protected static function recordExist($title){
        $conn = static::connect();

        $stmt = $conn->prepare("SELECT id from project where title = :title");
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->rowCount() == 0){ //record does not exists
            return false;
        }
        return true;
    }
}