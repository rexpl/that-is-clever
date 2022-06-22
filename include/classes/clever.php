<?php

class Clever {

	public const RAW_BOARD = array('p1'=>0,'re'=>0,'fox'=>0,'blue'=>array(2=>false,3=>false,4=>false,5=>false,6=>false,7=>false,8=>false,9=>false,10=>false,11=>false,12=>false),'yellow'=>array(11=>false,12=>false,13=>false,14=>false,15=>false,16=>false,21=>false,22=>false,23=>false,24=>false,25=>false,26=>false),'green'=>0,'orange'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0),'orange_position'=>0,'purple'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0),'purple_position'=>0,'last_purple_value'=>0);

	private const COLOR_FUNCTION_POSSIBILITY = array('yellow'=>'possibilityWithYellow','green'=>'possibilityWithGreen','orange'=>'possibilityWithOrange','purple'=>'possibilityWithPurple');

	private const COLOR_FUNCTION_VERIFY = array('yellow'=>'verifyYellow','orange'=>'verifyOrange','purple'=>'verifyPurple','green'=>'verifyGreen');

	private const BONUS_BOARD_VALUE_NAME = array(1 => 5, 3 => 'fox', 4 => 're', 5 => 'green', 7 => 'p1', 9 => 4, 12 => 6);

	private const BONUS_NAME_COLOR = array(5=>'green', 6=>'purple', 1=>'orange', 9=>'orange', 12=>'orange');

	private const POSSIBLE_RECURSIVE_BONUS = array(1, 5, 6, 9, 12);

	private const BONUS_BLUE_YELLOW = array('blue'=>array(1=>array(2,3,4),2=>array(5,6,7,8),3=>array(9,10,11,12),4=>array(5,9),5=>array(2,6,10),6=>array(3,7,11),7=>array(4,8,12)),'yellow'=>array(8=>array(13,16,15),9=>array(12,11,25),5=>array(21,22,14),3=>array(23,24,26),7=>array(13,11,22,26)));

	private const BONUS_GREEN_ORANGE_PURPLE = array('green'=>array(4=>7,6=>8,7=>3,9=>6,10=>4),'purple'=>array(3=>4,4=>8,5=>7,6=>2,7=>3,8=>4,9=>5,10=>12,11=>7),'orange'=>array(3=>4,4=>10,5=>2,6=>7,7=>10,8=>3,9=>10,10=>6,11=>11));

	private const VALUE_CASES_BOARD = array('green'=>array(0=>0,1=>1,2=>3,3=>6,4=>10,5=>15,6=>21,7=>28,8=>36,9=>45,10=>55,11=>66),'blue'=>array(0=>0,1=>1,2=>2,3=>4,4=>7,5=>11,6=>16,7=>22,8=>29,9=>37,10=>46,11=>56));

	

	public static function Log($text, $file=__FILE__, $line=__LINE__) {

		echo date("[Y-m-d H:i:s]") . "\t[" . $file . ":" . $line . "]\t".$text.PHP_EOL;

	}


	/**
	 * possibility() returns true if there is an available postion with all dices
	 * possibility{Color}() returns true if there is an available postion with the given dice for this color
	 */
	
	private static function possibilityWithBlue(array $board, $whiteblue_value) {

		if ($board['blue'][$whiteblue_value]) return false;

		return true; 
	}

	private static function possibilityWithYellow(array $board, $dice_value) {

		if ($board['yellow']['1'.$dice_value]) return false;
		if ($board['yellow']['2'.$dice_value]) return false;

		return true;
	}

	private static function possibilityWithGreen(array $board, $dice_value) {

		$value_should_be = (++$board['green'] > 5) ? $board['green'] - 5 : $board['green'];

		if ($dice_value < $value_should_be) return false;

		return true;
	}

	private static function possibilityWithOrange(array $board, $dice_value) {

		if ($board['orange_position'] == 11 || is_null($dice_value)) return false;

		return true;
	}

	private static function possibilityWithPurple(array $board, $dice_value) {

		if ($board['purple_position'] == 11 || $board['last_purple_value'] >= $dice_value) return false;

		return true;
	}

	public static function possibilityWithAll(array $board, array $dice, $whiteblue_value) {

		//blue works slightly differently, so we do it beforehand and remove it from $dice
		if (isset($dice['blue']) || isset($dice['white'])) {

			if (self::possibilityWithBlue($board, $whiteblue_value)) return true;

			if (isset($dice['blue'])) unset($dice['blue']);
		}

		//white match with every color so we test it first and remove it from $dice
		if (isset($dice['white'])) {

			foreach (self::COLOR_FUNCTION_POSSIBILITY as $value) {
			
				if (self::$value($board, $dice['white'])) return true;

			}

			unset($dice['white']);
		}

		//if still no match we are gonna try all colors left
		foreach ($dice as $key => $value) {

			/**
			 * $string is only used to call the method, self::$color[$key](param) does not work.
			 */
			$string = self::COLOR_FUNCTION_POSSIBILITY[$key];
			
			if (self::$string($board, $value)) return true;

		}

		return false;
	}



	private static function applyBonus(array $board, array $bonus, $pre_bonus) {

		$possible_recursive = null;

		/**
		 * This function applies the requested bonus to the board
		 * Along the way we collect the position if we apply a new bonus (this is only for recursive bonuses).
		 */

		if (is_null($pre_bonus)) $pre_bonus = array();

		foreach ($bonus as $value) {

			array_push($pre_bonus, $value);

			switch ($value) {
				case 1:
				case 9:
				case 12:

					foreach ($board['orange'] as $clef => $valeur) {
						if ($valeur > 0) continue;

						$board['orange'][$clef] = self::BONUS_BOARD_VALUE_NAME[$value];
						$position_for_recursive = $clef;

						break;
					}

					$board['orange_position']++;

				break;
				case 6:

					foreach ($board['purple'] as $clef => $valeur) {
						if ($valeur > 0) continue;

						$board['purple'][$clef] = 6;
						$position_for_recursive = $clef;

						break;
					}

					$board['purple_position']++;
					$board['last_purple_value'] = 0;

				break;
				case 3:
				case 4:
				case 5:
				case 7:

					$board[self::BONUS_BOARD_VALUE_NAME[$value]]++;

					if ($value == 5) $position_for_recursive = $board['green'];

				break;
				case 10:

					foreach ($board['orange'] as $clef => $valeur) {

						if ($valeur > 0) continue;

						$board['orange'][$clef - 1] = $board['orange'][$clef - 1] * 2;
						break;
					}

				break;
				case 11:

					$board['orange'][11] = $board['orange'][11] * 3;

				break;
			}

			if (in_array($value, self::POSSIBLE_RECURSIVE_BONUS)) {

				$possible_recursive = $value;

			}
		}
		
		if (!is_null($possible_recursive)) {
		
			return self::bonusGreenOrangePurple($board, self::BONUS_NAME_COLOR[$possible_recursive], $position_for_recursive, $pre_bonus);
		}

		
		return array('board' => $board, 'isBonus' => true, 'idBonus' => $pre_bonus);
	}

	private static function bonusBlueYellow(array $board, $color, $new_position) {

		// color yellow -> $new_postion = $postion . $value
		// color yellow -> ex: yellow(1)(3) = 13

		$all_positions_used = array();
		$bonus = array();

		//we get all used positions
		foreach ($board[$color] as $key => $value) {
			
			if ($value) array_push($all_positions_used, $key);
			
		}

		// we see if row/column is now filled with new position
		foreach (self::BONUS_BLUE_YELLOW[$color] as $key => $value) {

			if (!in_array($new_position, $value)) continue;

			$i = 0;

			foreach ($value as $recompare) {

				if (in_array($recompare, $all_positions_used)) $i++;

			}

			if ($i == count($value)) {

				//user got a bonus
				array_push($bonus, $key);
			}
		}

		if (empty($bonus)) return array('board' => $board, 'isBonus' => false, 'idBonus' => array());

		return self::applyBonus($board, $bonus, null);
	}

	private static function bonusGreenOrangePurple(array $board, $color, $new_position, $pre_bonus = null) {

		if (array_key_exists($new_position, self::BONUS_GREEN_ORANGE_PURPLE[$color])) {

			return self::applyBonus($board, array(self::BONUS_GREEN_ORANGE_PURPLE[$color][$new_position]), $pre_bonus);
		}

		//no bonus detected we exit

		if (!empty($pre_bonus)) {
			return array('board' => $board, 'isBonus' => true, 'idBonus' => $pre_bonus);
		}
		else {
			return array('board' => $board, 'isBonus' => false, 'idBonus' => array());
		}
	}

	public static function verifyBonus(array $board, $color, $position, $bonus_id) {

		switch ($bonus_id) {
			case 2:

				if ($color != 'yellow' || $board['yellow'][$position]) return false;

				$board['yellow'][$position] = true;
				return self::bonusBlueYellow($board, 'yellow', $position);

			break;
			case 8:

				if ($color != 'blue' || $board['blue'][$position]) return false;

				$board['blue'][$position] = true;
				return self::bonusBlueYellow($board, 'blue', $position);

			break;
			case 13:

				if (!in_array($color, ['blue', 'yellow', 'green'])) return false;

				if ($color === 'green') {
					
					if (++$board['green'] > 11) return false;
					return self::bonusGreenOrangePurple($board, 'green', $position);
				}

				if ($board[$color][$position]) return false;

				$board[$color][$position] = true;
				return self::bonusBlueYellow($board, $color, $position);

			break;
			case 14:

				if (!in_array($color, ['orange', 'purple']) || ++$board[$color.'_position'] > 11) return false;

				$board[$color][$board[$color.'_position']] = 6;

				if ($color === 'purple') $board['last_purple_value'] = 0;

				return self::bonusGreenOrangePurple($board, $color, $position);

			break;
		}
	}



	private static function verifyBlue(array $board, $position, $whiteblue_value) {

		if ($board['blue'][$position] || $position != $whiteblue_value) return false;

		$board['blue'][$position] = true;

		return self::bonusBlueYellow($board, 'blue', $position);
	}

	private static function verifyYellow(array $board, $position, $dice_value) {

		if ($board['yellow'][$position] || !isset($board['yellow'][$position])) return false;

		$needed_dice_value = ($position > 20) ? $position - 20 : $position - 10;

		if ($needed_dice_value != $dice_value) return false;

		$board['yellow'][$position] = true;

		return self::bonusBlueYellow($board, 'yellow', $position);
	}

	private static function verifyGreen(array $board, $position, $dice_value) {

		$value_should_be = (++$board['green'] > 5) ? $board['green'] - 5 : $board['green'];

		if ($board['green'] > 11 || $dice_value < $value_should_be || $board['green'] != $position) return false;

		return self::bonusGreenOrangePurple($board, 'green', $position);
	}

	private static function verifyOrange(array $board, $position, $dice_value) {

		if ($board['orange_position'] == 11 || is_null($dice_value) || ++$board['orange_position'] != $position) return false;

		$board['orange'][$position] = $dice_value;

		return self::bonusGreenOrangePurple($board, 'orange', $position);
	}

	private static function verifyPurple(array $board, $position, $dice_value) {

		if ($board['purple_position'] == 11 || $board['last_purple_value'] >= $dice_value || ++$board['purple_position'] != $position) return false;

		$board['purple'][$position] = $dice_value;
		$board['last_purple_value'] = ($dice_value == 6) ? 0 : $dice_value;

		return self::bonusGreenOrangePurple($board, 'purple', $position);
	}

	public static function verifyGameplay(array $board, array $dice, array $choice, $whiteblue_value) {

		//if dice is not in list of dices no need to continue
		if (!array_key_exists($choice['dice'], $dice)) return false;

		//make sure chosen color = color dice, except for white
		if ($choice['dice'] != $choice['color'] && $choice['dice'] != 'white') return false;

		if ($choice['color'] == 'blue') {
			return self::verifyBlue($board, $choice['position'], $whiteblue_value);
		}

		/**
		 * $string is only used to call the method, self::$color[$key](param) does not work.
		 */
		$string = self::COLOR_FUNCTION_VERIFY[$choice['color']];

		return self::$string($board, $choice['position'], $dice[$choice['dice']]);
	}



	public static function countPoints($board) {

		//blue

		$i = 0;

		foreach ($board['blue'] as $value) {

			if ($value) $i++;
		}

		$points['blue'] = self::VALUE_CASES_BOARD['blue'][$i];

		//yellow

		$points['yellow'] = 0;

		if ($board['yellow'][13] && $board['yellow'][12] && $board['yellow'][21]) $points['yellow'] = $points['yellow'] + 10;
		if ($board['yellow'][16] && $board['yellow'][11] && $board['yellow'][23]) $points['yellow'] = $points['yellow'] + 14;
		if ($board['yellow'][15] && $board['yellow'][22] && $board['yellow'][24]) $points['yellow'] = $points['yellow'] + 16;
		if ($board['yellow'][25] && $board['yellow'][14] && $board['yellow'][26]) $points['yellow'] = $points['yellow'] + 20;

		//green

		$points['green'] = self::VALUE_CASES_BOARD['green'][$board['green']];

		//orange

		$points['orange'] = array_sum($board['orange']);

		//purple

		$points['purple'] = array_sum($board['purple']);

		//fox

		$points['fox'] = min($points) * $board['fox'];

		//total

		$points['total'] = array_sum($points);

		return $points;
	}
}