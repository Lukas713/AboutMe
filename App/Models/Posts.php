<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 29/01/2019
 * Time: 10:24
 */

namespace App\Models;

use mysql_xdevapi\Exception;
use App\Config;
use PDO;

/**
 * Post model that operates on database
 */
class Posts extends \Core\Model
{
    /**
     * @var that holds error texts if any
     */
    public $errors = [];

    /**
     * init properties with input values from $_POST
     */
    public function __construct($params)
    {
        foreach($params as $key => $value){
            $this->$key = $value;
        }
    }

    /**
     * fetch all projects from database
     * @return array
     */
    public static function getAll($offset = 0){
        try {
            $conn = static::connect();
            $stmt = $conn->prepare("SELECT * from project LIMIT :limit OFFSET :offset");
            $stmt->bindValue("limit", Config::LIMIT_PAGES, PDO::PARAM_INT);
            $stmt->bindValue("offset", $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * inserts record into database
     * @return bool
     */
    public function insert(){
        try {
            $this->validate();
            if(!empty($this->errors)){
                return false;
            }
            if(!$this->projectExist($this->title)){  //invoke method that checks if record exists
                $conn = static::connect();
                $stmt = $conn->prepare("INSERT into project (id, title, branch, link, description) 
                                                VALUES (null, :title, :branch, :link, :description)");
                $stmt->bindValue("title", $this->title, PDO::PARAM_STR);
                $stmt->bindValue("branch", $this->branch, PDO::PARAM_STR);
                $stmt->bindValue("link", $this->link, PDO::PARAM_STR);
                $stmt->bindValue("description", $this->description, PDO::PARAM_STR);
                return $stmt->execute();
            }
            return false;
        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * update record inside database
     * @return bool
     */
    public function update(){
        try {
            $this->validate();
            if(!empty($this->errors)){
                return false;
            }
            $conn = static::connect();
            $stmt = $conn->prepare("UPDATE project SET title = :title, branch = :branch, link = :link, 
                                            description = :description where id = :id");
            $stmt->bindValue("title", $this->title, PDO::PARAM_STR);
            $stmt->bindValue("branch", $this->branch, PDO::PARAM_STR);
            $stmt->bindValue("link", $this->link, PDO::PARAM_STR);
            $stmt->bindValue("description", $this->description, PDO::PARAM_STR);
            $stmt->bindValue("id", $this->id, PDO::PARAM_STR);
            return $stmt->execute();
        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * get the project with id from parameter
     * @return array
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

    /**
     * checks if project with title as argument exists
     * @return bool
     */
    protected function projectExist($title){
        $conn = static::connect();

        $stmt = $conn->prepare("SELECT id from project where title = :title");
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount() == 0){ //record does not exists
            return false;
        }
        return true;
    }

    /**
     * validates inputs from form for adding new project
     * @return void
     */
    protected function validate(){

        if($this->title == ''){
            $this->errors[] = 'Title can\'t be empty string';
        }

        if($this->branch == ''){
            $this->errors[] = 'Branch can\'t be empty string';
        }

        if(filter_var($this->link, FILTER_VALIDATE_URL) === false){
            $this->errors[] = 'URL is not valid';
        }

        if(strlen($this->description) > 800){
            $this->errors[] = 'Description must have maximum 800 characters';
        }
    }

    public static function countPosts(){
        $conn = static::connect();
        $stmt = $conn->prepare("SELECT COUNT(*) as number FROM project");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}