<?php
namespace Anax\Users;

class UsersController implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectable, \Anax\MVC\TRedirectHelpers;


	public function indexAction()
	{
		$this->di->users->query()->where("deleted ='false'");
		$all = $this->di->users->execute();
		$users=[];
		$n=-1;
		foreach($all as $key => $user){
			if(($key+1)%4 == 1){
				$users[++$n] =[];
			}
			$user->shortdesc =$this->di->textFilter->doFilter(htmlentities($user->shortdesc), 'markdown');
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

	public function startAction($post, $calling)
	{
		$all =$this->di->users->query()->where("deleted = 'false'")->orderBy('posted DESC')->limit(4)->execute();
		foreach($all as &$user){
			$user->shortdesc =$this->di->textFilter->doFilter(htmlentities($user->shortdesc), 'markdown');
		}

		$this->views->add('users/list-all', [
			'users' => [$all],
			], 'flash'
		);
		$this->dispatcher->forward([
			'controller' => 'tags',
			'action' => 'start',
			'params' => [$post, $calling]
			]);
	}

	
	
	

	public function idAction($id=null)
	{
		$user =$id ? $this->di->users->find($id)->getProperties() : $this->di->session->get('user');
		$id =$id ? $id : $user['id'];

		$user['shortdesc'] =$this->di->textFilter->doFilter(htmlentities($user['shortdesc']), 'markdown');
		$user['description'] =$this->di->textFilter->doFilter(htmlentities($user['description']), 'markdown');

		$this->theme->setTitle("Visa användare");
		$this->views->add('users/view', [
			'user' => $user,
			'title' => "Användare: $id",
		],
		'main'
		);

		$this->di->dispatcher->forward([
			'controller' => 'comment',
			'action' => 'index',
			'params' => [$_POST, 'users', $user['acronym']],
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
			//	$this->di->session->set('user', $user);
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
				'shortdesc' => $this->di->request->getPost('shortdesc'),
				'description' => $this->di->request->getPost('description'),
				'image' =>$this->di->users->gravUrl($this->di->form->Value('email')),
				'password' => password_hash($this->di->form->Value('password'), PASSWORD_DEFAULT),
				'type' => 'user',
				'created' => $now,
				'active' =>$now,
			];

			if($this->di->users->save($user)){
				$this->di->users->login($user['acronym'], $this->di->form->Value('password'));

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
			$this->doSoftDelete($id);
		}
		
	}

	public function undoDeleteAction($id=null)
	{
		$users =$this->di->users->query()->where("deleted = 'true'")->execute();
		print_r($users);

		$form =$this->getIdSelect('Återställ', $users);

		if($form->check()){
			$id =$this->di->request->getpost('users');
			$this->di->db->update('User', ['deleted'], ['false'], 'id='.$id);
			$this->di->db->execute();
			
		
			$url =$this->url->create('users/id/'.$id);
			$this->response->redirect($url);
		}
		$this->views->add('me/page', [
			'content' => $form->getHTML()]);
	}

	public function setup()
	{
		$tbl=false;
		$admin=false;
		$user=false;

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
   		        'deleted'   => ['varchar(5)', "default 'false'"],
   		        'active'    => ['datetime'],
	        ]
	    );
	    if($this->db->execute()){
	    	$tbl =true;
	    }
	    $this->db->insert(
	        'user',
		        ['acronym', 'email', 'name', 'shortdesc', 'password', 'image', 'type', 'posted', 'points', 'created', 'active']
	    );

	    $now =gmdate('Y-m-d H:i:s');

	    if($this->db->execute([
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
	    	])) { $admin =true;}
	    if($this->db->execute([
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
	    	])){ $user =true;}
	    return array($tbl, $admin, $user);
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

	public function getIdSelect($act, $users)
	{
		$all =$users;
			
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
	private function doSoftDelete($acronym)
	{
			$now =gmdate('Y-m-d H:i:s');
			$this->di->db->update('user', ['deleted', 'updated'], ['true', $now], 'acronym='.$acronym);
			$this->di->db->execute();
			$u = $this->di->session->get('user');
			if($u['acronym'] == $acronym){
				$this->di->users->logout('user');
			}
			$user =$this->di->users->find($acronym, 'acronym');
			$url =$this->url->create('users/id/'.$user->id);
			$this->response->redirect($url);
	}
}
