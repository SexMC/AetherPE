<?php

declare(strict_types=1);

namespace skyblock\misc\collection\foraging;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\SavannaBow;
use skyblock\misc\collection\Collection;
use skyblock\misc\trades\TradesHandler;

class AcaciaCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::ACACIA_LOG()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getForagerEggItem(1, MinionHandler::getInstance()->getForagerTypeByBlock(VanillaBlocks::ACACIA_LOG())),
			2 => TradesHandler::getInstance()->getByOutput(VanillaBlocks::ACACIA_LEAVES()->asItem()),
			3 => ["+150 Foraging XP"],
			4 => ["+200 Foraging XP"],
			5 => SkyblockItems::ENCHANTED_ACACIA_WOOD(),
			6 => ["+575 Foraging XP"],
			7 => SkyblockItems::SAVANNA_BOW(),
			8 => ["Coming Soon"],
			9 => ["Coming Soon"],
		];
	}

	public function getName() : string{
		return "Acacia Wood";
	}
}