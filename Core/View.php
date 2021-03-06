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
    /**
     * creates Twig envirnomet object with template and its arguments
     * adds globals that serves to display features for logged user only
     * and to display messages if any
     *
     * @param string template, The template file
     * @param array $arguments, Assoc array of data to display in the view (optional)
     * @return void
     */
    public static function render($template, $arguments = []){
        echo self::getRenderTemplate($template, $arguments);
    }

    /**
     * get the view template using Twig
     * @param string template, The template file
     * @param array, Assoc array of data to display in the view (optional)
     * @return string
     */
    public static function getRenderTemplate($template, $arguments = []){
        static $twig = null;    //maintain its value between function calls
        if($twig === null){
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views'); //loads template from the file system
            $twig = new \Twig_Environment($loader); //call template with default configuration
            $twig->addGlobal('currentUser', \App\Auth::getUser());
            $twig->addGlobal('messages', \App\Flash::getMessage());
        }
        //loading template with some variables
        return $twig->render($template, $arguments);
    }
}