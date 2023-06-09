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
use skyblock\items\special\types\crafting\EnchantedGlisteringMelon;
use skyblock\items\special\types\crafting\EnchantedMelon;
use skyblock\items\special\types\crafting\EnchantedSugar;
use skyblock\items\special\types\crafting\EnchantedSugarcane;
use skyblock\player\AetherPlayer;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\PveUtils;
use skyblock\utils\TimeUtils;

class HealthPotion extends SkyBlockPotion {
	public function getPotionName() : string{
		return "Health";
	}

	public static function getEffectsLore(int $level): array {
		$speed = number_format(self::getBoostByLevel($level));

		return [
			"+$speed" . PveUtils::getHealthSymbol() . " Max Health",
		];
	}

	public static function getBoostByLevel(int $level): int {
		return 50 * $level;
	}

	public function onActivate(AetherPlayer $player) : void{
		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + self::getBoostByLevel($this->getPotionLevel()));
	}


	public function onDeActivate(?AetherPlayer $player) : void{
		$player?->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() - self::getBoostByLevel($this->getPotionLevel()));
	}

	public function getInputWithOutputs() : array{
		return [
			[VanillaItems::GLISTERING_MELON(), SkyBlockPotions::HEALTH()->setPotionLevel(1)],
			[SkyblockItems::ENCHANTED_MELON(), SkyBlockPotions::HEALTH()->setPotionLevel(3)],
			[SkyblockItems::ENCHANTED_GLISTERING_MELON(), SkyBlockPotions::HEALTH()->setPotionLevel(5)],
		];
	}
}