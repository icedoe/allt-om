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
}