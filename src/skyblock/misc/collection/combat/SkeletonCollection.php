<?php

declare(strict_types=1);

namespace skyblock\misc\collection\combat;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Power;
use skyblock\items\masks\types\SkeletonMask;
use skyblock\items\pets\types\SkeletonPet;
use skyblock\items\pets\types\SpiderPet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedBone;
use skyblock\items\special\types\crafting\EnchantedCookedMutton;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class SkeletonCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::BONE();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::SKELETON))),
			2 => ["+20 Combat XP"],
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::POWER(), 4)),
			4 => SkeletonMask::getItem(),
			5 => SkyblockItems::ENCHANTED_BONE(),
			6 => (SkyblockItems::MYSTERY_PET_ITEM())->setPet(new SkeletonPet()),
			7 => SkyblockItems::HURRICANE_BOW(),
			8 => ["Coming Soon"],
			9 => SkyblockItems::RUNAANS_BOW(),
			//TODO: 9 runaans bow
		];

	}

	public function getName() : string{
		return "Bone";
	}
}