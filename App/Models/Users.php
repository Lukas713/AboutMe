<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 31/01/2019
 * Time: 09:53
 */

namespace App\Models;

use mysql_xdevapi\Exception;
use PDO;

/*
 * User model that talks with the database
 */
class Users extends \Core\Model
{
    /*
     * @var array that holds errors (if any)
     */
    public $errors = [];

    /*
     * init objects properties
     */
    public function __construct($params = [])
    {
        foreach($params as $key => $value){
            $this->$key = $value;
        }
    }

    /*
     * inserts user data into database
     * @return bool
     */
    public function save(){

        $this->validate();
        if(!empty($this->errors)){
            return false;
        }
        $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

        $conn = static::connect();
        $stmt = $conn->prepare("INSERT into user (id, email, password) VALUES (null, :email, :password)");
        $stmt->bindValue("email", $this->email, PDO::PARAM_STR);
        $stmt->bindValue("password", $passwordHash, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /*
     * validate inputs
     * @return void
     */
    protected function validate(){
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

    /*
     * check if email is already in use
     * @param email from post
     * @return bool Returns true if record exists with specific email, false otherwise
     */
    public static function emailExists($email){
        return self::findByEmail($email) !== false;
    }

    /*
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
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());  //fetch as constructed object

        return $stmt->fetch();  //return object, if there is no such record
    }
    /*
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

    /*
     * checks if user exists and if entered password is valid
     *
     * @param string, entered Email
     * @param string, entered Password
     *
     * @return object, construct Users object if there is a record with entered email/password
     * @return bool, record with emil/password does not exists or password is not valid
     */
    public static function authenticate($email, $password){
        $user = self::findByEmail($email);

        if(!$user || !password_verify($password, $user->password)){
            return false;
        }
        return $user;
    }
}