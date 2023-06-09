<?php

declare(strict_types=1);

namespace skyblock\misc\coinflip;

use dktapps\pmforms\MenuOption;
use JsonSerializable;

class Coinflip implements JsonSerializable{

	public function __construct(
		private string $player,
		private string $color,
		private int $amount,
		private bool $used = false
	){ }

	public function getFormButton(): MenuOption {
		return new MenuOption("§f§l" . $this->player . "\n§r§c$" . number_format($this->amount));
	}

	/**
	 * @return string
	 */
	public function getPlayer() : string{
		return $this->player;
	}

	/**
	 * @return int
	 */
	public function getAmount() : int{
		return $this->amount;
	}

	/**
	 * @return string
	 */
	public function getColor() : string{
		return $this->color;
	}

	/**
	 * @return bool
	 */
	public function isUsed() : bool{
		return $this->used;
	}

	public function jsonSerialize(){
		return [
			"player" => $this->player,
			"color" => $this->color,
			"amount" => $this->amount,
			"used" => $this->used
		];
	}
}