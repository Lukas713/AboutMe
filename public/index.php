<?php
define('BP', __DIR__ . '/');

/* require composer's autoloader */
require '../vendor/autoload.php';

/* Exception/Error handling */
error_reporting(E_ALL); //Sets which PHP errors are reported
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/* sessions */
session_start();

/* routing */
$router = new Core\Router();

/* defining possible route structure */
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('post', ['controller' => 'Posts', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{action}/{id:\d+}');
$router->add('signup', ['controller' => 'signup', 'action' => 'index']);
$router->add('login', ['controller' => 'login', 'action' => 'index']);
$router->add('logout', ['controller' => 'login', 'action' => 'destroy']);

/* catching the entered URL */
$url = $_SERVER['QUERY_STRING'];
/* convert URL into route and follow it */
$router->dispatch($url);