<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 28/01/2019
 * Time: 16:07
 */

namespace Core;
/*Exception and error handler*/
class Error
{
    /**
     *  Error handler. Convert all errors to Exceptions by throwing the ErrorException object
     *  @param int $level, Error level
     *  @param string $message, Error message
     *  @param string $file, Filename the error was raised in
     *  @param int $line, Line number in the file
     */
    public static function errorHandler($level, $message, $file, $line){
        if(error_reporting() !== 0){
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }
    /**
     * Exception handler.
     * @param Exception $exception, The exception
     * @return void
     */
    public static function exceptionHandler($exception){
        $code = $exception->getCode();
        if($code != 404){
            $code = 500;
        }
        http_response_code($code);  //Gets or sets the HTTP response status code.
        //production or development mode
        if(\App\Config::SHOW_ERRORS){
            echo '<h1> Fatal error </h1>';
            echo '<p>Uncaught exception: ' . get_class($exception) . '</p>';
            echo '<p>Message: ' . $exception->getMessage() . '</p>';
            echo '<p>Stack trace: <pre>' . $exception->getTraceAsString() . '</pre></p>';
            echo '<p>Thrown in file: ' . $exception->getFile()
                . ' on line: ' . $exception->getLine() . '</p>';
        }else {
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';   //take path to logs
            ini_set('error_log', $log); //sets destination of error_log (change php settings)
            //create message
            $message = 'Uncaught exception ' . get_class($exception);
            $message .= ' with message ' . $exception->getMessage();
            $message .= '\nStack trace: ' . $exception->getTraceAsString();
            $message .= '\Thrown in: ' . $exception->getFile() . ' on line: '
                . $exception->getLine();
            //sends message
            error_log($message);
            if($code != 404){
                View::render('Error/' . $code . '.html');
                return;
            }
            View::render('Error/' . $code . '.html');
        }
    }
}