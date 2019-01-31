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

class Users extends \Core\Model
{
    public $errors = [];


    /*constructs model's object
        @param array
    */
    public function __construct($params)
    {
        foreach($params as $key => $value){
            $this->$key = $value;
        }
    }
    /* inserts user data into database
     *  @return bool
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

        //validate if email is taken
        if($this->emailExists($this->email)){
            $this->errors[] = 'Email is already in use';
        }

        //validate password lenght
        if(strlen($this->password) < 6){
            $this->errors[] = 'Password must have at least 6 characters';
        }

        //validate repeated password
        if($this->password != $this->password_repeat){
            $this->errors[] = 'Password and repeated password must match';
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
     * @return bool
    */
    protected function emailExists($email){
        $conn = static::connect();

        $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindValue("email", $email, PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->rowCount() != 0){ //user exists
            return true;
        }
        return false;
    }
}