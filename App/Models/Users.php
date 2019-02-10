<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 31/01/2019
 * Time: 09:53
 */

namespace App\Models;

use mysql_xdevapi\Exception;
use \App\Token;
use \APP\Flash;
use \App\Controllers\Image;
use PDO;

/**
 * User model that talks with the database
 */
class Users extends \Core\Model
{
    /**
     * @var array that holds errors (if any)
     */
    public $errors = [];

    /**
     * init objects properties
     */
    public function __construct($params = [])
    {
        foreach($params as $key => $value){
            $this->$key = $value;
        }
    }

    /**
     * inserts user data into database
     * @return bool
     */
    public function save(){

        $this->validate();
        if(!empty($this->errors)){
            return false;
        }
        $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

        $file = $this->email . "/" . 'profile' . ".jpg";
        $conn = static::connect();
        $stmt = $conn->prepare("INSERT into user (id, firstname, lastname, email, password, profile) VALUES (null, :firstname, :lastname, :email, :password, :profile)");
        $stmt->bindValue("firstname", $this->fname, PDO::PARAM_STR);
        $stmt->bindValue("lastname", $this->lname, PDO::PARAM_STR);
        $stmt->bindValue("email", $this->email, PDO::PARAM_STR);
        $stmt->bindValue("password", $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue("profile", $file, PDO::PARAM_STR);

        if(!$stmt->execute()){
            return false;
        }
        $this->id = $conn->lastInsertId();
        return true;
    }

    /**
     * validate inputs
     * @return void
     */
    protected function validate(){
        //validate firstname
        if(filter_var($this->fname, FILTER_SANITIZE_STRING) === false){
            $this->errors[] = 'Invalid first name';
        }

        if(filter_var($this->lname, FILTER_SANITIZE_STRING) === false){
            $this->errors[] = 'Invalid last name';
        }

        //validate email
        if(filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
            $this->errors[] = 'Invalid email';
        }

        //validate if email is already taken
        if($this->emailExists($this->email)){
            $this->errors[] = 'Email is already in use';
        }

        //validate password lenght
        if(strlen($this->password) < 6){
            $this->errors[] = 'Password must have at least 6 characters';
        }

        //validate password: letters
        if(preg_match('/.*[a-z]+.*/i', $this->password) == 0){
            $this->errors[] = 'Passwords must contains at least one letter';
        }

        //validate passwor: numbers
        if(preg_match('/.*\d+.*/i', $this->password) == 0){
            $this->errors[] = 'Passwords must contains at least one number';
        }
    }

    /**
     * check if email is already in use
     * @param email from post
     * @return bool Returns true if record exists with specific email, false otherwise
     */
    public static function emailExists($email){
        return self::findByEmail($email) !== false;
    }

    /**
     * finds record with specific email and return is, false if not
     * @param string Email from post
     *
     * @return array, record exists with entered email
     * @return false, record does not exists with entered email
     */
    public static function findByEmail($email){
        $conn = static::connect();

        $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindValue("email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());  //fetch as constructed object from class that this method is called

        return $stmt->fetch();  //return object, if there is no such record
    }
    /**
     * finds record with specific id and return is, false if not
     * @param string id
     *
     * @return array, record exists with entered id
     * @return false, record does not exists with entered id
     */
    public static function findById($id){
        $conn = static::connect();

        $stmt = $conn->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());  //fetch as constructed object

        return $stmt->fetch();  //return object, if there is no such record
    }

    /**
     * checks if user exists and if entered password is valid
     *
     * @param string, entered Email
     * @param string, entered Password
     *
     * @return bool, record with email/password does not exists or password is not valid
     * @return object, constructs Users object if there is a record with entered email/password
     */
    public static function authenticate($email, $password){
        $user = self::findByEmail($email);

        if(!$user || !password_verify($password, $user->password)){
            return false;
        }
        return $user;
    }

    /**
     * creates hashed "remember me" token and date when it will expire.
     * Inserts hashed token inside database
     *
     * @return bool
     */
    public function rememberMe(){
        $rememberMe = new Token();
        //set as properties
        $this->hashedToken = $rememberMe->getHash(); //hashed token for database insert
        $this->token = $rememberMe->getToken(); //token
        $this->expiryDate = time() + 60 * 60 * 24 * 10; //10 days from now

        $conn = static::connect();
        $stmt = $conn->prepare("INSERT INTO remember(id, token, expires, user)
                                        VALUES (null, :token, :expires, :user)");
        $stmt->bindValue("token", $this->hashedToken, PDO::PARAM_STR);
        $stmt->bindValue("expires", date('Y-m-d H:i:s', $this->expiryDate), PDO::PARAM_STR);
        $stmt->bindValue("user", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * validates if image format is wrong
     * inserts record in database
     * moves file from temp position
     *
     * @return bool
     */
    public function validateImageAndInsert($fileArray){
        if(!exif_imagetype($fileArray['image']['tmp_name'])){
            Flash::addMessage("Image format is wrong", Flash::INFO);
            return false;
        }
        $file = BP . 'img/' . $this->email . "/" . 'profile' . ".jpg";  //take file path
        mkdir(BP . 'img/' . $this->email);

        $conn = static::connect();
        $stmt = $conn->prepare('INSERT into images (id, path, title, user)
                                            VALUES (null, :path, :title, :user)');
        $stmt->bindValue('path', $file, PDO::PARAM_STR);
        $stmt->bindValue('title', 'profile', PDO::PARAM_STR);
        $stmt->bindValue('user', $this->id, PDO::PARAM_INT);

        move_uploaded_file($_FILES["image"]["tmp_name"], BP . 'img/' . $this->email . '/profile.jpg');
        return $stmt->execute();
    }


}