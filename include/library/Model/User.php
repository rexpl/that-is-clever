<?php

namespace Clever\Library\Model;

use Mexenus\Database\Model;

class User extends Model
{
	/**
	 * Table name. Required.
	 * 
	 * @var string
	 */
	protected $table = 'user';


	/**
	 * Hidden fields on json_encode(). Default: []
	 * 
	 * @var array
	 */
	protected $hidden = [
		'status',
		'password',
		'mail_hash',
		'protected_key',
		'mail',
		'failed_login_count',
	];


	/**
	 * Default values for fields. Default: []
	 * 
	 * @var array
	 */
	protected $default = [
		'status' => 1,
		'failed_login_count' => 0,
	];


	/**
	 * Verify if the user exist or not.
	 * 
	 * @param string $username
	 * 
	 * @return bool
	 */
	public function usernameExist($username)
	{
		return $this->select([1])->where('username', $username)->execute()->fetch();
	}
}