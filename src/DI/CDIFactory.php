<?php
namespace Anax\DI;

class CDIFactory extends CDIFactoryDefault
{
	public function __construct()
	{
		parent::__construct();
		$this->set('form', '\Mos\HTMLForm\CForm');

		$this->setShared('db', function() {
            $db =new \Mos\Database\CDatabaseBasic(require ANAX_APP_PATH.'config/config_mysql.php');
            return $db;
        });

		$this->setShared('users', function() {
			$users =new \Anax\Users\User();
			$users->setDI($this);
			return $users;
		});
	}
}