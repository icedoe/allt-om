<?php
	require __DIR__.'/config_with_app.php';

	$grid= $app->request->getGet('showgrid') ? 'showgrid' : '';
	$app->theme->setVariable('grid', $grid);

	$app->theme->configure(ANAX_APP_PATH.'config/theme-grid.php');
	$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);
	$app->navbar->configure(ANAX_APP_PATH.'config/navbar_me.php');

	$app->router->add('', function() use ($app) {
		$app->theme->setTitle('Tema');
		
		$content =$app->fileContent->get('theme/tema.md');
		$content = $app->textFilter->doFilter($content, 'markdown');

		$app->views->add('me/page', [
			'content' => $content]);
	});

	$app->router->add('regioner', function() use ($app) {
 
    $app->theme->addStylesheet('css/anax-grid/regions_test.css');
    $app->theme->setTitle("Regioner");
 
    $app->views->addString('flash', 'flash')
               ->addString('featured-1', 'featured-1')
               ->addString('featured-2', 'featured-2')
               ->addString('featured-3', 'featured-3')
               ->addString('main', 'main')
               ->addString('sidebar', 'sidebar')
               ->addString('triptych-1', 'triptych-1')
               ->addString('triptych-2', 'triptych-2')
               ->addString('triptych-3', 'triptych-3')
               ->addString('footer-col-1', 'footer-col-1')
               ->addString('footer-col-2', 'footer-col-2')
               ->addString('footer-col-3', 'footer-col-3')
               ->addString('footer-col-4', 'footer-col-4');
 
	});

	$app->router->add('typografi', function() use ($app) {
		$app->theme->setTitle("Typografi");

		$app->views->add('theme/typography', [], 'main');
		$app->views->add('theme/typography', [], 'sidebar');
	});

	$app->router->add('font-awesome', function() use ($app) {
		$app->theme->setTitle("Ikontest");

		$app->views->add('theme/icon_main', [], 'main');
		$app->views->add('theme/icon_sidebar', [], 'sidebar');
	});
	


	$app->router->handle();
	$app->theme->render();