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
use skyblock\items\customenchants\types\Knockback;
use skyblock\items\customenchants\types\Punch;
use skyblock\items\masks\types\SlimeHatMask;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedSlimeball;
use skyblock\items\special\types\crafting\EnchantedSlimeBlock;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class SlimeCollection extends Collection {

	//TODO: finish this

	public function getItem() : Item{
		return VanillaItems::SLIMEBALL();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::SLIME))),
			2 => SlimeHatMask::getItem(),
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::KNOCKBACK(), 1)),
			4 => ["+250 Combat XP"],
			5 => SkyblockItems::ENCHANTED_SLIMEBALL(),
			6 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::PUNCH(), 1)),
			7 => ["+500 Combat XP"],
			8 => SkyblockItems::ENCHANTED_SLIME_BLOCK(),
			//TODO: slime bow MAYBE IN AN UPDATE? ,
		];
	}

	public function getName() : string{
		return "Slimeball";
	}
}