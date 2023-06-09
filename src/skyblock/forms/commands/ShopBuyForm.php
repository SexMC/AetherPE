<?php

declare(strict_types=1);

namespace skyblock\forms\commands;

use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use skyblock\Main;
use skyblock\misc\shop\Shop;
use skyblock\misc\shop\ShopItem;
use skyblock\sessions\Session;

class ShopBuyForm extends CustomForm {

	public function __construct(private ShopItem $item, private Player $player){
		parent::__construct("Buy " . $this->item->getItem()->getName(), $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, CustomFormResponse $response): void {
		if(!$player->isOnline()) return;

		$index = $response->getInt("Amount");
		$add = $this->getAddCount($player);

		$amount = match($index) {
			0 => 1,
			1 => $this->item->getItem()->getMaxStackSize(),
			2 => $add,
		};

		if($response->getString("am") !== ""){
			$amount = abs((int) intval($response->getString("am")));
		}

		if($amount <= 0){
			return;
		}

		Shop::buy($player, $this->item, $amount);
	}
	
	public function getButtons(): array {
		return [
			new Dropdown("Amount", "Amount", [
				"Buy 1x for $" . number_format($this->item->getBuyPrice()),
				"Buy {$this->item->getItem()->getMaxStackSize()}x for $" . number_format($this->item->getBuyPrice() * $this->item->getItem()->getMaxStackSize()),
				"Buy {$this->getAddCount($this->player)}x for $" . number_format($this->item->getBuyPrice() * $this->getAddCount($this->player)),
			]),
			new Input("am", "Amount", "1")
		];
	}

	public function getAddCount(Player $player): int {
		$clone = clone $this->item->getItem();
		return ($player->getInventory()?->getAddableItemQuantity($clone->setCount(55 * 64))) ?? 0;
	}
}