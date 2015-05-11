<?php
namespace Deg\Tags;

class TagsController implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectionAware;

	public function indexAction()
	{
		$comments = new \Deg\Comment\CommentsInDatabase();
		$comments->setDI($this->di);
		$tags = $comments->getColVals('tags');

		$urls =[];
		foreach($tags as $tag){
			$urls[] =$this->di->url->create('comment/tag/'.$tag);
		}

		$this->di->theme->setTitle('Taggar');

		$this->di->views->add('tags/tags', [
			'title' => 'Taggar',
			'tags' => $tags,
			'urls' => $urls,
			]);
		$this->di->dispatcher->forward([
            'controller' => 'sidebar',
            'action' => 'index',
            'params' => array($_POST, 'tags'),
            ]);
	}
}