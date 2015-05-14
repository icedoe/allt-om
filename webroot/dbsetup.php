<?php 
/**
 * This is a Anax pagecontroller.
 *
 */
// Include the essential settings.
require __DIR__.'/config.php'; 


// Create services and inject into the app. 
$di  = new \Anax\DI\CDIFactory();

$comment = new \Deg\Comment\CommentController();
$comment->setDI($di);

$user = new \Anax\Users\UsersController();
$user->setDI($di);

$app = new \Anax\Kernel\CAnax($di);



$user->setup();
$comment->setup();

