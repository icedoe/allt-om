

/*
$di->set('FormController', function () use ($di) {
    $controller = new \Anax\HTMLForm\FormSmallController();
    $controller->setDI($di);
    return $controller;
});

// Test form route
$app->router->add('', function () use ($app) {
   $app->session();
   $app->dispatcher->forward([
        'controller' => 'form']);
});
*/


// Render the page
$app->theme->render();