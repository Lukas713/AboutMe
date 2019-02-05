<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 04/02/2019
 * Time: 16:57
 */

namespace App\Models;

use PDO;

class Images extends \Core\Model
{
    public $errors = [];

    /*
     * takes assoc array as parameter and creates properties
     *
     * @param assoc array
     * @return void
     */
    public function __construct($file)
    {
        foreach($file as $key => $value){
            $this->$key = $value;
        }
    }

    /*
     * insert image into database
     * @param string, title
     * @return void
     */
    public function insert($title){
        try {
            $this->validateTitle($title);   //validate title input

            if(!empty($this->errors)){
                return false;
            }
            $userID = explode('-', $_SESSION['userID']);
            $file = BP . 'images/' . trim($userID[1]) . "/" . $title . ".jpg";  //take file path

            if(!file_exists(BP . 'images/' . trim($userID[1]))){
                mkdir(BP . 'images/' . trim($userID[1]));
            }
            $conn = static::connect();
            $stmt = $conn->prepare("INSERT into images (id, path, title, user)
                                            VALUES (null, :path, :title, :user)");
            $stmt->bindValue('path', $file, PDO::PARAM_STR);
            $stmt->bindValue('title', $title, PDO::PARAM_STR);
            $stmt->bindValue('user', $userID[0], PDO::PARAM_INT);
            $stmt->execute();

            $lastId = $conn->lastInsertId();
            return $this->getLastRecord($lastId);

        }catch (\PDOException $e){
            echo $e->getMessage();
        }
    }

    /*
     * returns record with last inserted id
     *
     * @param int last inserted id
     * @return array
     */
    protected function getLastRecord($id){
        $conn = static::connect();
        $stmt = $conn->prepare("SELECT * FROM images where id=:id");
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /*
     * validate input title
     *
     * @return void*/
    protected function validateTitle($title){
        /* check if title is empty string */
        if($title == ''){
            $this->errors[] = 'Title can\'t be empty';
        }
        /* check if is string */
        if(filter_var($title, FILTER_SANITIZE_STRING) === false){
            $this->errors[] = 'Title is not valid';
        }
        /* check length of title */
        if(strlen($title) > 20){
            $this->errors[] = 'Title mut have maximum 20 characters';
        }
    }
}