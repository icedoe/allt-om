<?php

namespace Anax\Session;

/**
 * Anax base class for wrapping sessions.
 *
 */
class MySession extends \Anax\Session\CSession
{
	public function kill($key)
	{
		if($this->has($key)){
			unset($_SESSION[$key]);
		}
	}
}