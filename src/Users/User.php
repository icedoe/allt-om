<?php
namespace Anax\Users;

class User extends \Anax\MVC\CDatabaseModel
{


	public function find($id, $key='id')
	{
		return parent::find($id, $key);
	}

	public function save($values=[], $key='id')
	{
		return parent::save($values, $key);
	}

	public function delete($value, $key='id')
	{
		return parent::delete($value, $key);
	}

	public function login($name, $password)
	{
		$user =$this->find($name, 'acronym')->getProperties();

		//$pw=$user['password'];
		//echo $password;


		if(password_verify($password, $user['password']) && $user['deleted'] == 'false'){
			$this->di->session->set('user', $user);

		}
	}

	public function logout()
	{
		$this->di->session->kill('user');
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

	public function gravUrl($email)
	{
		$hash =md5(strtolower(trim($email)));
		return 'http://www.gravatar.com/avatar/'.$hash;
	}
}
