<?php

class Solo extends Clever {

	private $id_game;
	private $id_connection;
	private $id_user;

	/**
	 * Contains the game log and if oupoutCLI is true wil log everything in cli
	 * This will allow to view the entire log if user reported an issue in game
	 */
	private $outpoutCLI;
	private $log;

	/**
	 * Contains the user board or empty/raw board on begining
	 */
	private $board;
	private $points = 0;

	/**
	 * Each solo game consists of 6 rounds and 4 phases each round
	 */
	private $round = 1;
	private $phase = 0;

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
	private $isActiveBonus = false;
	private $active_bonus_id = array();
	private $actionAfterBonus;

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
	private $isActivePlusOne = false;

	/**
	 * count the loop, if no possiblities to avoid crash
	 */
	private $countNoPossibilities;


	public function __construct($id_game, $id_connection, $id_user, bool $cli_log = false) {

		$this->id_game = $id_game;
		$this->id_connection = $id_connection;
		$this->id_user = $id_user;
		$this->outpoutCLI = $cli_log;

		$this->board = self::RAW_BOARD;
	}

	public function consoleLog($text, $file, $line) {

		$this->log .= date("[Y-m-d H:i:s]") . "\t[" . $this->round . "." . $this->phase . "]\t[" . $file . ":" . $line . "]\t".$text.PHP_EOL;

		if ($this->outpoutCLI) echo date("[Y-m-d H:i:s]") . "\t[" . $this->round . "." . $this->phase . "]\t[" . $file . ":" . $line . "]\t".$text.PHP_EOL;
	}

	public function saveGameLog($location) {

		file_put_contents($location.$this->id_game.".log.gz", gzencode($this->log, 9));

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

	private function first_phase() {

		if ($this->round == 7) return $this->last_round();

		$this->dice();
		$this->phase = 1;

		$this->consoleLog('Starting new round.', __FILE__, __LINE__);

		if ($this->round <= 4) {
			
			switch ($this->round) {
				case 1:
				case 3:
					$bonus_id = array(4);
					$this->board['re']++;
					$this->consoleLog('Bonus 4 detected.', __FILE__, __LINE__);
				break;
				case 2:
					$bonus_id = array(7);
					$this->board['p1']++;
					$this->consoleLog('Bonus 7 detected.', __FILE__, __LINE__);
				break;
				case 4:
					$bonus_id = array(13, 14);
					$this->active_bonus_id = $bonus_id;
					$this->isActiveBonus = true;
					$this->actionAfterBonus = 2;
					$this->consoleLog('Bonus 13 detected.', __FILE__, __LINE__);
					$this->consoleLog('Bonus 14 detected.', __FILE__, __LINE__);
				break;
			}

			$this->message[] = array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'board' => $this->board,
					'bonus' => $bonus_id
				)
			);

			if ($this->isActiveBonus) return $this->message;
		}

		$this->message[] = array(
			'id_connection' => $this->id_connection,
			'body' => array(
				'round' => $this->round
			)
		);

		$this->consoleLog('Sending board and dices: ' . json_encode($this->last_dice_trow), __FILE__, __LINE__);

		if (!self::possibilityWithAll($this->board, $this->last_dice_trow, $this->blue_white_value)) return $this->escape();

		if (!$this->isActiveBonus) {
			$this->message[] = array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'last_phase' => false,
					'replay' => true,
					'plus' => false,
					'board' => $this->board,
					'dice' => $this->last_dice_trow
				)
			);
		}

		return $this->message;

	}

	private function gameplay($choice) {

		$result = self::verifyGameplay($this->board, $this->last_dice_trow, $choice, $this->blue_white_value);

		if (!$result) {

			$this->consoleLog('User chose dice '.$choice['dice'].'('.$this->last_dice_trow[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice denied.', __FILE__, __LINE__);

			return $this->message;

		}

		$this->message = array();

		$this->consoleLog('User chose dice '.$choice['dice'].'('.$this->last_dice_trow[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice accepted.', __FILE__, __LINE__);


		$this->board = $result['board'];

		$this->dice($choice['dice']);

		$this->message[] = array(
			'id_connection' => $this->id_connection,
			'body' => array(
				'used_dices' => $this->used_dices
			)
		);

		if ($result['isBonus']) {

			$this->message[] = array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'board' => $this->board,
					'bonus' => $result['idBonus']
				)
			);

			foreach ($result['idBonus'] as $value) {

				$this->consoleLog('Bonus ' . $value . ' detected.', __FILE__, __LINE__);

				if ($value == 2 || $value == 8) {
					$this->active_bonus_id[] = $value;
					$this->isActiveBonus = true;
					$this->actionAfterBonus = (++$this->phase == 4 || empty($this->last_dice_trow)) ? 4 : 3;

					return $this->message;
				}
			}

		}

		if (++$this->phase == 4 || empty($this->last_dice_trow)) return $this->prepare_last_phase();

		$this->consoleLog('Sending board and dices: ' . json_encode($this->last_dice_trow), __FILE__, __LINE__);

		if (!self::possibilityWithAll($this->board, $this->last_dice_trow, $this->blue_white_value)) return $this->escape();

		if (!$this->isActiveBonus) {

			$this->message[] = array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'last_phase' => false,
					'replay' => true,
					'plus' => false,
					'dice' => $this->last_dice_trow,
					'board' => $this->board
				)
			);
		}

		return $this->message;
	}

	private function prepare_last_phase() {

		$this->consoleLog('Preparing last phase.', __FILE__, __LINE__);

		$this->isLastPhase = true;
		$this->phase = 4;

		$this->side_dices = $this->side_dices + $this->dice_last_phase;

		$this->diceCorrection();

		$this->consoleLog('Sending board and dices: ' . json_encode($this->side_dices), __FILE__, __LINE__);

		if (!self::possibilityWithAll($this->board, $this->side_dices, $this->blue_white_value)) return $this->escape();

		$this->message[] = array(
			'id_connection' => $this->id_connection,
			'body' => array(
				'last_phase' => true,	
				'replay' => false,
				'plus' => true,
				'board' => $this->board,
				'dice' => $this->side_dices
			)
		);

		return $this->message;
	}

	private function last_phase($choice) {

		$result = self::verifyGameplay($this->board, $this->side_dices, $choice, $this->blue_white_value);

		if (!$result) {

			$this->consoleLog('User chose dice '.$choice['dice'].'('.$this->side_dices[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice denied.', __FILE__, __LINE__);

			return $this->message;

		}

		$this->message = array();

		$this->consoleLog('User chose dice '.$choice['dice'].'('.$this->side_dices[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice accepted.', __FILE__, __LINE__);

		$this->board = $result['board'];

		$this->round++;

		$this->isLastPhase = false;

		if ($result['isBonus']) { 

			$this->message[] = array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'board' => $this->board,
					'bonus' => $result['idBonus']
				)
			);

			foreach ($result['idBonus'] as $value) {

				$this->consoleLog('Bonus ' . $value . ' detected.', __FILE__, __LINE__);

				if ($value == 2 || $value == 8) {
					$this->active_bonus_id[] = $value;
					$this->isActiveBonus = true;
					$this->actionAfterBonus = 1;
					return $this->message;
				}
			}

		}

		if ($this->round == 7) return $this->last_round();

		return $this->first_phase();
	}

	private function activeBonus($message) {

		if (!array_key_exists('bonus_choice', $message)) return $this->message;

		$choice = $message['bonus_choice'];

		if (!in_array($choice['id'], $this->active_bonus_id) || !isset($choice['position']) || !isset($choice['color'])) return $this->message;

		$result = self::verifyBonus($this->board, $choice['color'], $choice['position'], $choice['id']);

		if (!$result) {
			
			$this->consoleLog('User chose bonus '.$choice['id'].' on '.$choice['color'].'('.$choice['position'].'). User choice denied.', __FILE__, __LINE__);
			return $this->message;
		}

		$this->consoleLog('User chose bonus '.$choice['id'].' on '.$choice['color'].'('.$choice['position'].'). User choice accepted.', __FILE__, __LINE__);

		$this->board = $result['board'];
		$this->message = array();

		$position_delete = array_search($choice['id'], $this->active_bonus_id);
		unset($this->active_bonus_id[$position_delete]);

		if ($result['isBonus']) {

			$this->message[] = array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'bonus' => $result['idBonus']
				)
			);

			foreach ($result['idBonus'] as $value) {

				$this->consoleLog('Bonus ' . $value . ' detected.', __FILE__, __LINE__);

				if ($value == 2 || $value == 8) {
					$this->active_bonus_id[] = $value;
				}
			}

		}

		if (!empty($this->active_bonus_id)) {

			$send_bonus = ($result['isBonus']) ? $this->active_bonus_id + $result['idBonus'] : $this->active_bonus_id;

			$this->message[] = array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'board' => $this->board,
					'bonus' => $send_bonus
				)
			);

			return $this->message;
		}

		$this->isActiveBonus = false;

		switch ($this->actionAfterBonus) {
			case 1:				
				return $this->first_phase();
			break;
			case 4:
				return $this->prepare_last_phase();
			break;
			default:

				if ($this->actionAfterBonus == 2) {
					$this->message[] = array(
						'id_connection' => $this->id_connection,
						'body' => array(
							'round' => $this->round
						)
					);
				}

				$this->consoleLog('Sending board and dices: ' . json_encode($this->last_dice_trow), __FILE__, __LINE__);

				if (!self::possibilityWithAll($this->board, $this->last_dice_trow, $this->blue_white_value)) return $this->escape();

				$this->message[] = array(
					'id_connection' => $this->id_connection,
					'body' => array(
						'last_phase' => false,
						'replay' => true,
						'plus' => false,
						'board' => $this->board,
						'dice' => $this->last_dice_trow
					)
				);

				return $this->message;
			break;
		}
	}

	private function escape() {

		$this->consoleLog('No possibilities were detected. Sending canceled.', __FILE__, __LINE__);

		if (++$this->countNoPossibilities > 10) {

			$this->consoleLog('Not enough combinations left. Game terminated.', __FILE__, __LINE__);
			$this->round = 7;

			return $this->last_round();
		}

		if ($this->phase == 4) {

			$this->round++;

			return $this->first_phase();
		}

		$this->dice_last_phase = $this->last_dice_trow;

		return $this->prepare_last_phase();
	}

	private function replay() {

		if ($this->isLastPhase || $this->isActiveBonus || $this->board['re'] <= 0) {

			$this->consoleLog('User requested a replay. User choice denied.', __FILE__, __LINE__);
			return $this->message;
		}

		$this->board['re']--;

		$this->consoleLog('User requested a replay. User choice accepted.', __FILE__, __LINE__);

		$this->message = array();

		foreach ($this->last_dice_trow as $key => $value) {
			$this->last_dice_trow[$key] = random_int(1, 6);
		}

		$this->last_white_value = (isset($this->last_dice_trow['white'])) ? $this->last_dice_trow['white'] : $this->last_white_value;
		$this->last_blue_value = (isset($this->last_dice_trow['blue'])) ? $this->last_dice_trow['blue'] : $this->last_blue_value;

		$this->blue_white_value = $this->last_white_value + $this->last_blue_value;

		$this->message[] = array(
			'id_connection' => $this->id_connection,
			'body' => array(
				'last_phase' => false,
				'replay' => true,
				'plus' => false,
				'board' => $this->board,
				'dice' => $this->last_dice_trow
			)
		);

		return $this->message;
	}

	private function activatePlusOne() {

		if (!$this->isLastPhase || $this->isActiveBonus || $this->board['p1'] <= 0) {
			
			$this->consoleLog('User requested a +1. User choice denied.', __FILE__, __LINE__);
			return $this->message;
		}

		$this->board['p1']--;

		$this->consoleLog('User requested a +1. User choice accepted.', __FILE__, __LINE__);

		$this->message = array();
		$this->isActivePlusOne = true;

		$this->message[] = array(
			'id_connection' => $this->id_connection,
			'body' => array(
				'last_phase' => true,	
				'replay' => false,
				'plus' => false,
				'board' => $this->board,
				'dice' => $this->side_dices + $this->used_dices
			)
		);

		return $this->message;
	}

	private function activePlusOne($message) {

		if (!array_key_exists('p1_choice', $message)) return $this->message;

		$choice = $message['p1_choice'];
		$tmp_dices = $this->side_dices + $this->used_dices;

		if (!isset($choice['dice']) || !isset($choice['position']) || !isset($choice['color'])) return $this->message;

		$result = self::verifyGameplay($this->board, $tmp_dices, $choice, $this->blue_white_value);

		if (!$result) {
			
			$this->consoleLog('User chose dice '.$choice['dice'].'('.$tmp_dices[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice denied.', __FILE__, __LINE__);
			return $this->message;
		}

		$this->consoleLog('User chose dice '.$choice['dice'].'('.$tmp_dices[$choice['dice']].') on '.$choice['color'].'('.$choice['position'].'). User choice accepted.', __FILE__, __LINE__);

		$this->board = $result['board'];
		$this->message = array();
		$this->isActivePlusOne = false;

		if ($result['isBonus']) { 

			$this->message[] = array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'board' => $this->board,
					'bonus' => $result['idBonus']
				)
			);

			foreach ($result['idBonus'] as $value) {

				$this->consoleLog('Bonus ' . $value . ' detected.', __FILE__, __LINE__);

				if ($value == 2 || $value == 8) {
					$this->active_bonus_id[] = $value;
					$this->isActiveBonus = true;
					$this->actionAfterBonus = 4;

					return $this->message;
				}
			}

		}

		return $this->prepare_last_phase();
	}

	private function last_round() {

		$this->consoleLog('Game is finished.', __FILE__, __LINE__);

		$this->points = self::countPoints($this->board);

		$this->message = array(
			array(
				'id_connection' => $this->id_connection,
				'body' => array(
					'board' => $this->board,
					'finish' => $this->points
				)
			)
		);

		return $this->message;
	}

	public function response($message = null, $connection, $first_round = false) {

		$this->countNoPossibilities = 0;

		if ($first_round) return $this->first_phase();

		if ($this->isActiveBonus) return $this->activeBonus($message);
		if ($this->isActivePlusOne) return $this->activePlusOne($message);
		if ($this->round == 7) return $this->last_round();

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

		if (isset($message['choice'])) {

			$choice = $message['choice'];

			if (!isset($choice['dice']) || !isset($choice['color']) || !isset($choice['position'])) return $this->message;

			if ($this->phase == 4) return $this->last_phase($choice);

			return $this->gameplay($choice);

		}

		return $this->message;

	}

	public function close() {

		return array(
			'board' => $this->board,
			'id_user' => $this->id_user,
			'points' => $this->points
		);
	}

}