<?php

declare(strict_types=1);

namespace skyblock\items\potions\types;

use pocketmine\block\BrewingStand;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\potions\AetherPotion;
use skyblock\items\potions\SkyBlockPotion;
use skyblock\items\potions\SkyBlockPotions;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedCookedMutton;
use skyblock\items\special\types\crafting\EnchantedMutton;
use skyblock\items\special\types\crafting\EnchantedSugar;
use skyblock\items\special\types\crafting\EnchantedSugarcane;
use skyblock\player\AetherPlayer;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\PveUtils;
use skyblock\utils\TimeUtils;
use SOFe\AwaitGenerator\Await;

class ManaPotion extends SkyBlockPotion {
	use AwaitStdTrait;


	public static function getEffectsLore(int $level): array {
		return [
			"§7Grants §3+$level " . PveUtils::getIntelligence() . "§r§7 per second",
		];
	}

	public function onActivate(AetherPlayer $player) : void{
		Await::f2c(function() use ($player){
			while(true) {
				yield $this->getStd()->sleep(20);

				if($player->getPotionData()->isActivePotion($this)){
					$player->getPveData()->setIntelligence($player->getPveData()->getIntelligence() + $potion->level);
					continue;
				}

				break;
			}
		});
	}


	public function onDeActivate(?AetherPlayer $player) : void{}

	public function getInputWithOutputs() : array{
		return [
			[VanillaItems::RAW_MUTTON(), SkyBlockPotions::MANA()->setPotionLevel(1)],
			[SkyblockItems::ENCHANTED_MUTTON(), SkyBlockPotions::MANA()->setPotionLevel(3)],
			[SkyblockItems::ENCHANTED_COOKED_MUTTON(), SkyBlockPotions::MANA()->setPotionLevel(5)],
		];
	}

	public function getPotionName() : string{
		return "Mana";
	}
}