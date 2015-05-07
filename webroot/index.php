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

		$content = $app->fileContent->get('me.md');
		$content = $app->textFilter->doFilter($content, 'shortcode, markdown');
		$byline =$app->fileContent->get('byline.md');
		$byline =$app->textFilter->doFilter($byline, 'markdown');

		$app->views->add('me/page', [
			'content' => $content,
			'byline' => $byline,
		],
		'main');

		$app->dispatcher->forward([
			'controller' => 'sidebar',
			'action' => 'index',
			'params' => array($_POST),
			]);
	});

	$app->router->add('redovisning', function() use ($app){
		$app->theme->setTitle("Redovisning");

		$content =$app->fileContent->get('redovisning.md');
		$content =$app->textFilter->doFilter($content, 'markdown');
		$byline =$app->fileContent->get('byline.md');
		$byline =$app->textFilter->doFilter($byline, 'markdown');

		$app->views->add('me/page', [
			'content' => $content,
			'byline' => $byline,
		]);
//		$doShow =$app->request->getPost('doShow') ? true : false;
//		$doShow =$app->request->getPost('doHide') ? false : $doShow;

		$id =$app->request->getPost('commentId');

		$app->dispatcher->forward([
       		'controller' => 'comment',
        	'action'     => 'control',
        	'params'	=> ['redovisning', 'reports']
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