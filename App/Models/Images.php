<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 04/02/2019
 * Time: 16:57
 */

namespace App\Models;

use \App\Config;
use PDO;

class Images extends \Core\Model
{
    public $errors = [];

    /**
     * takes assoc array as parameter and creates properties
     *
     * @param assoc array
     * @return void
     */
    public function __construct($file = [])
    {
        $this->limitPages = Config::LIMIT_IMAGES;

        foreach($file as $key => $value){
            $this->$key = $value;
        }
    }

    public static function usersImageOnly($offset = 0) {
        $conn = static::connect();
        $email = explode(" - ", $_SESSION['userID']);
        $stmt = $conn->prepare("SELECT a.id, a.path, SUBSTRING_INDEX(a.title, '&', 1) as title, b.email
                                          from images a inner join user b
                                          on a.user = b.id
                                          where b.email = :email
                                          order by id desc
                                          LIMIT :limit
                                          OFFSET :offset");
        $stmt->bindValue("email", $email[1], PDO::PARAM_STR);
        $stmt->bindValue("limit", Config::LIMIT_IMAGES, PDO::PARAM_INT);
        $stmt->bindValue("offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return self::convertPath($result);
    }

    /**
     * get all user images and pass them to controller
     *
     * @return array, associative array with records
     */
    public static function getAll($offset = 0){
        $conn = static::connect();
        $stmt = $conn->prepare("SELECT a.id, a.path, SUBSTRING_INDEX(a.title, '&', 1) as title, a.user, b.email as email 
                                        from images a
                                        inner join user b
                                        on a.user = b.id
                                        ORDER BY id DESC
                                        LIMIT :limit
                                        OFFSET :offset");
        $stmt->bindValue("limit", Config::LIMIT_IMAGES, PDO::PARAM_INT);
        $stmt->bindValue("offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return self::convertPath($result);
    }

    /**
     * validate title input
     * adds random chars to title to differentiate from other titles
     * and returns it
     */
    protected function insertPreworkout($title){
        $this->validateTitle($title);

        if(!empty($this->errors)){
            return false;
        }
        //set random string in image's title
        $title .= '&' . bin2hex(random_bytes(5));

        $userFile = explode('-', $_SESSION['userID']);
        if(!self::isItJpg($this->tmp_name)){
            $file = BP . 'img/' . trim($userFile[1]) . "/" . $title . ".png";  //take file path
        }else {
            $file = BP . 'img/' . trim($userFile[1]) . "/" . $title . ".jpg";  //take file path
        }
        if(!file_exists(BP . 'img/' . trim($userFile[1]))){
            mkdir(BP . 'img/' . trim($userFile[1]));
        }
        $userFile[1] = $file;
        $userFile[2] = $title;
        return $userFile;
    }

    /**
     * insert image into database
     * @param string, title
     * @return mixed, false if there is errors or array if insert is executed correctly
     */
    public function insert($title){
        try {
            $userFile = $this->insertPreworkout($title);
            if(!$userFile){
                return false;
            }
            $conn = static::connect();
            $stmt = $conn->prepare("INSERT into images (id, path, title, user)
                                            VALUES (null, :path, :title, :user)");
            $stmt->bindValue('path', $userFile[1], PDO::PARAM_STR);
            $stmt->bindValue('title', $userFile[2], PDO::PARAM_STR);
            $stmt->bindValue('user', $userFile[0], PDO::PARAM_INT);
            $stmt->execute();
            $lastId = $conn->lastInsertId();
            return $this->getImageWithId($lastId);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * method that deletes image from database with id that is sent by form
     *
     * @return bool
     */
    public function delete(){
        $record = $this->getImageWithId($this->id);
        if(!$record){
            return false;
        }
        $conn = static::connect();
        $stmt = $conn->prepare("DELETE FROM images WHERE id = :id");
        $stmt->bindValue("id", $record['id'], PDO::PARAM_INT);

        /* removes file from server */
        unlink($record['path']);
        return $stmt->execute();
    }

    /**
     * returns record with last inserted id
     *
     * @param int last inserted id
     * @return mixed, false if there is no records, assoc array if there is one
     */
    protected function getImageWithId($id){
        $conn = static::connect();
        $stmt = $conn->prepare("SELECT * FROM images where id=:id");
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
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
        /* title is letters and numbers */
        if(ctype_alnum($title) == false){
            $this->errors[] = 'Title mut contain only letters and numbers';
        }
    }

    /**
     * removes unnecessary part of the path
     *
     * @param array, $records are assciative array of fetched results from database
     * @return array, new array that has only helpful infos
     */
    public static function convertPath($records){
        if(!empty($records)){
            foreach($records as $key => $value){
                $path = explode('public/', $value['path']);
                $records[$key]['path'] = $path[1];
            }
        }
        return $records;
    }

    /**
     * method that checks if file is jpg
     *
     * @param string, nae of the image
     * @return bool
     */
    public static function isItJpg($tmpName){
        if(exif_imagetype($tmpName) != IMAGETYPE_JPEG){
            return false;
        }
        return true;
    }

    public function countPosts($id = null){
        $conn = static::connect();

        if($id != null){
            $stmt = $conn->prepare("SELECT COUNT(*) as number FROM images WHERE user = :user");
            $stmt->bindValue("user", $id, PDO::PARAM_INT);
        }else {
            $stmt = $conn->prepare("SELECT COUNT(*) as number FROM images");
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}