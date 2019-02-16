<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 07/02/2019
 * Time: 10:06
 */

namespace App;

/**
 * class that holds token value inside cookie
 */
class Token
{
    /**
     * token value
     * @var string
     */
    protected $token;

    /**
     * Class constructor
     * creates NEW random 16 bytes and turns them into ASCII coded
     * or assigns an existing one IF passed in
     *
     */
    public function __construct($tokenValue = null)
    {
        if(!$tokenValue){   //if there is existing value
            $this->token = bin2hex(random_bytes(16)); //16 bytes = 128 bits = 32 hex characters
            return;
        }
        $this->token = $tokenValue;
    }

    /**
     * returns Token value
     *
     * @return string
     */
    public function getToken(){
        return $this->token;
    }

    /**
     * create hash value on token using sha256 algorithm and secret key
     *
     * @return string
     */
    public function getHash(){
        return hash_hmac('sha256', $this->token, Config::SECRET_KEY);
    }
}