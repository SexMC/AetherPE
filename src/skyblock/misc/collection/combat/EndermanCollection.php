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
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedEnderPearl;
use skyblock\items\special\types\crafting\EnchantedGrilledPork;
use skyblock\items\special\types\crafting\EnchantedPorkchop;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\PigmanSword;
use skyblock\items\weapons\AspectOfTheEndSword;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class EndermanCollection extends Collection{


	public function getItem() : Item{
		return VanillaItems::RAW_PORKCHOP();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::ENDERMAN))),
			2 => SkyblockItems::ENCHANTED_ENDER_PEARL(),
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::ENDER_SLAYER(), 4)),
			4 => ["Coming Soon"],
			5 => SkyblockItems::ENDER_BOW(),
			6 => SkyblockItems::ENCHANTED_EYE_OF_ENDER(),
			7 => ["Coming Soon"],
			8 => SkyblockItems::ASPECT_OF_THE_END_SWORD(),
		];
	}

	public function getName() : string{
		return "Ender Pearl";
	}
}