<h1>Web app about my projects</h1><hr>
<h3>Used technologies</h3>
<ul>
    <li>PHP</li>
    <li>MySql</li>
    <li>Twig as template engine</li>
    <li>HTML</li>
    <li>PhpStorm as IDE</li>
</ul>
<hr>
<h4>Description</h4>
<p>Simple Model, View, Controller architeture that
allows me to add new project into database, change's it
or removes it. Users can register on page, and login ofcourse.</p>
<hr>
<h4>Specifications</h4>
<pre>
d-App
    d-Controllers
    d-Models
    d-Views
    l-Config
d-Core
    l-Controller
    l-Model
    l-Router
    l-View
    l-Error
d-logs
d-public
    l-.htaccess
    l-index
    l-script
d-vendor
    d-composer
</pre>

Front controller adds routes that are converted into regular expression
and every route as Query String is compared with those regular expression inside Dispatcher
class in match method. 
<pre>
`$router->add('{controller}/{action}');`
`$url = $_SERVER['QUERY_STRING'];`
`$router->dispatch($url);`

//inside dispatch()
if(!$this->match($url)){
     throw new \Exception("No route: $url found", 404);
}
</pre>

If there is available class as route's first part,
invoke that class and if there is souch method as second part of URL
invoke that method inside class too.
<pre>
//still inside dispatch()
if(!class_exists($controller)){
    throw new \Exception("Class $controller does not exists!");
}
$controllerObject = new $controller($this->params);
$action = $this->params['action'];

//invoke controllers method inside class
$controllerObject->$action($this->params);
</pre>
Where specific controller calls model if necessary and renders view
that is requested.
<pre>
//where user is requesting for index page
`public function index(){
    View::render('Home/index.html');
}`
</pre>