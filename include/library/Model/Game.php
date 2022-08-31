<?php 

namespace Clever\Library\Model;

use Mexenus\Database\Model;

class Game extends Model
{
	/**
	 * Table name. Required.
	 * 
	 * @var string
	 */
	protected $table = 'game';


	/**
	 * Look if given token already exist for freidns game.
	 *
	 * @param string $token
	 * 
	 * @return bool
	 */
	public function tokenExist($token)
	{
		return $this->select([1])->where('type', 3)->where('token', $token)->execute()->fetch();
	}
}