<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 28/01/2019
 * Time: 14:36
 */

namespace Core;

/*  Dispatcher class */
class Router
{
    /**
     * @assoc array that holds routes
     */
    protected $routes = [];

    /**
     * @assoc array that holds params
     */
    protected $params = [];

    /** function that adds route into routing table using regular expression
     *   @param string, Route URL
     *   @param array, Parameters
     *   @return void
     */
    public function add($route, $params = []){

        /*escape forward slash*/
        /* {controller/action} into {controller\/action}*/
        $route = preg_replace('/\//', '\\/', $route);

        /*convert variables*/
        /* {controller\/action} into (?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)  */
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        /*convert variables with custom regular expression*/
        /* {id:\d} into (?P<id>\d) */
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        /*add starting and ending delimeters and option for case insensitive*/
        /* (?P<controller>[a-z-]+)\/(?P<action>[a-z-]+) into ^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$ */
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /** parse URL and recognize controller, action part
     *  @param string, URL
     *  @return bool
     */
    public function match($url){
        /* for all routes */
        foreach($this->routes as $route => $param){
            /* if URL is the same structure as route's key*/
            if(!preg_match($route, $url, $matches)){
                continue;
            }
            /* check if he's match is a string*/
            foreach($matches as $key => $match){
                if(!is_string($key)){
                    continue;
                }
                /*insert into pa*/
                $param[$key] = $match;
            }
            $this->params = $param;
            return true;
        }
        return false;
    }

    /** follow the routes
     *
     * @param string, URL
     */
    public function dispatch($url){

        $url = $this->removeQueryStringVariable($url);

        if(!$this->match($url)){
           throw new \Exception("No route: $url found", 404);
        }

        //if user knows our logic for invoking action filters
        if(preg_match('/action$/i', $this->params['action']) != 0){
            throw new \Exception("Action $this->params['action'] cant be called directly from URL!");
        }

        $controller = $this->params['controller'];
        $controller = $this->convertToStudyCase($controller);
        $controller = $this->getNamespace() . $controller;
        if(!class_exists($controller)){
            throw new \Exception("Class $controller does not exists!");
        }

        $controllerObject = new $controller($this->params);
        $action = $this->params['action'];
        $action = $this->convertToCamelCase($action);

        $controllerObject->$action($this->params);
    }

    /*get routes array*/
    public function getRoutes(){
        return $this->routes;
    }

    /*get routes array*/
    public function getParams(){
        return $this->params;
    }

    /**
     * replace '-' with ' '
     * set each first letter into upper letter
     * replace ' ' with ''
     * @param string
     * @return string
     *
     * e.x. new-employee into NewEmployee
     */
    protected function convertToStudyCase($string){
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    /**
     * convert string into study case
     * convert just first letter into lower letter
     * @param string
     * @return string
     *
     * e.x. add-new into addNew
     */
    protected function convertToCamelCase($string){
        return lcfirst($this->convertToStudyCase($string));
    }
    /**
     * removes query string variables from URL
     * but still can access then in $_GET global variable
     *
     * mvc.com/post/index?page=1  ----->  post/index?page=1  -----> post/index
     *
     * @param string
     * @return string
     */
    protected function removeQueryStringVariable($url){
        if($url != ''){
            $parts = explode('&', $url, 2); //split url, first half is before & and second is after
            //if first half contains equality sign, URL is  e.x. empty mvc.com?page=2 = ''
            if(strpos($parts[0], '=') === false){
                $url = $parts[0];
            }else {
                $url = '';
            }
        }
        return $url;
    }

    /**
     * get the namespace for the controller class
     * namespace defined in the route parameters is added if presented
     * @return string  request URL
     */
    protected function getNamespace(){
        $namespace = '\App\Controllers\\';
        if(array_key_exists('namespace', $this->params)){
            $namespace = $namespace . $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
}