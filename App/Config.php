<?php

namespace App;

/**
 * class that holds constants for connecting to database
 */
class Config {

    const DB_HOST = 'localhost';

    const DB_USER = 'lukas';

    const DB_PASSWORD = '123456789';

    const DB_NAME = 'aboutLukas';

    const SHOW_ERRORS = true;

    /**
     * secret key for hashing
     * @var string
     */
    const SECRET_KEY = '05MUIT9C7ys15WIuMrzmdyjBTNpCt3V5';

    /**
     * MailGun API key
     * @var string
     */
    const MAILGUN_API_KEY = 'pubkey-212f648f19ccd7378d816441a6a08b65';

    /**
     * MailGun domain
     * @var string
     */
    const MAILGUN_DOMAIN = 'sandbox8790d90252fa4807b73db1b0c8f4a46f.mailgun.org';
}