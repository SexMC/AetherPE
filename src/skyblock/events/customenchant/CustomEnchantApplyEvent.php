<?php

declare(strict_types=1);

namespace skyblock\events\customenchant;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;
use skyblock\items\customenchants\BaseCustomEnchant;

class CustomEnchantApplyEvent extends PlayerEvent {

	public function __construct(
		Player $player,
		protected BaseCustomEnchant $customEnchant,
		protected int $level,
		protected int $success,
		protected int $destroy,
	){
		$this->player = $player;
	}

	/**
	 * @return \skyblock\items\customenchants\BaseCustomEnchant
	 */
	public function getCustomEnchant() : BaseCustomEnchant{
		return $this->customEnchant;
	}

	/**
	 * @param \skyblock\items\customenchants\BaseCustomEnchant $customEnchant
	 */
	public function setCustomEnchant(BaseCustomEnchant $customEnchant) : void{
		$this->customEnchant = $customEnchant;
	}

	/**
	 * @return int
	 */
	public function getLevel() : int{
		return $this->level;
	}

	/**
	 * @param int $level
	 */
	public function setLevel(int $level) : void{
		$this->level = $level;
	}

	/**
	 * @return int
	 */
	public function getSuccess() : int{
		return $this->success;
	}

	/**
	 * @param int $success
	 */
	public function setSuccess(int $success) : void{
		$this->success = $success;
	}

	/**
	 * @return int
	 */
	public function getDestroy() : int{
		return $this->destroy;
	}

	/**
	 * @param int $destroy
	 */
	public function setDestroy(int $destroy) : void{
		$this->destroy = $destroy;
	}
}