<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 28/01/2019
 * Time: 15:47
 */

namespace Core;


class View
{
    /*
    @param string template, The template file
    @param array $arguments, Assoc array of data to display in the view (optional)
    @return void
    */
    public static function render($template, $arguments = []){
        static $twig = null;    //maintain its value between function calls
        if($twig === null){
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views'); //loads template from the file system
            $twig = new \Twig_Environment($loader); //call template with default configuration
        }
        //loading template with some variables
        echo $twig->render($template, $arguments);
    }
}