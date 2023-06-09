<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\logs\Log;

class SellLog extends Log {

	public function __construct(Player $player, int $profit, array $drops){
		$this->data["player"] = $player->getName();
		$this->data["items"] = json_encode($drops);
		$this->data["profit"] = $profit;
	}

	public function getType() : string{
		return "sell";
	}
}