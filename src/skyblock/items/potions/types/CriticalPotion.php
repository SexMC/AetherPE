<?php

declare(strict_types=1);

namespace skyblock\items\potions\types;

use pocketmine\block\BrewingStand;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\potions\AetherPotion;
use skyblock\items\potions\SkyBlockPotion;
use skyblock\items\potions\SkyBlockPotions;
use skyblock\items\special\types\crafting\EnchantedSugar;
use skyblock\items\special\types\crafting\EnchantedSugarcane;
use skyblock\player\AetherPlayer;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\PveUtils;
use skyblock\utils\TimeUtils;

class CriticalPotion extends SkyBlockPotion {
	public function getPotionName() : string{
		return "Critical";
	}

	public static function getEffectsLore(int $level): array {
		return [
			"+" . (10 + ($level * 5)) . "% " . PveUtils::getCritChance(),
			"+" . (10 * $level) . "% " . PveUtils::getCritDamage()
		];
	}


	public function onActivate(AetherPlayer $player) : void{
		$player->getPveData()->setCritChance($player->getPveData()->getCritChance() + (10 + (5 * $this->getPotionLevel())));
		$player->getPveData()->setCritDamage($player->getPveData()->getCritDamage() + (10 * $this->getPotionLevel()));
	}


	public function onDeActivate(?AetherPlayer $player) : void{
		$player->getPveData()->setCritChance($player->getPveData()->getCritChance() - (10 + (5 * $this->getPotionLevel())));
		$player->getPveData()->setCritDamage($player->getPveData()->getCritDamage() - (10 * $this->getPotionLevel()));
	}


	public function getInputWithOutputs() : array{
		return [
			[VanillaItems::FLINT(), SkyBlockPotions::CRITICAL()->setPotionLevel(1)],
		];
	}
}