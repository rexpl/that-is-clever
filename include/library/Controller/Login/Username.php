<?php

namespace Clever\Library\Controller\Login;

use Mexenus\Database\Database;

use Clever\Library\Model\User;

class Username
{
	/**
	 * Verify if username already in use.
	 * 
	 * @param Clever\Library\Database
	 * 
	 * @return bool
	 */
	public function usernameExist(Database $database)
	{
		$user = new User($database);

		if (!$user->usernameExist($_GET['username'])) return false;

		return true;
	}
}