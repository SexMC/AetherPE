<?php

declare(strict_types=1);

namespace skyblock\misc\collection\fishing;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\angler\AnglerSet;
use skyblock\items\masks\types\FishMask;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;

class RawFishCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::RAW_FISH();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{


		return [
			1 => FishMask::getItem(),
			2 => MinionEgg::getFishingEggItem(1, MinionHandler::getInstance()->getFishingTypeByItem(VanillaItems::RAW_FISH())),
			3 => ["+50 Fishing XP"],
			4 => ["+75 Fishing XP"],
			5 => AnglerSet::getInstance()->getPieceItems(),
			6 => SkyblockItems::ENCHANTED_RAW_FISH(),
			7 => ["+200 Fishing XP"],
			8 => ["+500 Fishing XP"],
			9 => ["+900 Fishing XP"],
		];
	}

	public function getName() : string{
		return "Raw Fish";
	}
}