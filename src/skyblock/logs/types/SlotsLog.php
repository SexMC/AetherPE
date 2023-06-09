<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\command\defaults\KillCommand;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\logs\Log;

class SlotsLog extends Log {

	public function __construct(string $player, string $msg, array $rewards){
		$this->data["player"] = $player;
		$this->data["message"] = TextFormat::clean($msg);
		$this->data["rewards"] = json_encode($rewards);
	}

	public function getType() : string{
		return "slots";
	}

}