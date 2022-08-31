<?php 

namespace Clever\Library\Model;

use Mexenus\Database\Model;

class GamePlayers extends Model
{
	/**
	 * Table name. Required.
	 * 
	 * @var string
	 */
	protected $table = 'game_players';


	/**
	 * Hidden fields on json_encode(). Default: []
	 * 
	 * @var array
	 */
	protected $hidden = [
		'token_player',
	];
}