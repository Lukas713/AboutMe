<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 07/02/2019
 * Time: 15:06
 */

namespace App\Models;

use PDO;
use App\Token;

class RememberedLogin extends \Core\Model
{
    /**
     * creates hashed value from token
     * selects record from database with that token
     * constructs object and returns it
     *
     * @param string, existing token
     * @return object
     */
    public static function findByToken($token){
        $token = new Token($token);
        $token = $token->getHash();

        $conn = static::connect();
        $stmt = $conn->prepare("SELECT * from remember where token = :token");
        $stmt->bindValue("token", $token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * converts string format Y-m-d H:i:s into current Unix timestamp
     * and compares it with "beginning of time"
     *
     * @return bool
     */
    public function hasExpired(){
        return strtotime($this->expires) < time();
    }

    /**
     * delete's cookie from database
     *
     * @return void
     */
    public function delete(){
        $conn = static::connect();
        $stmt = $conn->prepare("DELETE FROM remember where token = :token");
        $stmt->bindValue("token", $this->token, PDO::PARAM_STR);
        $stmt->execute();
    }
}