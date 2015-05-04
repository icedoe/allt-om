<?php
namespace Anax\Users;

class UsersController implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectable;

	public function initialize()
	{
		$this->users =new \Anax\Users\User();
		$this->users->setDI($this->di);
	}

	public function indexAction()
	{
		$this->theme->setTitle("Användare");
		$this->views->add('default/page', [
        	'title' => "Användarsidor",
        	'content' => "Alternativ för användarhantering.",
        	'links' => [
        		[
        			'href' => $this->url->create('users/setup'),
        			'text' => "Återställ databasen"
        		],
        	    [
        	        'href' => $this->url->create('users/list'),
        	        'text' => "Visa alla användare",
        	    ],
        	    [
        	        'href' => $this->url->create('users/active'),
        	        'text' => "Visa aktiva användare",
        	    ],
        	    [
        	    	'href' => $this->url->create('users/inactive'),
        	    	'text' => "Visa inaktiva användare",
        	    ],
        	    [
        	    	'href' => $this->url->create('users/activate'),
        	    	'text' => "Aktivera användare",
        	    ],
        	    [
        	    	'href' => $this->url->create('users/deactivate'),
        	    	'text' => "Inaktivera användare",
        	    ],
        	    [
        	    	'href' => $this->url->create('users/deleted'),
        	    	'text' => 'Visa mjukt raderade',
        	    ],
        	    [
        	        'href' => $this->url->create('users/delete'),
        	        'text' => "Ta bort användare",
        	    ],
        	    [
        	    	'href' => $this->url->create('users/soft-delete'),
        	    	'text' => "Ta bort användare (Kan ångras)",
        	    ],
        	    [
        	    	'href' => $this->url->create('users/undo-delete'),
        	    	'text' => "Ångra radering",
        	    ],
        	    [
        	    	'href' => $this->url->create('users/add'),
        	    	'text' => "Lägg till användare",
        	    ],
        	    [
        	    	'href' => $this->url->create('users/update'),
        	    	'text' => "Uppdatera användare",
        	    ],
        	],
    	]);
	}

	public function listAction()
	{
		$all = $this->users->findAll();

		$this->theme->setTitle("Visa alla");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' =>"Alla användare",
			]);
	}

	public function activeAction()
	{
		$all =$this->users->query()
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
		$all =$this->users->query()->where('active IS NULL')->execute();

		$this->theme->setTitle("Inaktiva användare");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Alla inaktiva användare"]);
	}

	public function deletedAction()
	{
		$all =$this->users->query()
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
		$user =$this->users->find($id);

		$this->theme->setTitle("Visa användare");
		$this->views->add('users/list-all', [
			'users' => [$user],
			'title' => "Användare: $id",
		]);
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
				'created' => $now,
				'active' =>$now,
			];

			if($this->users->save($user)){
				$url =$this->di->url->create('users/id/'.$this->users->id);
				$this->response->redirect($url);
			}
		}

        $this->views->add('me/page', [
   	        'content' => $form->getHTML(),
   	    ]);
	}

	public function updateAction($id=false)
	{
		if(!$id){
			$form =$this->getIdSelect('Uppdatera');

			if($form->check()){
				$url =$this->di->url->create('users/update/'.$this->di->request->getPost('users'));
				$this->response->redirect($url);
			}
		}else{
			$user =$this->users->find($id);
			$user =$user->getProperties();
			$form =$this->getUserForm($user);

			if($form->check()){
				$u =[
					'name' => $this->di->request->getPost('name'),
					'acronym' => $this->di->request->getPost('acronym'),
					'email' => $this->di->request->getPost('email'),
					'updated' => gmdate('Y-m-d H:i:s')
				];
				if(!$this->di->request->getPost('password')){
					unset($user['password']);
				}else{
					$u['password'] =$this->di->request->getForm('password');
				}
				if($this->users->save($u)){
					$url =$this->di->url->create('users/id/'.$this->users->id);
					$this->response->redirect($url);
				}
			}else{
				$form->saveInSession =true;
			}
		}
		$this->views->add('me/page', [
			'content' => $form->getHTML()]);
	}
	
	public function activateAction($id=0)
	{
		$users =$this->users->query()->where('active IS NULL')->execute();

		$form =$this->getIdSelect('Aktivera', $users);

		if($form->check()){
			$id =$this->di->request->getPost('users');
			$user =$this->users->find($id);

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
		$users = $this->users->query()->where('active IS NOT NULL')->execute();

		$form =$this->getIdSelect('Inaktivera', $users);

		if($form->check()){
			$id =$this->di->request->getPost('users');
			$user =$this->users->find($id);

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
			if($this->users->delete($id)){
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
		$users =$this->users->query()->where("'deleted' = null")->execute();

		$form =$this->getIdSelect('Radera', $users);

		$content =null;

		if($form->check()){
			$form->addOutput('Användare raderad');
			$id =$this->di->request->getPost('users');
			$user =$this->users->find($id);
			$now =gmdate('Y-m-d H:i:s');
			$user->deleted =$now;
			$user->save();
			$url =$this->url->create('users/id/'.$id);
			$this->response->redirect($url);
		}
		
		$this->views->add('me/page', [
			'content' => $form->getHTML()]);
	}

	public function undoDeleteAction($id=null)
	{
		$users =$this->users->query()->where("deleted IS NOT NULL")->execute();

		$form =$this->getIdSelect('Återställ', $users);

		if($form->check()){
			$id =$this->di->request->getpost('users');
			$user =$this->users->find($id);
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
    	        'password'  => ['varchar(255)', 'not null'],
    	        'created'   => ['datetime'],
    	        'updated'   => ['datetime'],
    	        'deleted'   => ['datetime'],
    	        'active'    => ['datetime'],
	        ]
	    );
	    $this->db->execute();
	     $this->db->insert(
	        'user',
		        ['acronym', 'email', 'name', 'password', 'created', 'active']
	    );

	    $now =gmdate('Y-m-d H:i:s');

	    $this->db->execute([
	        'admin',
	        'admin@dbwebb.se',
	        'Administrator',
	        password_hash('admin', PASSWORD_DEFAULT),
	        $now,
	        $now
	    ]);
	    $this->db->execute([
	        'doe',
	        'doe@dbwebb.se',
	        'John/Jane Doe',
	        password_hash('doe', PASSWORD_DEFAULT),
	        $now,
	        $now
	    ]);
	    $url=$this->url->create('users');
	    $this->response->redirect($url);
	}

	public function getUserForm($user=[])
	{
		$user['name'] =isset($user['name']) ? $user['name'] : '';
		$user['acronym'] =isset($user['acronym']) ? $user['acronym'] : '';
		$user['email'] =isset($user['email']) ? $user['email'] : '';
		$passValidation =isset($user['password']) ? "pass" : "not_empty";
		$passReq =isset($user['password']) ? false : true;
		
		$vals=[
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
	
}
