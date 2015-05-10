<?php
namespace Anax\Sidebar;

class SidebarController implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectionAware, \Anax\MVC\TRedirectHelpers;

	private $user;
	private $calling;
	private $modifier;

	public function initialize()
	{
		//$this->setDI($this->di);
		$url =$this->di->request->getCurrentUrl();
		$this->di->session->set('lastPage', $url);
	}

	public function indexAction($post=[], $calling=null, $modifier=null)
	{
		$this->calling =$calling;
		$this->modifier =$modifier;

		if(isset($post['doLogin'])){
			$name =$post['username'];

			$password =$post['password'];

			$name = $this->di->request->getPost('username');
			$password = $this->di->request->getPost('password');

			$this->di->users->login($name, $password);

		}
		$this->user = $this->di->session->get('user') ? $this->di->session->get('user') : [];

		if(isset($this->user['type'])){
			switch($this->user['type']){
				case 'user':
					$this->userAction();
					break;
				case 'admin':
					$this->adminAction();
					break;
			}
		} else {
			$form = $this->getLoginForm();
			$this->di->views->add('nav/sidebar', [
				'form' => $form,
			//	'menu' => $this->getDefaultMenu(),
				],
				'sidebar'
			);
		}
	}

	public function userAction()
	{
		$this->di->views->add('nav/sidebar', [
			'user' => $this->user,
			'menu' => $this->getUserMenu(),
			],
			'sidebar'
		);
	}

	public function adminAction()
	{
		$this->di->views->add('nav/sidebar', [
			'user' => $this->user,
			'menu' => $this->getAdminMenu(),
			],
			'sidebar'
		);
	}

	public function getLoginForm()
	{
		$url =$this->di->url->create('users/add');
		$html =
			"<form method='post'>
				<input type='text' name='username' label='Akronym' required='true' />
				<input type='password' name='password' label='Lösenord' required='true' />
				<input type='submit' name='doLogin' />
				<a href='".$url."'>Eller skapa konto</a>
			</form>";
		return $html;
	}

	private function getUserMenu()
	{
		$array =[];
		
		//Question
		$array['Ställ fråga'] = $this->di->url->create('comment/edit');
		//profile
		if($this->calling == 'users' && $this->modifier == $this->user['id']) {
			$array['Redigera profil'] = $this->di->url->create('users/update');
			$array['Avregistera'] = $this->di->url->create('users/soft-delete/'.$this->user['id']);
		} else {
			$array['Min profil'] =$this->di->url->create('users/id');
		}
		
		//tracked subjects
		$array['Bevakning'] =$this->di->url->create('users/tracked');

		//logout
		$array['Logga ut'] =$this->di->url->create('users/logout');

		return $array;
	}

	private function getAdminMenu()
	{
		$menu =$this->getUserMenu();

		unset($menu['Logga ut']);
		$menu['Återställ raderad'] = $this->di->url->create('users/undo-delete');

		if($this->calling){
			switch($this->calling) {
				case 'users':
					if($this->modifier){
						if($this->modifier != $this->user['id']){
							$menu['Ta bort'] = $this->di->url->create('users/soft-delete/'.$this->modifier);
							$menu['Utse admin'] = $this->di->url->create('users/update/'.$this->modifier.'/admin');
						}
					}
					break;
				case 'comment':
					if($this->modifier){
						$menu['Ta bort'] = $this->di->url->create('comment/soft-delete/'.$this->modifier);
					}
			}
		}
		$menu['Logga ut'] = $this->di->url->create('users/logout');
		return $menu;
	}
}