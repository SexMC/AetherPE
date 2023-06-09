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
use skyblock\items\special\types\crafting\EnchantedSugar;
use skyblock\items\special\types\crafting\EnchantedSugarcane;
use skyblock\player\AetherPlayer;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\TimeUtils;

class SpeedPotion extends SkyBlockPotion {
	public static function getEffectsLore(int $level): array {
		$speed = number_format(self::getBoostByLevel($level));

		return [
			"+$speed Speed"
		];
	}


	public static function getBoostByLevel(int $level): int {
		return 25 * $level;
	}

	public function onActivate(AetherPlayer $player) : void{
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() + self::getBoostByLevel($this->getPotionLevel()));
	}


	public function onDeActivate(?AetherPlayer $player) : void{
		$player?->getPveData()->setSpeed($player->getPveData()->getSpeed() - self::getBoostByLevel($this->getPotionLevel()));
	}

	public function getInputWithOutputs() : array{
		return [
			[VanillaItems::SUGAR(), SkyBlockPotions::SPEED()->setPotionLevel(1)],
			[SkyblockItems::ENCHANTED_SUGAR(), SkyBlockPotions::SPEED()->setPotionLevel(3)],
			[SkyblockItems::ENCHANTED_SUGARCANE(), SkyBlockPotions::SPEED()->setPotionLevel(5)],
		];
	}

	public function getPotionName() : string{
		return "Speed";
	}
}