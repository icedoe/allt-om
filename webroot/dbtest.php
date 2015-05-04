<?php 
/**
 * This is a Anax frontcontroller.
 *
 */

// Get environment & autoloader.
require __DIR__.'/config_full.php';

// Create services and inject into the app. 


 
// Check for matching routes and dispatch to controller/handler of route
$app->router->handle();