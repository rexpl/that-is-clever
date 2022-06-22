<?php

class Friend extends Clever {

	private $id_game;

	private $id;

	/**
	 * Is false when not al players are connected yet
	 */
	private $activeGame = false;

	/**
	 * Number of users
	 */
	private $nPlayers;

	/**
	 * User data
	 */
	private $user;
	private $connections = array();
	private $turnUsers = array();

	/**
	 * Contains the user board or empty/raw board on begining
	 */
	private $board;
	private $points = array();

	/**
	 * Contains the game log and if oupoutCLI is true wil log everything in cli
	 * This will allow to view the entire log if user reported an issue in game
	 */
	private $outpoutCLI;
	private $log;

	/**
	 * Each game consists of minimum 4 (depending of the number of players) rounds and 4 phases each round
	 */
	private $lastRound;
	private $round = 1;
	private $phase = 0;
	private $turn = 1;

	/**
	 * Saves dices values while user plays and allow us to verify his choice
	 */
	private $last_dice_trow = array();

	/**
	 * Saves blue/white values every time the dice is "rethrown", for the specif rules of blue
	 * Those variables allow access to blue/white no matter in wich array they are
	 */
	private $blue_white_value;
	private $last_white_value;
	private $last_blue_value;

	/**
	 * Saves dices wich have a lower value than the played dice for the last phase of each round
	 * This variable is reset on every round
	 */
	private $side_dices = array();

	/**
	 * In case user use a +1, he should also be able to use the previously used dices
	 * This variable is reset on every round
	 */
	private $used_dices = array();
	
	/**
	 * Saves the dices value before dice() start to manipulate all values
	 * This is necessary for the last phase of each round as we want the previous values
	 * This variable is reset on each call of dice() where $color != null
	 */
	private $dice_last_phase = array();

	/**
	 * Stops the game process
	 * Contains the bonus wich needs choice
	 * What to do after the bonus
	 */
	private $isActiveBonus = array();
	private $active_bonus_id = array();
	private $actionAfterBonus = array();

	/**
	 * Message to be sent
	 */
	private $message = array();

	/**
	 * true if it the last phase
	 * needed to know if user is allowed to use the bonus +1 or replay
	 */
	private $isLastPhase = false;

	/**
	 * true if user chose plus one
	 */
	private $isActivePlusOne = array();

	/**
	 * count the loop, if no possiblities to avoid crash
	 */
	private $countNoPossibilities;

	/**
	 * contains the id of each user who already has made there choise for the last phase
	 */
	private $userPlayedLastPhase = array();

	public function __construct($id_game, $nPlayers, bool $cli_log = false) {

		$this->id_game = $id_game;
		$this->nPlayers = $nPlayers;
		$this->outpoutCLI = $cli_log;

		switch ($nPlayers) {
			case 2:
				$this->lastRound = 7;
			break;
			case 3:
				$this->lastRound = 6;
			break;
			case 4:
				$this->lastRound = 5;
			break;
			default:
				throw new Exception("Unsupported number of players.");
			break;
		}
	}

	public function addUser($connection, $user, $username) {

		if ($this->activeGame) return false;

		$this->user[$connection] = array(
			'id_user' => $user,
			'username' => $username
		);

		$userCount = count($this->user);

		$this->connections[] = $connection;
		$this->turnUsers[$userCount] = $connection;

		$this->board[$connection] = self::RAW_BOARD;
		$this->points[$connection] = 0;

		$this->isActivePlusOne[$connection] = false;
		$this->isActiveBonus[$connection] = false;
		$this->active_bonus_id[$connection] = array();
		$this->actionAfterBonus[$connection] = 0;

		$this->consoleLog('New player('.$user.') on connection '.$connection.'.', __FILE__, __LINE__);

		if ($userCount == $this->nPlayers) {

			$this->consoleLog('Starting game.', __FILE__, __LINE__);
			$this->activeGame = true;
			$this->turn = 1;
		}

		return true;
	}

	public function consoleLog($text, $file, $line) {

		$this->log .= date("[Y-m-d H:i:s]") . "\t[" . $this->round . "." . $this->turn . "." . $this->phase . "]\t[" . $file . ":" . $line . "]\t".$text.PHP_EOL;

		if ($this->outpoutCLI) echo date("[Y-m-d H:i:s]") . "\t[" . $this->round . "." . $this->turn . "." . $this->phase . "]\t[" . $file . ":" . $line . "]\t".$text.PHP_EOL;
	}

	public function saveGameLog($location) {
	
		file_put_contents($location.$this->id_game.".log.gz", gzencode($this->log, 9));

	}

	private function prepareSend(array $body, $to = null, $except = null) {

		$toSend = array();

		if (is_null($to) && !is_null($except)) {

			$toSend = $this->connections;

			unset($toSend[array_search($except, $toSend)]);

		}
		elseif (!is_null($to) && is_null($except)) {

			$toSend[] = $to;
			
		}
		else {
			$toSend = $this->connections;
		}

		foreach ($toSend as $value) {

			$this->message[$value]['id_connection'] = $value;
			
			foreach ($body as $key => $valeur) {
				$this->message[$value]['body'][$key] = $valeur;
			}
		}
	}

	private function dice($color=null) {

		//reset requested (new round)
		if (is_null($color)) {
			
			$this->last_dice_trow = array(
				'blue' => random_int(1, 6),
				'yellow' => random_int(1, 6),
				'green' => random_int(1, 6),
				'orange' => random_int(1, 6),
				'purple' => random_int(1, 6),
				'white' => random_int(1, 6)
			);

			$this->last_white_value = $this->last_dice_trow['white'];
			$this->last_blue_value = $this->last_dice_trow['blue'];

			$this->blue_white_value = $this->last_white_value + $this->last_blue_value;

			$this->side_dices = array();
			$this->used_dices = array();

			return;
		}

		/**
		 * set random value for each left dice and save to dice array
		 * remove every dice not requested from dice array and sets them aside
		 */

		$this->used_dices[$color] = $this->last_dice_trow[$color];
		$value_dice = $this->last_dice_trow[$color];
		unset($this->last_dice_trow[$color]);
		$this->dice_last_phase = $this->last_dice_trow;

		foreach ($this->last_dice_trow as $key => $value) {
			
			if ($value < $value_dice) {
				
				$this->side_dices[$key] = $value;

				unset($this->last_dice_trow[$key]);
				unset($this->dice_last_phase[$key]);

				continue;

			}

			$this->last_dice_trow[$key] = random_int(1, 6);

			if ($key === 'white') $this->last_white_value = $this->last_dice_trow['white'];
			if ($key === 'blue') $this->last_blue_value = $this->last_dice_trow['blue'];

		}


		$this->blue_white_value = $this->last_white_value + $this->last_blue_value;
	}

	private function diceCorrection() {

		$blue = (isset($this->side_dices['blue'])) ? $this->side_dices['blue'] : $this->used_dices['blue'];
		$white = (isset($this->side_dices['white'])) ? $this->side_dices['white'] : $this->used_dices['white'];

		$this->blue_white_value = $blue + $white;
	}

	private function firstPhase() {
		
		if ($this->round == $this->lastRound) return $this->lastRound();

		$this->dice();
		$this->phase = 1;

		//inform users who is playing
		$this->prepareSend(['player' => $this->user[$this->id]['username']], null, $this->id);
		$this->prepareSend(['player' => true], $this->id);

		$this->consoleLog('Starting new turn.', __FILE__, __LINE__);

		if ($this->round <= 4) {
			
			switch ($this->round) {
				case 1:
				case 3:
					$bonus_id = array(4);
					$this->board[$this->id]['re']++;
					$this->consoleLog('Bonus 4 detected.', __FILE__, __LINE__);
				break;
				case 2:
					$bonus_id = array(7);
					$this->board[$this->id]['p1']++;
					$this->consoleLog('Bonus 7 detected.', __FILE__, __LINE__);
				break;
				case 4:
					$bonus_id = array(13, 14);
					$this->active_bonus_id[$this->id] = $bonus_id;
					$this->isActiveBonus[$this->id] = true;
					$this->actionAfterBonus[$this->id] = 2;
					$this->consoleLog('Bonus 13 detected.', __FILE__, __LINE__);
					$this->consoleLog('Bonus 14 detected.', __FILE__, __LINE__);
				break;
			}

			$this->prepareSend(['board' => $this->board[$this->id],'bonus' => $bonus_id], $this->id);

			if ($this->isActiveBonus[$this->id]) return $this->message;
		}

		$this->consoleLog('Sending board and dices: ' . json_encode($this->last_dice_trow), __FILE__, __LINE__);

		if (!self::possibilityWithAll($this->board[$this->id], $this->last_dice_trow, $this->blue_white_value)) return $this->escape();

		$this->prepareSend(['last_phase' => false, 'board' => $this->board[$this->id], 'dice' => $this->last_dice_trow]);

		return $this->message;
	}

	private function gameplay($choice) {

		$result = self::verifyGameplay($this->board[$this->id], $this->last_dice_trow, $choice, $this->blue_white_value);

		if (!$result) {

			$this->consoleLog('User chose dice '.$choice['dice'].'('.$this->last_dice_trow[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice denied.', __FILE__, __LINE__);

			return array($this->message[$this->id]);

		}

		$this->message = array();

		$this->consoleLog('User chose dice '.$choice['dice'].'('.$this->last_dice_trow[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice accepted.', __FILE__, __LINE__);


		$this->board[$this->id] = $result['board'];

		$this->dice($choice['dice']);

		$this->prepareSend(['used_dices' => $this->used_dices, 'board' => $this->board[$this->id]]);

		if ($result['isBonus']) {

			$this->prepareSend(['bonus' => $result['idBonus']], $this->id);

			foreach ($result['idBonus'] as $value) {

				$this->consoleLog('Bonus ' . $value . ' detected.', __FILE__, __LINE__);

				if ($value == 2 || $value == 8) {
					$this->active_bonus_id[$this->id][] = $value;
					$this->isActiveBonus[$this->id] = true;
					$this->actionAfterBonus[$this->id] = (++$this->phase == 4 || empty($this->last_dice_trow)) ? 4 : 3;

					return $this->message;
				}
			}
		}

		if (++$this->phase == 4 || empty($this->last_dice_trow)) return $this->prepareLastPhase();

		$this->consoleLog('Sending board and dices: ' . json_encode($this->last_dice_trow), __FILE__, __LINE__);

		if (!self::possibilityWithAll($this->board[$this->id], $this->last_dice_trow, $this->blue_white_value)) return $this->escape();

		$this->prepareSend(['last_phase' => false, 'dice' => $this->last_dice_trow]);

		return $this->message;
	}

	private function prepareLastPhase() {

		$this->consoleLog('Preparing last phase.', __FILE__, __LINE__);

		$this->userPlayedLastPhase = array();

		$this->isLastPhase = true;
		$this->phase = 4;

		$this->side_dices = $this->side_dices + $this->dice_last_phase;

		$this->diceCorrection();

		$this->consoleLog('Sending board and dices: ' . json_encode($this->side_dices), __FILE__, __LINE__);

		foreach ($this->connections as $value) {

			if ($value == $this->id) {

				$this->userPlayedLastPhase[] = $this->id;
				$this->prepareSend(['last_phase' => true, 'board' => $this->board[$value]], $value);
			}
			else {

				$this->prepareSend(['last_phase' => true, 'dice' => $this->side_dices, 'board' => $this->board[$value]], $value);
			
				if (!self::possibilityWithAll($this->board[$value], $this->side_dices, $this->blue_white_value)) {

					$this->userPlayedLastPhase[] = $value;
				}
			}
		}

		if (count($this->userPlayedLastPhase) == $this->nPlayers) return $this->escape();

		return $this->message;
	}

	private function lastPhase($choice) {

		//user already made his choice
		if (in_array($this->id, $this->userPlayedLastPhase)) return false;

		$result = self::verifyGameplay($this->board[$this->id], $this->side_dices, $choice, $this->blue_white_value);

		if (!$result) {

			var_dump($this->blue_white_value);

			$this->consoleLog('User chose dice '.$choice['dice'].'('.$this->side_dices[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice denied.', __FILE__, __LINE__);

			return array($this->message[$this->id]);

		}

		$this->userPlayedLastPhase[] = $this->id;

		$this->consoleLog('User chose dice '.$choice['dice'].'('.$this->side_dices[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice accepted.', __FILE__, __LINE__);

		$this->board[$this->id] = $result['board'];

		unset($this->message[$this->id]);
		$this->prepareSend(['board' => $this->board[$this->id]], $this->id);

		if ($result['isBonus']) { 

			$this->prepareSend(['bonus' => $result['idBonus']], $this->id);

			foreach ($result['idBonus'] as $value) {

				$this->consoleLog('Bonus ' . $value . ' detected.', __FILE__, __LINE__);

				if ($value == 2 || $value == 8) {
					$this->active_bonus_id[$this->id][] = $value;
					$this->isActiveBonus[$this->id] = true;
					$this->actionAfterBonus[$this->id] = 1;
					return array($this->message[$this->id]);
				}
			}

		}

		//exit if all users haven't made there choice
		if (count($this->userPlayedLastPhase) != $this->nPlayers) return array($this->message[$this->id]);

		$this->isLastPhase = false;
		$this->message = array();

		if (++$this->turn > $this->nPlayers) {

			$this->consoleLog('Starting new round.', __FILE__, __LINE__);

			$this->turn = 1;
			$this->round++;

			$this->prepareSend(['round' => $this->round]);
		}

		$this->id = $this->turnUsers[$this->turn];

		return $this->firstPhase();
	}

	private function activeBonus($message) {

		if (!array_key_exists('bonus_choice', $message)) return array($this->message[$this->id]);

		$choice = $message['bonus_choice'];

		if (!in_array($choice['id'], $this->active_bonus_id[$this->id]) || !isset($choice['position']) || !isset($choice['color'])) return array($this->message[$this->id]);

		$result = self::verifyBonus($this->board[$this->id], $choice['color'], $choice['position'], $choice['id']);

		if (!$result) {
			
			$this->consoleLog('User chose bonus '.$choice['id'].' on '.$choice['color'].'('.$choice['position'].'). User choice denied.', __FILE__, __LINE__);
			return array($this->message[$this->id]);
		}

		$this->consoleLog('User chose bonus '.$choice['id'].' on '.$choice['color'].'('.$choice['position'].'). User choice accepted.', __FILE__, __LINE__);

		$this->board[$this->id] = $result['board'];

		$position_delete = array_search($choice['id'], $this->active_bonus_id[$this->id]);
		unset($this->active_bonus_id[$this->id][$position_delete]);

		if ($result['isBonus']) {

			$this->prepareSend(['bonus' => $result['idBonus']], $this->id);

			foreach ($result['idBonus'] as $value) {

				$this->consoleLog('Bonus ' . $value . ' detected.', __FILE__, __LINE__);

				if ($value == 2 || $value == 8) {
					$this->active_bonus_id[$this->id][] = $value;
				}
			}

		}

		if (!empty($this->active_bonus_id[$this->id])) {

			$send_bonus = ($result['isBonus']) ? $this->active_bonus_id[$this->id] + $result['idBonus'] : $this->active_bonus_id[$this->id];

			$this->prepareSend(['board' => $this->board[$this->id], 'bonus' => $send_bonus], $this->id);

			return array($this->message[$this->id]);
		}

		unset($this->message[$this->id]);
		$this->prepareSend(['board' => $this->board[$this->id]], $this->id);

		$this->isActiveBonus[$this->id] = false;

		switch ($this->actionAfterBonus[$this->id]) {
			case 1:				
				
				//exit if all users haven't made there choice
				if (count($this->userPlayedLastPhase) != $this->nPlayers) return array($this->message[$this->id]);

				$this->isLastPhase = false;
				$this->message = array();

				if (++$this->turn > $this->nPlayers) {

					$this->consoleLog('Starting new round.', __FILE__, __LINE__);

					$this->turn = 1;
					$this->id = $this->turnUsers[1];
					$this->round++;

					$this->prepareSend(['round' => $this->round]);
				}

				return $this->firstPhase();

			break;
			case 4:
				return $this->prepareLastPhase();
			break;
			case 5:

				if ($this->id == $this->turnUsers[$this->turn]) {
					$this->prepareSend(['board' => $this->board[$this->id]], $this->id);
				}
				else {
					$this->prepareSend(['board' => $this->board[$this->id], 'dice' => $this->side_dices], $this->id);
				}

				return array($this->message[$this->id]);

			break;
			default:

				$this->consoleLog('Sending board and dices: ' . json_encode($this->last_dice_trow), __FILE__, __LINE__);

				if (!self::possibilityWithAll($this->board[$this->id], $this->last_dice_trow, $this->blue_white_value)) return $this->escape();

				$this->prepareSend(['last_phase' => false, 'replay' => true, 'plus' => false, 'board' => $this->board[$this->id], 'dice' => $this->last_dice_trow]);

				return $this->message;
			break;
		}
	}

	private function escape() {

		$this->consoleLog('No possibilities were detected. Sending canceled.', __FILE__, __LINE__);

		$this->message = array();

		if (++$this->countNoPossibilities > 10) {

			$this->consoleLog('Not enough combinations left. Game terminated.', __FILE__, __LINE__);
			$this->round = $this->lastRound;

			return $this->lastRound();
		}

		if ($this->phase == 4) {

			$this->isLastPhase = false;

			if (++$this->turn > $this->nPlayers) {

				$this->consoleLog('Starting new round.', __FILE__, __LINE__);

				$this->turn = 1;
				$this->id = $this->turnUsers[1];
				$this->round++;

				$this->prepareSend(['round' => $this->round]);
			}

			return $this->firstPhase();
		}

		$this->dice_last_phase = $this->last_dice_trow;

		return $this->prepareLastPhase();
	}

	private function replay() {

		if ($this->isLastPhase || $this->isActiveBonus[$this->id] || $this->board[$this->id]['re'] <= 0 || $this->id != $this->turnUsers[$this->turn]) {

			$this->consoleLog('User requested a replay. User choice denied.', __FILE__, __LINE__);
			return array($this->message[$this->id]);
		}

		$this->board[$this->id]['re']--;

		$this->consoleLog('User requested a replay. User choice accepted.', __FILE__, __LINE__);

		$this->message = array();

		foreach ($this->last_dice_trow as $key => $value) {
			$this->last_dice_trow[$key] = random_int(1, 6);
		}

		$this->last_white_value = (isset($this->last_dice_trow['white'])) ? $this->last_dice_trow['white'] : $this->last_white_value;
		$this->last_blue_value = (isset($this->last_dice_trow['blue'])) ? $this->last_dice_trow['blue'] : $this->last_blue_value;

		$this->blue_white_value = $this->last_white_value + $this->last_blue_value;

		$this->prepareSend(['board' => $this->board[$this->id], 'dice' => $this->last_dice_trow]);

		return $this->message;
	}

	private function activatePlusOne() {

		if (!$this->isLastPhase || $this->isActiveBonus[$this->id] || $this->board[$this->id]['p1'] <= 0 || $this->isActivePlusOne[$this->id]) {
			
			$this->consoleLog('User requested a +1. User choice denied.', __FILE__, __LINE__);
			return array($this->message[$this->id]);
		}

		$this->board[$this->id]['p1']--;

		$this->consoleLog('User requested a +1. User choice accepted.', __FILE__, __LINE__);

		unset($this->message[$this->id]);
		$this->isActivePlusOne[$this->id] = true;

		$this->prepareSend(['dice' => $this->side_dices + $this->used_dices], $this->id);

		return array($this->message[$this->id]);
	}

	private function activePlusOne($message) {

		if (!array_key_exists('p1_choice', $message)) return array($this->message[$this->id]);

		$choice = $message['p1_choice'];
		$tmp_dices = $this->side_dices + $this->used_dices;

		if (!isset($choice['dice']) || !isset($choice['position']) || !isset($choice['color'])) return array($this->message[$this->id]);

		$result = self::verifyGameplay($this->board[$this->id], $tmp_dices, $choice, $this->blue_white_value);

		if (!$result) {
			
			$this->consoleLog('User chose dice '.$choice['dice'].'('.$tmp_dices[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice denied.', __FILE__, __LINE__);
			return array($this->message[$this->id]);
		}

		$this->consoleLog('User chose dice '.$choice['dice'].'('.$tmp_dices[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice accepted.', __FILE__, __LINE__);

		$this->board[$this->id] = $result['board'];
		unset($this->message[$this->id]);
		$this->isActivePlusOne[$this->id] = false;

		if ($result['isBonus']) { 

			$this->prepareSend(['board' => $this->board[$this->id], 'bonus' => $result['idBonus']], $this->id);

			foreach ($result['idBonus'] as $value) {

				$this->consoleLog('Bonus ' . $value . ' detected.', __FILE__, __LINE__);

				if ($value == 2 || $value == 8) {
					$this->active_bonus_id[$this->id][] = $value;
					$this->isActiveBonus[$this->id] = true;
					$this->actionAfterBonus[$this->id] = 5;

					return array($this->message[$this->id]);
				}
			}

		}

		if ($this->id == $this->turnUsers[$this->turn]) {
			$this->prepareSend(['board' => $this->board[$this->id]], $this->id);
		}
		else {
			$this->prepareSend(['board' => $this->board[$this->id], 'dice' => $this->side_dices], $this->id);
		}

		return array($this->message[$this->id]);
	}

	private function newMessage($message, $idSender) {

		$send = array();

		if (!in_array($idSender, $this->connections)) return false;

		foreach ($this->connections as $value) {
			
			if ($value == $idSender) continue;

			$send[] = array(
				'id_connection' => $value,
				'body' => array(
					'message' => array(
						'sender' => $this->user[$idSender]['username'],
						'text' => htmlentities($message, ENT_QUOTES)
					)
				)
			);
		}

		return $send;
	}

	private function lastRound() {

		$this->message = array();

		$this->consoleLog('Game is finished.', __FILE__, __LINE__);

		foreach ($this->connections as $value) {

			$this->points[$value] = self::countPoints($this->board[$value]);

			$this->prepareSend(['board' => $this->board[$value], 'finish' => $this->points[$value]], $value);
		}

		return $this->message;
	}

	public function response($message = null, $connection, $first_round = false) {

		if (isset($message['message'])) return $this->newMessage($message['message'], $connection);

		if (!$this->activeGame || !in_array($connection, $this->connections)) return false;

		$this->id = $connection;
		$this->countNoPossibilities = 0;

		if ($first_round) {

			$this->consoleLog('Starting new round.', __FILE__, __LINE__);

			$this->prepareSend(['round' => 1]);

			$this->id = $this->turnUsers[1];
			return $this->firstPhase();
		}

		if ($this->isActiveBonus[$this->id]) return $this->activeBonus($message);
		if ($this->isActivePlusOne[$this->id]) return $this->activePlusOne($message);
		if ($this->round == $this->lastRound) return $this->lastRound();

		if (isset($message['bonus'])) {
			switch ($message['bonus']) {
				case 'replay':
					return $this->replay();
				break;
				case 'plus1':
					return $this->activatePlusOne();
				break;	
			}
		}

		if (isset($message['choice']) && $this->phase == 4) {

			$choice = $message['choice'];

			if (!isset($choice['dice']) || !isset($choice['color']) || !isset($choice['position'])) return array($this->message[$this->id]);

			return $this->lastPhase($choice);

		}

		if ($this->id != $this->turnUsers[$this->turn]) return array($this->message[$this->id]);

		if (isset($message['choice'])) {

			$choice = $message['choice'];

			if (!isset($choice['dice']) || !isset($choice['color']) || !isset($choice['position'])) return array($this->message[$this->id]);

			return $this->gameplay($choice);

		}

	}

	public function close($connection) {

		if (!in_array($connection, $this->connections)) return false;

		if ($this->points[$connection] != 0) {

			return array(
				'boards' => $this->board,
				'users' => $this->user,
				'points' => $this->points
			);
		}

		$this->message = array();

		$this->prepareSend(['exit' => $this->user[$connection]['username']]);

		return array(
			'points' => 0,
			'users' => $this->user,
			'send' => $this->message
		);
	}
}