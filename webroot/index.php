<?php
	require __DIR__.'/config_full.php';

	$app->theme->configure(ANAX_APP_PATH.'config/theme_grid.php');
	$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);
	$app->navbar->configure(ANAX_APP_PATH.'config/navbar.php');

	$grid= $app->request->getGet('showgrid') ? 'showgrid' : '';
	$app->theme->setVariable('grid', $grid);
/*	$di->set('ThemeController', function() use ($di) {
		$controller = $app->url->setScript('theme.php');
		$controller->setDI($di);
		return $controller;
	});
*/


	$app->router->add('', function() use ($app){
		$app->theme->setTitle("Allt om...");

		$app->dispatcher->forward([
			'controller' => 'users',
			'action' => 'start',
			'params' => array($_POST, ''),
			]);
	});

	

	$app->router->add('source', function() use ($app){
		$app->theme->addStylesheet('css/source.css');
		$app->theme->setTitle("Källkod");

		$source = new \Mos\Source\CSource([
			'secure_dir' => '..',
			'base_dir' => '..',
			'add_ignnore' => ['.htaccess'],
		]);

		$app->views->add('me/source', [
			'content' => $source->View(),
			]);
	});


	$app->router->handle();
	$app->theme->render();