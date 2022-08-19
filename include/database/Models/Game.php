<?php

namespace Mexenus\Database\Models;

use Mexenus\Database\Model;

class Game extends Model
{
	/**
	 * Table name. Required.
	 * 
	 * @var string
	 */
	protected $table = 'user';


	/**
	 * Table primary key. Default: id
	 * 
	 * @var string
	 */
	protected $primary = 'id';


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
	protected $default = [];
}