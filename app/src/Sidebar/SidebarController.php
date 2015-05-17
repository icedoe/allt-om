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
				<input type='text' name='username' placeholder='Akronym' required='true' /><br />
				<input type='password' name='password' placeholder='Lösenord' required='true' /><br />
				<input type='submit' name='doLogin' value='Logga in' /><br />
				<a href='".$url."'>Eller skapa konto</a>
			</form>";
		return $html;
	}

	private function getUserMenu()
	{
		$array =[];

		//Question
		$array['Ställ fråga'] = [
			'icon' => "<i class='fa-li fa fa-plus'></i>",
			'url' => $this->di->url->create('comment/edit')
			];
		//profile
		if($this->calling == 'users' && $this->modifier == $this->user['acronym']) {
			$array['Redigera profil'] = [
				'icon' => "<i class='fa-li fa fa-pencil'></i>",
				'url' =>$this->di->url->create('users/update')
				];
			$array['Avregistera'] = [
				'icon' => "<i class='fa-li fa fa-ban'></i>",
				'url' => $this->di->url->create('users/soft-delete/'.$this->user['acronym'])
				];
		} else {
			$array['Min profil'] = [
				'icon' => "<i class='fa-li fa fa-user'></i>",
				'url' => $this->di->url->create('users/id')
			];
		}


		//logout
		$array['Logga ut'] = [
			'icon' => "<i class='fa-li fa fa-sign-out'></i>",
			'url' => $this->di->url->create('users/logout')
			];

		return $array;
	}

	private function getAdminMenu()
	{
		$menu =$this->getUserMenu();

		unset($menu['Logga ut']);
		$menu['Återställ raderad'] = [
			'icon' => "<i class='fa-li fa fa-medkit'></i>",
			'url' => $this->di->url->create('users/undo-delete')
			];

		if($this->calling){
			switch($this->calling) {
				case 'users':
					if($this->modifier){
						if($this->modifier != $this->user['acronym']){
							$menu['Ta bort'] = [
							'icon' => "<i class='fa-li fa fa-ban'></i>",
							'url' => $this->di->url->create('users/soft-delete/'.$this->modifier)
							];
							$menu['Utse admin'] = [
								'icon' => "<i class='fa-li fa fa-angle-double-up'></i>",
								'url' => $this->di->url->create('users/update/'.$this->modifier.'/admin')
								];
						}
					}
					break;
				case 'comment':
					if($this->modifier){
						$menu['Ta bort'] = [
							'icon' => "<i class='fa-li fa fa-trash'></i>",
							'url' => $this->di->url->create('comment/soft-delete/'.$this->modifier)
							];
					}
			}
		}
		$menu['Logga ut'] = [
			'icon' => "<i class='fa-li fa fa-sign-out'></i>",
			'url' => $this->di->url->create('users/logout')
			];
		return $menu;
	}
}
