<?php

declare(strict_types=1);

namespace skyblock\misc\collection\combat;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\zombie\ZombieSet;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\masks\types\ZombiesHeartMask;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class ZombieCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::ROTTEN_FLESH();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::ZOMBIE))),
			2 => SkyblockItems::ZOMBIE_PICKAXE(),
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::SMITE(), 4)),
			4 => SkyblockItems::ENCHANTED_ROTTEN_FLESH(),
			5 => (SkyblockItems::COMBAT_SACK())->setCapacity(StorageSack::SIZE_SMALL),
			6 => ZombiesHeartMask::getItem(),
			7 => [(SkyblockItems::COMBAT_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), SkyblockItems::ZOMBIE_SWORD()],
			8 => ZombieSet::getInstance()->getPieceItems(),
			9 => (SkyblockItems::COMBAT_SACK())->setCapacity(StorageSack::SIZE_LARGE), //TODO: on 9th add also mystery zombie pet
		];
	}

	public function getName() : string{
		return "Zombie";
	}
}