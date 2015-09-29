<?php
//include the database config
include 'database.php';
// register the gwaAutoload function as the autoloading handler
spl_autoload_register('gwaAutoload');
// autoload function - find file for given class & include it
function gwaAutoload($classname)
{
    // controllers
    if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
        include __DIR__.'/controllers/'.$classname.'.php';

        return true;
    // models
    } elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
        include __DIR__.'/models/'.$classname.'.php';

        return true;
    // handlers (views)
    } elseif (preg_match('/[a-zA-Z]+Handler$/', $classname)) {
        include __DIR__.'/handlers/'.$classname.'.php';

        return true;
    // misc files - lib directory
    } else {
        include __DIR__.'/lib/'.str_replace('_', DIRECTORY_SEPARATOR, $classname).'.php';

        return true;
    }
    // couldn't autoload class
    return false;
}
// create a request object
$request = new Request();

// route the request

// first get the controller name - this is the first 'directory' of the HTTP Path
$controller_name = ucfirst($request->url_elements[1]).'Controller';
// if we can autoload the class
if (class_exists($controller_name)) {
    // create a controller object
    $controller = new $controller_name();
    // construct the function name - eg getAction
    $action_name = strtolower($request->verb).'Action';
    // run the desired function & store result in $result
    $result = $controller->$action_name($request);
    // this is where $format comes in
    // send $result to the required handler
    $handler_name = ucfirst($request->format).'Handler';
    // if we can autoload the class
    if (class_exists($handler_name)) {
        // create a handler object
        $handler = new $handler_name();
        // render the result
        // the handler should echo it's output within the render function
        $handler->render($result);
    }
}
