<?php

namespace Clever\Library\Game\Gameplay;

use Workerman\Connection\TcpConnection;

class Player
{
	/**
	 * Connection of the player.
	 * 
	 * @var Workerman\Connection\TcpConnection
	 */
	private $connection;


	/**
	 * Player model to save the scores at the end of the game.
	 * 
	 * @var object
	 */
	private $player;


	/**
	 * User model to access user data in game.
	 * 
	 * @var object
	 */
	public $user;


	/**
	 * Contains the user board.
	 * 
	 * @var array
	 */
	public $board = ['p1'=>0,'re'=>0,'fox'=>0,'blue'=>[2=>false,3=>false,4=>false,5=>false,6=>false,7=>false,8=>false,9=>false,10=>false,11=>false,12=>false],'yellow'=>[11=>false,12=>false,13=>false,14=>false,15=>false,16=>false,21=>false,22=>false,23=>false,24=>false,25=>false,26=>false],'green'=>0,'orange'=>[1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0],'orange_position'=>1,'purple'=>[1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0],'purple_position'=>1,'last_purple_value'=>0];


	/**
	 * Contains the message to be send to the user.
	 * 
	 * @var array
	 */
	public $message = [];


	/**
	 * Game instance.
	 * 
	 * @var object
	 */
	public $game;


	/**
	 * Method names for different colors.
	 * 
	 * @var array
	 */
	private const colorMethodNames = [
		'yellow' => 'applyYellow',
		'orange' => 'applyOrange',
		'purple' => 'applyPurple',
		'green' => 'applyGreen',
	];


	/**
	 * Contains wich key in $this->board to adress per bonus ID.
	 * 
	 * @var array
	 */
	private const keyByBonusID = [
		1 => 5,
		3 => 'fox',
		4 => 're',
		7 => 'p1',
		9 => 4,
		12 => 6,
	];


	/**
	 * Combinations to get a bonus in blue.
	 * 
	 * @var array
	 */
	private const blueBonusPosition = [
		1 => [2, 3, 4],
		2 => [5, 6, 7, 8],
		3 => [9, 10, 11, 12],
		4 => [5, 9],
		5 => [2, 6, 10],
		6 => [3, 7, 11],
		7 => [4, 8, 12],
	];


	/**
	 * Combinations to get a bonus in yellow.
	 * 
	 * @var array
	 */
	private const yellowBonusPosition = [
		8 => [13, 16, 15],
		9 => [12, 11, 25],
		5 => [21, 22, 14],
		3 => [23, 24, 26],
		7 => [13, 11, 22, 26],
	];


	/**
	 * Positions & IDs of the bonuses in green.
	 * 
	 * @var array
	 */
	private const greenBonusPosition = [
		4 => 7,
		6 => 8,
		7 => 3,
		9 => 6,
		10 => 4,
	];


	/**
	 * Positions & IDs of the bonuses in orange.
	 * 
	 * @var array
	 */
	private const orangeBonusPosition = [
		3 => 4,
		4 => 10,
		5 => 2,
		6 => 7,
		7 => 10,
		8 => 3,
		9 => 10,
		10 => 6,
		11 => 11,
	];


	/**
	 * Positions & IDs of the bonuses in purple.
	 * 
	 * @var array
	 */
	private const purpleBonusPosition = [
		3 => 4,
		4 => 8,
		5 => 7,
		6 => 2,
		7 => 3,
		8 => 4,
		9 => 5,
		10 => 12,
		11 => 7,
	];


	/**
	 * @param Workerman\Connection\TcpConnection
	 * @param object $player
	 */
	public function __construct(TcpConnection $connection, $player)
	{
		$this->connection = $connection;

		$this->player = $player;
	}


	/**
	 * Prepares a message to be sent.
	 * 
	 * @param array $data
	 * @param bool $reset
	 * 
	 * @return void
	 */
	public function message(array $data, $reset = false)
	{
		if ($reset) $this->message = [];

		foreach ($data as $key => $value) {

			$this->message[$key] = $value;
		}
	}


	/**
	 * Send the message queue.
	 * 
	 * @param bool $board
	 * @param bool $ignore
	 * 
	 * @return void
	 */
	public function send($board = false, $ignore = false)
	{
		if ($ignore && $board) return $this->sendBoard();

		if ($board) $this->message['board'] = $this->board;

		$this->connection->send(json_encode($this->message));

		if (isset($this->message['bonus'])) unset($this->message['bonus']);
	}


	/**
	 * Sends the board to the user.
	 * 
	 * @return void
	 */
	public function sendBoard()
	{
		$this->connection->send(json_encode(['board' => $this->board]));
	}


	/**
	 * Apply a bonus to the board depending on the round.
	 * 
	 * @param int $round
	 * 
	 * @return int
	 */
	public function applyNewRoundBonus($round)
	{
		if ($round == 2) {

			$this->board['p1']++;
			return 7;
		}

		$this->board['re']++;
		return 4;
	}


	/**
	 * Attempt to use a +1 bonus if available.
	 * 
	 * @return bool
	 */
	public function usePlusOneIfAvailable()
	{
		if ($this->board['p1'] == 0) return false;

		$this->board['p1']--;

		return true;
	}


	/**
	 * Attempt to use a replay bonus if available.
	 * 
	 * @return bool
	 */
	public function useReplayIfAvailable()
	{
		if ($this->board['re'] == 0) return false;

		$this->board['re']--;

		return true;
	}


	/**
	 * Attempt to apply the newly acquired bonus to the board.
	 * Returns the bonus ID + any other bonus IDs triggered by the new bonus.
	 * 
	 * @param int $bonusID
	 * 
	 * @return array
	 */
	private function applyBonus($bonusID)
	{
		switch ($bonusID) {
			case 1:
			case 9:
			case 12:

				$valueToAdd = [];
				$result = $this->applyOrange($this->board['orange_position'] + 1, self::keyByBonusID[$bonusID]);
				
			break;
			case 3:
			case 4:
			case 7:

				$this->board[self::keyByBonusID[$bonusID]]++;

			break;
			case 5:

				$result = $this->applyGreen($this->board['green'] + 1, 6);

			break;
			case 6:

				$result = $this->applyPurple($this->board['purple_position'] + 1, 6);

			break;
			case 10:

				$this->board['orange'][$this->board['orange_position'] - 1]
					= $this->board['orange'][$this->board['orange_position'] - 1] * 2;

			break;
			case 11:

				$this->board['orange'][11] = $this->board['orange'][11] * 3;

			break;
		}

		if (isset($result) && $result) return array_merge($result, [$bonusID]);
		
		return [$bonusID];
	}


	/**
	 * Returns all used positions by a color.
	 * Works only for blue and yellow.
	 * 
	 * @param string $color
	 * 
	 * @return array
	 */
	private function allUsedPosition($color)
	{
		$allUsedPosition = [];

		foreach ($this->board[$color] as $key => $value) {
			
			if ($value) $allUsedPosition[] = $key;
			
		}

		return $allUsedPosition;
	}


	/**
	 * Loop through array to detect the presence of a bonus on a new position.
	 * Works only for blue and yellow. Theis method also applies the bonus.
	 * 
	 * @param array $bonusPositions 
	 * @param string $color
	 * @param int $newPosition
	 * 
	 * @return array
	 */
	private function detectBonusOnLine(array $bonusPositions, $color, $newPosition)
	{
		$allUsedPosition = $this->allUsedPosition($color);

		$bonus = [];

		foreach ($bonusPositions as $key => $value) {
			
			if (!in_array($newPosition, $value)) continue;

			$i = 0;

			foreach ($value as $bonusLine) {
				
				if (!in_array($bonusLine, $allUsedPosition)) break;

				$i++;
			}

			if ($i != count($value)) continue;

			array_merge($bonus, $this->applyBonus($key));
		}

		return $bonus;
	}


	/**
	 * Add new value/cross to blue.
	 * 
	 * @param int $position
	 * @param int $value
	 * 
	 * @return bool|array
	 */
	private function applyBlue($position, $value)
	{
		if (
			!isset($this->board['blue'][$position])
			|| $this->board['blue'][$position]
			|| $position != $value
		) return false;

		$this->board['blue'][$position] = true;

		return [
			'success' => true,
			'bonus' => $this->detectBonusOnLine(self::blueBonusPosition, 'blue', $position),
		];
	}


	/**
	 * Add new value/cross to yellow.
	 * 
	 * @param int $position
	 * @param int $value
	 * 
	 * @return bool|array
	 */
	private function applyYellow($position, $value)
	{
		if (
			!isset($this->board['yellow'][$position])
			|| $this->board['yellow'][$position]
		) return false;

		$neededValue = $position > 20 ? $position - 20 : $position - 10;

		if ($neededValue != $value) return false;

		$this->board['yellow'][$position] = true;

		return [
			'success' => true,
			'bonus' => $this->detectBonusOnLine(self::yellowBonusPosition, 'yellow', $position),
		];
	}


	/**
	 * Detects bonus on the newly added position.
	 * 
	 * @param array $keyPairs
	 * @param int $position
	 * 
	 * @return array
	 */
	private function detectBonusOnPosition(array $keyPairs, $position)
	{
		if (!isset($keyPairs[$position])) return [];

		return $this->applyBonus($keyPairs[$position]);
	}


	/**
	 * Add new value/cross to green.
	 * 
	 * @param int $position
	 * @param int $value
	 * 
	 * @return bool|array
	 */
	private function applyGreen($position, $value)
	{
		if (
			$this->board['green'] == 11
			|| ($this->board['green'] + 1) != $position
		) return false;

		$neededValue = ($this->board['green'] + 1) > 5 ? $this->board['green'] - 5 : $this->board['green'] + 1;

		if ($value < $neededValue) return false;

		$this->board['green']++;

		return [
			'success' => true,
			'bonus' => $this->detectBonusOnPosition(self::greenBonusPosition, $position),
		];
	}


	/**
	 * Add new value/cross to orange.
	 * 
	 * @param int $position
	 * @param int $value
	 * 
	 * @return bool|array
	 */
	private function applyOrange($position, $value)
	{
		if (
			$this->board['orange_position'] == 11
			|| !(1 <= $value && $value <= 6)
			|| ($this->board['orange_position']) != $position
		) return false;

		$this->board['orange'][$position] = $value;
		$this->board['orange_position']++;

		return [
			'success' => true,
			'bonus' => $this->detectBonusOnPosition(self::orangeBonusPosition, $position),
		];
	}


	/**
	 * Add new value/cross to purple.
	 * 
	 * @param int $position
	 * @param int $value
	 * 
	 * @return bool|array
	 */
	private function applyPurple($position, $value)
	{
		if (
			$this->board['purple_position'] == 11
			|| $this->board['last_purple_value'] >= $value
			|| ($this->board['purple_position']) != $position
		) return false;

		$this->board['purple'][$position] = $value;
		$this->board['purple_position']++;

		$this->board['last_purple_value'] = $value == 6 ? 0 : $value;

		return [
			'success' => true,
			'bonus' => $this->detectBonusOnPosition(self::purpleBonusPosition, $position),
		];
	}


	/**
	 * Handles an incomming choice.
	 * 
	 * @param Clever\Library\Game\Gameplay\DiceSet $diceSet
	 * @param array $choice
	 * @param callable $func
	 * 
	 * @return mixed
	 */
	private function choice(callable $func, $choice, $blue)
	{
		// If dice is not in list of dices no need to continue
		if (!isset(call_user_func($func)[$choice['dice']])) return false;

		// Make sure chosen color = color dice, except for white
		if ($choice['dice'] != $choice['color'] && $choice['dice'] != 'white') return false;

		if ($choice['color'] === 'blue') return $this->applyBlue($choice['position'], $blue);

		return call_user_func_array(
			[$this, self::colorMethodNames[$choice['color']]],
			[$choice['position'], call_user_func($func)[$choice['dice']]],
		);
	}


	/**
	 * Handles an incomming active choice.
	 * 
	 * @param Clever\Library\Game\Gameplay\DiceSet $diceSet
	 * @param array $choice
	 * 
	 * @return mixed
	 */
	public function activeChoice(DiceSet $diceSet, $choice)
	{
		return $this->choice([$diceSet, 'activeDices'], $choice, $diceSet->blue());
	}


	/**
	 * Handles an incomming passive choice.
	 * 
	 * @param Clever\Library\Game\Gameplay\DiceSet $diceSet
	 * @param array $choice
	 * 
	 * @return mixed
	 */
	public function passiveChoice(DiceSet $diceSet, $choice)
	{
		return $this->choice([$diceSet, 'passiveDices'], $choice, $diceSet->blue());
	}


	/**
	 * Handles an incomming active bonus choice.
	 * 
	 * @param int $bonusID
	 * @param array $choice
	 * 
	 * @return mixed
	 */
	public function bonusChoice($bonusID, $choice)
	{
		$value = [];
		$blue = null;

		switch ($bonusID) {
			case 2:

				if ($choice['color'] != 'yellow') return false;

				$value['yellow'] = $choice['position'] > 20
					? $choice['position'] - 20
					: $choice['position'] - 10;
				
			break;
			case 8:

				if ($choice['color'] != 'blue') return false;

				$blue = $choice['position'];

			break;
			case 13:

				if (!in_array($choice['color'], ['blue', 'yellow', 'green'])) return false;

				if ($choice['color'] === 'green') {

					$value['green'] = 6;
					break;
				}

				$newBonusID = $choice['color'] === 'blue' ? 8 : 2;

				return $this->bonusChoice($newBonusID, $choice);

			break;
			case 14:

				if (!in_array($choice['color'], ['orange', 'purple'])) return false;

				$value[$choice['color']] = 6;

			break;
		}

		return $this->choice($value, $choice, $blue);
	}


	/**
	 * Handles an incomming +1 choice.
	 * 
	 * @param Clever\Library\Game\Gameplay\DiceSet $diceSet
	 * @param array $choice
	 * 
	 * @return mixed
	 */
	public function plusOneChoice(DiceSet $diceSet, $choice)
	{
		return $this->choice([$diceSet, 'all'], $choice, $diceSet->blue());
	}
}