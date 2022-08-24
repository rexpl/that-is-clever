<?php

namespace Clever\Library\Model;

use Mexenus\Database\Model;

class PersistantLogin extends Model
{
	/**
	 * Table name. Required.
	 * 
	 * @var string
	 */
	protected $table = 'user_persistant_login';


	/**
	 * Hidden fields on json_encode(). Default: []
	 * 
	 * @var array
	 */
	protected $hidden = [
		'id',
		'id_user',
		'serial',
		'token',
	];


	/**
	 * Verify if the serial exist or not.
	 * 
	 * @param string $serial
	 * 
	 * @return bool
	 */
	public function serialExist($serial)
	{
		return $this->select([1])->where('serial', $serial)->execute()->fetch();
	}
}