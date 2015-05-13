<?php
namespace Deg\Tags;

class TagsController implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectionAware;

	private $comments;

	public function initialize()
	{
		$this->comments = new \Deg\Comment\CommentsInDatabase();
		$this->comments->setDI($this->di);
	}

	public function indexAction()
	{
		$tags = $this->comments->getColVals('tags');

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

	public function startAction($post, $calling)
	{
		$tags =$this->comments->mostPopularTags(5);

		$urls =[];
		foreach($tags as $tag){
			$urls[] =$this->di->url->create('comment/tag/'.$tag);
		}

		$this->di->views->add('tags/tags', [
			'tags' => $tags,
			'urls' => $urls],
			'flash'
		);
		$this->di->dispatcher->forward([
			'controller' => 'comment',
			'action' => 'start',
			'params' => [$post, $calling]
			]);
	}
}