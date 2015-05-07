<?php
namespace Anax\Users;

class UsersController implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectable, \Anax\MVC\TRedirectHelpers;


	public function indexAction()
	{
		$this->di->users->query()->where('deleted is null');
		$all = $this->di->users->execute();
		$users=[];
		$n=-1;
		foreach($all as $key => $user){
			if(($key+1)%4 == 1){
				$users[++$n] =[];
			}
			$users[$n][] =$user;
		}

		$this->theme->setTitle("Användare");
		$this->views->add('users/list-all', [
			'users' => $users,
			'title' =>"Alla användare",
			]);
		$this->di->dispatcher->forward([
			'controller' => 'sidebar',
			'action' => '',
			'params' => [$_POST, 'users'],
			]
		);
	}

	

	public function activeAction()
	{
		$all =$this->di->users->query()
			->where('active IS NOT NULL')
			->andWhere('deleted IS NULL')
			->execute();

		$this->theme->setTitle("Visa alla aktiva");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Alla aktiva användare",
		]);
	}

	public function inactiveAction()
	{
		$all =$this->di->users->query()->where('active IS NULL')->execute();

		$this->theme->setTitle("Inaktiva användare");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Alla inaktiva användare"]);
	}

	public function deletedAction()
	{
		$all =$this->di->users->query()
			->where('deleted IS NOT NULL')
			->execute();

		$this->theme->setTitle("Mjukraderade användare");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Alla mjukt raderade användare",
		]);
	}

	public function idAction($id=null)
	{
		$user =$id ? $this->di->users->find($id)->getProperties() : $this->di->session->get('user');
		print_r($user);
		$id =$id ? $id : $user['id'];

		$this->theme->setTitle("Visa användare");
		$this->views->add('users/view', [
			'user' => $user,
			'title' => "Användare: $id",
		],
		'main'
		);

		$this->di->dispatcher->forward([
			'controller' => 'sidebar',
			'action' => '',
			'params' => [$_POST, 'users', $id],
			]
		);
	}

	public function loginAction()
	{
		$form =$this->getLoginForm();

		if($form->check()){
			$name =$this->di->form->Value('username');
			$password =password_hash($this->di->form->Value('password'), PASSWORD_DEFAULT);

			if($user =$this->di->users->login($name, $password)){
				$this->di->session->set('user', $user);
				$this->redirectTo();
			}
		}
		$this->views->add(
			'me/page', [
				'title' => 'Logga in',
				'form' => $form->getHTML()
			],
			'main');
	}

	public function logoutAction()
	{
		$this->di->users->logout();
		$this->redirectTo($this->di->session->get('lastPage'));
	}


	public function addAction()
	{
		$form =$this->getUserForm();

		if($form->check()){
			$now =gmdate('Y-m-d H:i:s');
		
			$user =[
				'acronym' => $this->di->form->Value('acronym'),
				'email' => $this->di->form->Value('email'),
				'name' => $this->di->form->Value('name'),
				'password' => password_hash($this->di->form->Value('password'), PASSWORD_DEFAULT),
				'type' => 'user',
				'created' => $now,
				'active' =>$now,
			];

			if($this->di->users->save($user)){
				$this->di->users->login($user['name'], $user['password']);

				$url = $this->di->url->create('users/id/'.$this->di->users->id);
				$this->response->redirect($url);
			}
		}

        $this->views->add('users/form', [
        	'title' => 'Skapa användare',
   	        'form' => $form->getHTML(),
   	    ],
   	    'main');
	}

	public function updateAction($id=false, $upgrade=false)
	{
		$user = $id ? $this->di->users->find($id)->getProperties() : $this->di->session->get('user');
		
		if($upgrade){
			$u =$this->di->session->get('user');
			if($u['type'] == 'admin'){
				$u =[
					'id' => $user['id'],
					'type' => $upgrade
				];
				if($this->di->users->save($u)){
				$url =$this->di->url->create('users/id/'.$this->di->users->id);
				$this->response->redirect($url);
				}
			}
		}

		$form =$this->getUserForm($user);

		if($form->check()){
			$u =[
				'name' => $this->di->request->getPost('name'),
				'acronym' => $this->di->request->getPost('acronym'),
				'email' => $this->di->request->getPost('email'),
				'shortdesc' => $this->di->request->getPost('shortdesc'),
				'description' => $this->di->request->getPost('description'),
				'updated' => gmdate('Y-m-d H:i:s')
			];
			if(!$this->di->request->getPost('password')){

				unset($user['password']);
			}else{
				$u['password'] =$this->di->request->getForm('password');
			}
			if(!empty($this->di->request->getPost('id'))){
				$u['id'] = $this->di->request->getPost('id');
			}
			if($this->di->users->save($u)){
				$url =$this->di->url->create('users/id/'.$this->users->id);
				$this->response->redirect($url);
			}
		}else{
			$form->saveInSession =true;
		}

		$this->views->add('me/page', [
			'content' => $form->getHTML()]);
	}
	
	public function activateAction($id=0)
	{
		$users =$this->di->users->query()->where('active IS NULL')->execute();

		$form =$this->getIdSelect('Aktivera', $users);

		if($form->check()){
			$id =$this->di->request->getPost('users');
			$user =$this->di->users->find($id);

			$now =gmdate('Y-m-d H:i:s');
			$user->active =$now;
			$user->updated =$now;
			$user->deleted =null;

			$user->save();
			$url =$this->di->url->create('users/id/'.$id);
			$this->response->redirect($url);
		}
		$this->views->add('me/page', ['content' => $form->getHtml()]);
	}

	public function deactivateAction()
	{
		$users = $this->di->users->query()->where('active IS NOT NULL')->execute();

		$form =$this->getIdSelect('Inaktivera', $users);

		if($form->check()){
			$id =$this->di->request->getPost('users');
			$user =$this->di->users->find($id);

			$now =gmdate('Y-m-d H:i:s');
			$user->active =null;
			$user->updated =$now;

			$user->save();
			$url =$this->di->url->create('users/id/'.$this->users->id);
			$this->response->redirect($url);
		}
		$this->views->add('me/page', ['content' =>$form->getHTML()]);
	}

	public function deleteAction()
	{
//		$id =$this->di->request->getPost('users');
//		$res =$this->users->delete($id);

		$form =$this->getIdSelect('Radera');

		$content =null;

		if($form->check()){
			$id =$this->di->request->getpost('users');
			if($this->di->users->delete($id)){
				$content ="Användare $id raderades";
				$url = $this->di->url->create('users/delete');
				$this->response->redirect($url);
			}else{
				$content ="Kunde inte radera användare";
			}
		}
		$this->views->add('me/page', [
			'content' => $content]);
		$this->views->add('me/page', [
			'content' => $form->getHTML()]);
	
		

	//	$url =$this->url->create('users/delete');
	//	$this->response->redirect($url);


    
	}


	public function softDeleteAction($id=null)
	{
		if($id){
			$u = $this->di->session->get('user');
			if($u['type'] == 'admin'){
				$this->doSoftDelete($id);
			}
		}
		$users =$this->di->users->query()->where("'deleted' = null")->execute();

		$form =$this->getIdSelect('Radera', $users);

		$content =null;

		if($form->check()){
			$form->addOutput('Användare raderad');
			$id =$this->di->request->getPost('users');
			$this->doSoftDelete($id);
			
		}
		
		$this->views->add('me/page', [
			'content' => $form->getHTML()]);
	}

	public function undoDeleteAction($id=null)
	{
		$users =$this->di->users->query()->where("deleted IS NOT NULL")->execute();

		$form =$this->getIdSelect('Återställ', $users);

		if($form->check()){
			$id =$this->di->request->getpost('users');
			$user =$this->di->users->find($id);
			$user->deleted =null;
			$user->save();
		
			$url =$this->url->create('users/id/'.$id);
			$this->response->redirect($url);
		}
		$this->views->add('me/page', [
			'content' => $form->getHTML()]);
	}

	public function setupAction()
	{
		 $this->db->dropTableIfExists('user')->execute();
 
    	$this->db->createTable(
    	    'user',
    	    [
    	        'id'        => ['integer', 'auto_increment', 'primary key', 'not null'],
    	        'acronym'   => ['varchar(20)', 'unique', 'not null'],
    	        'email'     => ['varchar(80)', 'not null'],
    	        'name'      => ['varchar(80)', 'not null'],
    	        'shortdesc'	=> ['varchar(255)'],
    	        'description' =>['text'],
    	        'password'  => ['varchar(255)', 'not null'],
    	        'image'		=> ['varchar(255)'],
    	        'posted'	=> ['integer'],
    	        'points'	=> ['integer'],
    	        'type'		=> ['varchar(20)'],
    	        'created'   => ['datetime'],
    	        'updated'   => ['datetime'],
    	        'deleted'   => ['datetime'],
    	        'active'    => ['datetime'],
	        ]
	    );
	    $this->db->execute();
	     $this->db->insert(
	        'user',
		        ['acronym', 'email', 'name', 'shortdesc', 'password', 'image', 'type', 'posted', 'points', 'created', 'active']
	    );

	    $now =gmdate('Y-m-d H:i:s');

	    $this->db->execute([
	        'admin',
	        'admin@dbwebb.se',
	        'Administrator',
	        'En av sidans administratörer',
	        password_hash('admin', PASSWORD_DEFAULT),
	        $this->di->users->gravUrl('admin@dbwebb.se'),
	        'admin',
	        '0',
	        '0',
	        $now,
	        $now
	    ]);
	    $this->db->execute([
	        'doe',
	        'doe@dbwebb.se',
	        'John/Jane Doe',
	        'En förvirrad stackars användare, vars enda brott var registrering',
	        password_hash('doe', PASSWORD_DEFAULT),
	        $this->di->users->gravUrl('doe@dbwebb.se'),
	        'user',
	        '0',
	        '0',
	        $now,
	        $now
	    ]);
	    $url=$this->url->create('users');
	    $this->response->redirect($url);
	}

	public function getLoginForm()
	{
		$vals =[
			'username' => [
				'type' => 'text',
				'label' => 'Användarnamn',
				'required' => true,
				'validation' => ['not_empty'],
			],
			'password' => [
				'type' => 'password',
				'label' => 'Lösenord',
				'required' => true,
				'validation' => ['not_empty'],
			],
			'doLogin' => [
				'type' => 'submit',
				'callback' => function() {return true;}
			],
		];
		$form =$this->di->form->create([], $vals);
		return $form;
	}

	public function getUserForm($user=[])
	{
		$user['id'] =isset($user['id']) ? $user['id'] : null;
		$user['name'] =isset($user['name']) ? $user['name'] : '';
		$user['acronym'] =isset($user['acronym']) ? $user['acronym'] : '';
		$user['email'] =isset($user['email']) ? $user['email'] : '';
		$user['shortdesc'] =isset($user['shortdesc']) ? $user['shortdesc'] : '';
		$user['description'] =isset($user['description']) ? $user['description'] : '';
		$passValidation =isset($user['password']) ? "pass" : "not_empty";
		$passReq =isset($user['password']) ? false : true;
		
		$vals=[
			'id' => [
				'type'		=> 'hidden',
				'value'		=> $user['id']
			],
			'name' => [
                'type'        => 'text',
                'label'       => 'Name of contact person:',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value'		  => $user['name']
            ],
            'acronym' => [
                'type'          => 'text',
                'required'      => true,
                'validation'    => ['not_empty'],
                'value'			=> $user['acronym']
            ],
            'email' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
                'value'		  => $user['email']
            ],
            'password' => [
                'type'        => 'password',
                'required'    => $passReq,
                'validation'  => [$passValidation],
            ],
            'shortdesc'	=> [
            	'type'		=>'text',
            	'value'		=> $user['shortdesc'],
            ],
            'description' => [
            	'type'		=> 'textarea',
            	'value'		=> $user['description'],
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => function() { return true;}
            ],
        ];

        if(isset($user['id'])){
			$vals['id'] =[
					'type'	=> 'hidden',
					'value'	=> $user['id'],
			];
		}

        $form = $this->di->form->create([], $vals);
        

        return $form;
	}

	public function getIdSelect($act, $users=false)
	{
		$all =$users ? $users : $this->users->findAll();
			
			$options =['default' => 'Välj användare'];
			foreach($all as $user){
				$options[$user->id] = $user->acronym;
			}
			$array =[
				'users' => [
					'type' =>'select',
					'label' =>'Användare',
					'options' =>$options,
				],
				'submit' => [
					'type' =>'submit',
					'value' =>"$act",
					'callback'  => function($form) { return true;}
				]
			];
			$form =$this->di->form->create([], $array);

			return $form;
	}
	private function doSoftDelete($id)
	{
		$user =$this->di->users->find($id);
			$now =gmdate('Y-m-d H:i:s');
			$user->deleted =$now;
			$user->save();
			$url =$this->url->create('users/id/'.$id);
			$this->response->redirect($url);
	}
}
