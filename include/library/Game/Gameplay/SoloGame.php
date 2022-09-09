<?php

namespace Clever\Library\Game\Gameplay;

use JsonSerializable;

use Workerman\Connection\TcpConnection;

class SoloGame implements JsonSerializable
{
	/**
	 * When game is finished.
	 *  
	 * @var bool
	 */
	public $destroy = false;


	/**
	 * Game model, to access game data.
	 *  
	 * @var mixed
	 */
	private $game;


	/**
	 * Clever\Library\Game\Gameplay\Player
	 *  
	 * @var Player
	 */
	private $player;


	/**
	 * Clever\Library\Game\Gameplay\DiceSet
	 *  
	 * @var DiceSet
	 */
	private $diceSet;


	/**
	 * Callabck on message.
	 *  
	 * @var mixed
	 */
	private $onMessageCallback;


	/**
	 * Game round. Each solo game is composed of 6 rounds.
	 *  
	 * @var int
	 */
	private $round = 0;


	/**
	 * Game phase. Each round is composed of 4 phases,
	 * 3 active rounds and 1 passive round.
	 *  
	 * @var int
	 */
	private $phase = 1;


	/**
	 * If is active bonus. To block bonus requests.
	 *  
	 * @var bool
	 */
	private $isActiveBonus = false;


	/**
	 * The active bonus ID.
	 *  
	 * @var bool
	 */
	private $activeBonusID;


	/**
	 * Contains the bonus queue.
	 *  
	 * @var mixed
	 */
	private $activeBonusQueue = [];


	/**
	 * Callback for after the active bonus.
	 *  
	 * @var mixed
	 */
	private $postBonusCallback;


	/**
	 * @param Workerman\Connection\TcpConnection
	 * @param mixed $game
	 * @param mixed $player
	 * 
	 * @return void
	 */
	public function __construct(TcpConnection $connection, $game, $player)
	{
		$connection->onMessage = [$this, 'onMessage'];
		$connection->onClose = [$this, 'onClose'];

		$this->game = $game;

		$this->diceSet = new DiceSet();

		$this->player = new Player($connection, $player);

		$this->newRound();
	}


	/**
	 * Process incoming messages.
	 * 
	 * @param Workerman\Connection\TcpConnection
	 * @param string $data
	 * 
	 * @return void
	 */
	public function onMessage(TcpConnection $connection, $data)
	{
		if ($data === '"latency"') {

			$connection->send('"latency"');
			return;
		}

		echo PHP_EOL;

		if ($data === '"debug"') {

			$myfile = fopen("/var/www/html/that-is-clever/log/game/" . time() . ".clever.json", "w");
			fwrite($myfile, json_encode($this, JSON_PRETTY_PRINT));
			fclose($myfile);
			return;
		}

		if (in_array($data, ['{"bonus":"replay"}', '{"bonus":"plus1"}'])) return $this->newBonusRequest();

		if (!$this->onMessageCallback) return;

		$message = json_decode($data, true);

		return call_user_func_array($this->onMessageCallback, [$message]);
	}


	/**
	 * Process the end of connection.
	 * 
	 * @param Workerman\Connection\TcpConnection
	 * 
	 * @return void
	 */
	public function onClose(TcpConnection $connection)
	{
		$this->game->status = 5;
		$this->game->save();

		$this->player->endGame(true);

		$this->destroy = true;
	}


	/**
	 * Prepare the new round and add the bonuses if necessary.
	 * 
	 * @return void
	 */
	private function newRound()
	{
		if (++$this->round == 7) return $this->endGame();
			
		$this->phase = 1;

		if ($this->round <= 4) {

			$bonusID = $this->round == 4
				? [13, 14]
				: $this->player->applyNewRoundBonus($this->round);

			return $this->newBonus($bonusID, [$this, 'sendNewRound']);
		}

		return $this->sendNewRound();
	}


	/**
	 * Send out the info for the new round.
	 * 
	 * @return void
	 */
	private function sendNewRound()
	{
		$this->player->message([
			'last_phase' => false,
			'round' => $this->round,
			'dice' => $this->diceSet->new(),
		]);

		$this->onMessageCallback = [$this, 'activeGameplayChoice'];

		$this->player->send(true);
	}


	/**
	 * Check if value is an active bonus.
	 * 
	 * @param int $bonusID
	 * 
	 * @return bool
	 */
	private function isActiveBonus($bonusID)
	{
		return in_array($bonusID, [2, 8, 13, 14]);
	}


	/**
	 * Hanldes new bonuses accordingly.
	 * 
	 * @param array $bonusIDs
	 * @param callable $postBonusCallback
	 * 
	 * @return void
	 */
	private function newBonus(array $bonusIDs, callable $postBonusCallback)
	{
		var_dump('newBonus', $bonusIDs);
		
		$activeBonus = [];

		foreach ($bonusIDs as $key => $value) {
			
			if (!$this->isActiveBonus($value)) continue;

			$activeBonus[] = $value;

			if (count($activeBonus) == 1) continue;

			unset($bonusIDs[$key]);
		}

		$this->player->message(['bonus' => $bonusIDs]);

		if (empty($activeBonus)) return call_user_func($postBonusCallback);

		return $this->newActiveBonus($activeBonus, $postBonusCallback);
	}


	/**
	 * Route the reponse to the required responder.
	 * 
	 * @return void
	 */
	private function postActiveChoice()
	{
		if (++$this->phase == 4) {

			$this->diceSet->last();

			$this->player->message([
				'last_phase' => true,
				'used_dices' => $this->diceSet->usedDices(),
			]);

			return $this->lastPhase();
		}

		$this->player->message([
			'used_dices' => $this->diceSet->usedDices(),
		]);

		return $this->nextPhase();
	}


	/**
	 * Prepare and send out the next active turn.
	 * 
	 * @return void
	 */
	private function nextPhase()
	{
		$this->player->message([
			'dice' => $this->diceSet->activeDices(),
		]);

		$this->player->send(true);
	}


	/**
	 * Prepare and send out the passive turn.
	 * 
	 * @return void
	 */
	private function lastPhase()
	{
		$this->onMessageCallback = [$this, 'passiveGameplayChoice'];

		$this->player->message([
			'dice' => $this->diceSet->passiveDices(),
		]);

		$this->player->send(true);
	}


	/**
	 * Make sure all key in array are present
	 * 
	 * @param array $data
	 * @param string $choice
	 * @param string $dice
	 * 
	 * @return void
	 */
	private function sanatizeGameplayChoice($data, $choice = 'choice', $dice = 'dice')
	{
		if (!isset($data[$choice])) return false;

		$choice = $data[$choice];

		if (
			!isset($choice['position'], $choice['color'], $choice[$dice])
		) return false;

		return $choice;
	}


	/**
	 * Process incomming message on active turn.
	 * 
	 * @param mixed $data
	 * 
	 * @return mixed
	 */
	private function activeGameplayChoice($data)
	{
		if (
			!($choice = $this->sanatizeGameplayChoice($data))
			|| !($result = $this->player->activeChoice($this->diceSet, $choice))
		) return $this->player->send(true);

		$this->player->message = [];

		$this->diceSet->use($choice['dice']);

		return $this->endChoice($result, [$this, 'postActiveChoice']);
	}


	/**
	 * Process incomming message on passive turn.
	 * 
	 * @param mixed $data
	 * 
	 * @return mixed
	 */
	private function passiveGameplayChoice($data)
	{
		if (
			!($choice = $this->sanatizeGameplayChoice($data))
			|| !($result = $this->player->passiveChoice($this->diceSet, $choice))
		) return $this->player->send(true);

		$this->player->message = [];

		return $this->endChoice($result, [$this, 'newRound']);
	}


	/**
	 * Process incomming message when +1 is active.
	 * 
	 * @param mixed $data
	 * 
	 * @return mixed
	 */
	private function activePlusOneChoice($data)
	{
		if (
			!($choice = $this->sanatizeGameplayChoice($data, 'p1_choice'))
			|| !($result = $this->player->plusOneChoice($this->diceSet, $choice))
		) return $this->player->send(true);

		$this->player->message = [];

		return $this->endChoice($result, [$this, 'lastPhase']);
	}


	/**
	 * Handles an incomming active bonus choice.
	 * Callback for onMessage when activebonus = true;
	 * 
	 * @param $data
	 * 
	 * @return mixed
	 */
	private function activeBonusChoice($data)
	{
		if (
			!($choice = $this->sanatizeGameplayChoice($data, 'bonus_choice', 'id'))
			|| !($result = $this->player->bonusChoice($this->activeBonusID, $choice))
		) return $this->player->send(true);


		var_dump('activeBonusChoice', $result);

		$this->player->message = [];

		return $this->endChoice($result, [$this, 'postActiveBonus']);
	}


	/**
	 * Process the end of a choice.
	 * 
	 * @param array $result
	 * @param callable $postChoiceCallback
	 * 
	 * @return mixed
	 */
	private function endChoice($result, callable $postChoiceCallback)
	{
		if (!empty($result['bonus']))
			return $this->newBonus($result['bonus'], $postChoiceCallback);

		return call_user_func($postChoiceCallback);
	}


	/**
	 * Handles an incomming bonus request.
	 * 
	 * @return mixed
	 */
	private function newBonusRequest()
	{
		if ($this->isActiveBonus) return $this->player->send(true);

		if ($this->phase == 4) return $this->newPlusOneRequest();

		return $this->newReplayRequest();
	}


	/**
	 * Handles the +1 requests.
	 * 
	 * @return void
	 */
	private function newPlusOneRequest()
	{
		if (!$this->player->usePlusOneIfAvailable()) return $this->player->send(true, true);

		$this->player->message([
			'dice' => $this->diceSet->all(),
		], true);

		$this->onMessageCallback = [$this, 'activePlusOneChoice'];

		$this->player->send(true);
	}


	/**
	 * Handles the replay requests.
	 * 
	 * @return void
	 */
	private function newReplayRequest()
	{
		if (!$this->player->useReplayIfAvailable()) return $this->player->send(true, true);

		$this->player->message([
			'dice' => $this->diceSet->replay(),
		], true);

		$this->player->send(true);
	}


	/**
	 * Found new active bonus.
	 * 
	 * @param array $bonusIDs
	 * @param callable $postBonusCallback
	 * 
	 * @return void
	 */
	private function newActiveBonus(array $bonusIDs, callable $postBonusCallback)
	{
		if ($this->isActiveBonus) return $this->addActiveBonus($bonusIDs);

		return $this->launchActiveBonus($bonusIDs, $postBonusCallback);
	}


	/**
	 * Add an active bonus to the active bonuses queue.
	 * 
	 * @param array $bonusIDs
	 * @param callable $postBonusCallback
	 * 
	 * @return void
	 */
	private function addActiveBonus($bonusIDs)
	{
		$key = array_key_first($bonusIDs);

		$this->activeBonusID = $bonusIDs[$key];
		unset($bonusIDs[$key]);

		if (count($bonusIDs) > 0)
			$this->activeBonusQueue = array_merge($this->activeBonusQueue, $bonusIDs);

		$this->player->send(true);
	}


	/**
	 * Start new active bonus process.
	 * 
	 * @param array $bonusIDs
	 * @param callable $postBonusCallback
	 * 
	 * @return void
	 */
	private function launchActiveBonus($bonusIDs, $postBonusCallback)
	{
		$this->isActiveBonus = true;
		$this->postBonusCallback = $postBonusCallback;

		$key = array_key_first($bonusIDs);

		$this->activeBonusID = $bonusIDs[$key];
		unset($bonusIDs[$key]);

		if (count($bonusIDs) >= 1) $this->activeBonusQueue = $bonusIDs;

		$this->onMessageCallback = [$this, 'activeBonusChoice'];

		$this->player->send(true);
	}


	/**
	 * Decides what to do after a bonus has been called.
	 * 
	 * @return mixed
	 */
	private function postActiveBonus()
	{
		if (empty($this->activeBonusQueue)) return $this->endActiveBonus();

		return $this->nextActiveBonus();
	}


	/**
	 * Finishes the bonus round.
	 * 
	 * @return mixed
	 */
	private function endActiveBonus()
	{
		$this->isActiveBonus = false;
		$this->activeBonusID = null;

		if (
			$this->postBonusCallback === [$this, 'postActiveChoice']
		) $this->onMessageCallback = [$this, 'activeGameplayChoice'];

		return call_user_func($this->postBonusCallback);
	}


	/**
	 * Goes to the next bonus.
	 * 
	 * @return mixed
	 */
	private function nextActiveBonus()
	{
		$key = array_key_first($this->activeBonusQueue);

		$this->activeBonusID = $this->activeBonusQueue[$key];
		unset($this->activeBonusQueue[$key]);

		$this->player->message([
			'bonus' => isset($this->player->message['bonus'])
				? array_merge($this->player->message['bonus'], [$this->activeBonusID])
				: [$this->activeBonusID],
		]);

		$this->player->send(true);
	}


	/**
	 * This function ends the game.
	 * 
	 * @return void
	 */
	private function endGame()
	{
		$this->game->status = 1;
		$this->game->save();

		$this->player->endGame();

		$this->destroy = true;
	}


	/**
	 * Change the behavior of json_encode()
	 * 
	 * @return array
	 */
	public function jsonSerialize()
	{
		$onMessageCallback = $this->onMessageCallback[1] ?? '[empty]';
		$postBonusCallback = $this->postBonusCallback[1] ?? '[empty]';

		return [
			'game' => $this->game,
			'player' => $this->player,
			'diceSet' => $this->diceSet,
			'onMessageCallback' => $onMessageCallback,
			'round' => $this->round,
			'phase' => $this->phase,
			'isActiveBonus' => $this->isActiveBonus,
			'activeBonusID' => $this->activeBonusID,
			'activeBonusQueue' => $this->activeBonusQueue,
			'postBonusCallback' => $postBonusCallback,
		];
	}
}