<?php

declare(strict_types=1);

namespace skyblock\misc\collection\combat;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedGrilledPork;
use skyblock\items\special\types\crafting\EnchantedPorkchop;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\PigmanSword;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class PigCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::RAW_PORKCHOP();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::PIG))),
			2 => ["+10 Combat XP"],
			3 => ["Coming Soon"],
			4 => SkyblockItems::ENCHANTED_PORKCHOP(),
			5 => ["+350 Combat XP"],
			6 => ["+500 Combat XP"],
			7 => SkyblockItems::ENCHANTED_GRILLED_PORK(),
			8 => ["Coming Soon"],
			9 => SkyblockItems::PIGMAN_SWORD(),
		];
	}

	public function getName() : string{
		return "Porkchop";
	}
}