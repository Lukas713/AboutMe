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
        $token = $token->getToken();

        $conn = static::connect();
        $stmt = $conn->prepare("SELECT * from remember where token = :token");
        $stmt->bindValue("token", $token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
}