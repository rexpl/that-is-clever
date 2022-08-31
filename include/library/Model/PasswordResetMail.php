<?php

namespace Clever\Library\Model;

use Mexenus\Database\Model;

class PasswordResetMail extends Model
{
	/**
	 * Table name. Required.
	 * 
	 * @var string
	 */
	protected $table = 'mail_password_reset';


	/**
	 * Hidden fields on json_encode(). Default: []
	 * 
	 * @var array
	 */
	protected $hidden = [
		'id',
		'id_user',
		'send_time',
		'serial',
		'token',
		'used_time',
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