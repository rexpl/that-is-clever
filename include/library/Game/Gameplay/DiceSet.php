<?php

namespace Clever\Library\Game\Gameplay;

class DiceSet
{
	/**
	 * Last dices wich have been thrown.
	 * 
	 * @var array
	 */
	private $lastThrow = [];


	/**
	 * Value of the last thrown blue dice.
	 * 
	 * @var int
	 */
	private $blue;


	/**
	 * Value of the last thrown white dice.
	 * 
	 * @var int
	 */
	private $white;


	/**
	 * Saves dices wich have a lower value than the played dice for the last phase of each round.
	 * 
	 * @var array
	 */
	private $lastPhase = [];


	/**
	 * In case user use a +1, he should also be able to use the previously used dices.
	 * 
	 * @var array
	 */
	private $used = [];


	/**
	 * Saves the dices value before we rethrow all values.
	 * This is necessary for the last phase of each round as we want the previous values.
	 * 
	 * @var array
	 */
	private $saveLastThrow = [];


	/**
	 * New round.
	 * 
	 * @return array
	 */
	public function new()
	{
		foreach (['blue', 'yellow', 'green', 'orange', 'purple', 'white'] as $key) {
			
			$this->lastThrow[$key] = random_int(1, 6);
		}

		$this->blue = $this->lastThrow['blue'];
		$this->white = $this->lastThrow['white'];

		$this->lastPhase = [];
		$this->used = [];

		return $this->lastThrow;
	}


	/**
	 * Last phase.
	 * 
	 * @return array
	 */
	public function last()
	{
		$this->lastPhase = $this->lastPhase + $this->saveLastThrow;

		$this->blue = $this->lastPhase['blue'] ?? $this->used['blue'];
		$this->white = $this->lastPhase['white'] ?? $this->used['white'];

		return $this->lastPhase;
	}


	/**
	 * Marks a dice as used. Invalid the lower ones.
	 * Rethrow the leftover dices.
	 * 
	 * @param string $color
	 * 
	 * @return array
	 */
	public function use($color)
	{
		$this->used[$color] = $this->lastThrow[$color];
		unset($this->lastThrow[$color]);

		$this->saveLastThrow = $this->lastThrow;

		foreach ($this->lastThrow as $key => $value) {
			
			if ($value < $this->used[$color]) {

				$this->lastPhase[$key] = $value;

				unset($this->lastThrow[$key]);
				unset($this->saveLastThrow[$key]);

				continue;
			}

			$this->lastThrow[$key] = random_int(1, 6);

			if (in_array($key, ['blue', 'white'])) {

				$this->$key = $this->lastThrow[$key];
			}
		}

		return $this->lastThrow;
	}


	/**
	 * Rethrow all active dices. Replay Bonus.
	 * 
	 * @return array
	 */
	public function replay()
	{
		foreach ($this->lastThrow as $key => $value) {
				
			$this->lastThrow[$key] = random_int(1, 6);

			if (in_array($key, ['blue', 'white'])) {

				$this->$key = $this->lastThrow[$key];
			}
		}

		return $this->lastThrow;
	}


	/**
	 * Returns all values for a +1.
	 * 
	 * @return array
	 */
	public function all()
	{
		return $this->lastPhase + $this->used;
	}


	/**
	 * Returns all active dices.
	 * 
	 * @return array
	 */
	public function activeDices()
	{
		return $this->lastThrow;
	}


	/**
	 * Returns all passive dices.
	 * 
	 * @return array
	 */
	public function passiveDices()
	{
		return $this->lastPhase;
	}


	/**
	 * Returns all used dices.
	 * 
	 * @return array
	 */
	public function usedDices()
	{
		return $this->used;
	}


	/**
	 * Returns the values for the color blue.
	 * 
	 * @return int
	 */
	public function blue()
	{
		return $this->blue + $this->white;
	}
}