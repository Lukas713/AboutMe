<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 25/01/2019
 * Time: 11:38
 */
namespace Core;

use \Core\View;
use \App\Auth;

/*
 * Base controller
 * */
abstract class Controller
{
    /*
     * parameters from matched route
    */
    protected $routeParams = [];
    /*
     * constructor
     *
     * @param array $routeParams, Parameters from the root
     * @return void
    */
    public function __construct($routeParams)
    {
        $this->routeParams = $routeParams;
    }
    /*
     * magic methods is invoked at the end of the dispatcher
     * invoke method before desired action
     * invoke desired action
     * invoke method after desired action
     *
     * @param string & array
     * @return void
     * */
    public function __call($name, $arguments)
    {
        $method = preg_replace('/@Action/', '', $name);

        if(!method_exists($this, $method)){
           throw new \Exception("Method: $method not found in controller: " . get_class($this));
        }
        if($this->before() !== false){
            call_user_func([$this, $method], $arguments);
            $this->after();
            return;
        }
        throw new \Exception("Something went wrong!");
    }

    /*
     * redirects user and exits the script
     *
     * @param string
     */
    public function redirect($url){
        exit(header('location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303));

    }

    /*
     * checks if user is not logged in
     * takes requested URI and redirects to login page
     *
     * @return void
     */
    public function requireLogin(){
        if(!Auth::getUser()){

            Auth::rememberRequestedURI();
            $this->redirect('/login');
        }
    }

    protected function after(){
    }
    protected function before(){
    }
}