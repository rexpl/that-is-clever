<?php

namespace Clever\Library\Game\Gameplay;

use Workerman\Connection\TcpConnection;

class SoloGame
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
	private $round = 1;


	/**
	 * Game phase. Each round is composed of 4 phases,
	 * 3 active rounds and 1 passive round.
	 *  
	 * @var int
	 */
	private $phase;


	/**
	 * If is active bonus. To block bonus requests.
	 *  
	 * @var bool
	 */
	private $isActiveBonus;


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
	private $activeBonusQueue = null;


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

		$this->diceSet = new DiceSet();

		$this->player = new Player($connection, $player);
		$this->player->game = $this;

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

		if (in_array($data, ['{"bonus":"replay"}', '{"bonus":"plus1"}'])) return $this->newBonusRequest();

		if (!$this->onMessageCallback) return;

		$message = json_decode($data, true);

		return call_user_func_array($this->onMessageCallback, [$message]);
	}


	public function onClose(TcpConnection $connection)
	{
		$this->destroy = true;

		posix_kill(posix_getppid(), SIGINT);
	}


	private function newBonusRequest()
	{
		if ($this->phase == 4) return $this->newPlusOneRequest();

		return $this->newReplayRequest();
	}


	private function newPlusOneRequest()
	{
		if (!$this->player->usePlusOneIfAvailable()) return $this->player->send(true, true);

		$this->player->message([
			'dice' => $this->diceSet->all(),
		], true);

		$this->onMessageCallback = [$this, 'activePlusOneChoice'];

		$this->player->send(true);
	}


	private function activePlusOneChoice($data)
	{
		$choice = $this->sanatizeGameplayChoice($data, 'p1_choice');

		if (!$choice) return $this->player->send(true);

		$result = $this->player->plusOneChoice($this->diceSet, $choice);

		if (!$result) return $this->player->send(true);

		$this->player->message = [];

		if (!empty($result['bonus']))
			return $this->bonusAfterChoice($result['bonus'], [$this, 'lastPhase']);

		return $this->lastPhase();
	}


	private function newReplayRequest()
	{
		if (!$this->player->useReplayIfAvailable()) return $this->player->send(true, true);

		$this->player->message([
			'dice' => $this->diceSet->replay(),
		], true);

		$this->player->send(true);
	}


	private function isActiveBonus($bonusID)
	{
		return in_array($bonusID, [2, 8, 13, 14]);
	}


	private function newActiveBonus(array $bonusIDs, callable $postBonusCallback)
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


	private function activeBonusChoice($data)
	{
		$choice = $this->sanatizeGameplayChoice($data, 'bonus_choice', 'id');

		if (!$choice) return $this->player->send(true);


	}


	private function newRound()
	{
		$this->phase = 1;

		if ($this->round <= 4) {

			$bonusID = $this->round == 4
				? 13
				: $this->player->applyNewRoundBonus($this->round);

			$this->player->message(['bonus' => [$bonusID]]);

			if ($this->round == 4) return $this->newActiveBonus([13, 14], [$this, 'sendNewRound']);
		}

		return $this->sendNewRound();
	}


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


	private function bonusAfterChoice(array $bonusIDs, callable $postBonusCallback)
	{
		$activeBonus = [];

		foreach ($bonusIDs as $key => $value) {
			
			if (!$this->isActiveBonus($value)) continue;

			$activeBonus[] = $value;

			if (empty($activeBonus)) continue;

			unset($bonusIDs[$key]);
		}

		$this->player->message(['bonus' => $bonusIDs]);

		if (empty($activeBonus)) return call_user_func($postBonusCallback);

		return $this->newActiveBonus($activeBonus, $postBonusCallback);
	}


	private function postActiveChoice()
	{
		if (++$this->phase == 4) {

			if (++$this->round == 7) return $this->endGame();

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


	private function nextPhase()
	{
		$this->player->message([
			'dice' => $this->diceSet->activeDices(),
		]);

		$this->player->send(true);
	}


	private function lastPhase()
	{
		$this->onMessageCallback = [$this, 'passiveGameplayChoice'];

		$this->player->message([
			'dice' => $this->diceSet->last(),
		]);

		$this->player->send(true);
	}


	private function sanatizeGameplayChoice($data, $choice = 'choice', $dice = 'dice')
	{
		if (!isset($data[$choice])) return false;

		$choice = $data[$choice];

		if (
			!isset($choice['position'], $choice['color'], $choice[$dice])
		) return false;

		return $choice;
	}


	private function activeGameplayChoice($data)
	{
		$choice = $this->sanatizeGameplayChoice($data);

		if (!$choice) return $this->player->send(true);

		$result = $this->player->activeChoice($this->diceSet, $choice);

		if (!$result) return $this->player->send(true);

		$this->player->message = [];

		$this->diceSet->use($choice['dice']);

		if (!empty($result['bonus']))
			return $this->bonusAfterChoice($result['bonus'], [$this, 'postActiveChoice']);

		return $this->postActiveChoice();
	}


	private function passiveGameplayChoice($data)
	{
		$choice = $this->sanatizeGameplayChoice($data);

		if (!$choice) return $this->player->send(true);

		$result = $this->player->passiveChoice($this->diceSet, $choice);

		if (!$result) return $this->player->send(true);

		$this->player->message = [];

		if (!empty($result['bonus']))
			return $this->bonusAfterChoice($result['bonus'], [$this, 'newRound']);

		return $this->newRound();
	}
}