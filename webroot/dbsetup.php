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

$success = true;

echo "Creating user db table:<br/>&nbsp;&nbsp;";
$u =$user->setup();
if($u[0]) {
	echo "DONE<br />";
}else {
	$success =false;
	echo "FAIL<br />";
}

echo "Adding admin account (usr: admin, pwd: admin):<br />&nbsp;&nbsp;";
if($u[1]){
	echo "DONE<br />";
}else {
	$success =false;
	echo "FAIL<br />";
}

echo "Adding user account (usr: doe, pwd: doe):<br />&nbsp;&nbsp;";
if($u[2]){
	echo "DONE<br />";
}else {
	$success =false;
	echo "FAIL<br />";
}
echo "Creating comment db table:<br />&nbsp;&nbsp;";
if($comment->setup()) {
	echo "DONE<br />";
}else {
	$success =false;
	echo "FAIL<br />";
}
if($success){
	echo "Setup finished<br />";
}else {
	echo "Setup failed. Check db config settings<br />";
}

