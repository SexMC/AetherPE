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
use skyblock\utils\TimeUtils;

class HastePotion extends SkyBlockPotion {
	public function getPotionName() : string{
		return "Haste";
	}

	public static function getEffectsLore(int $level): array {
		$speed = number_format(self::getBoostByLevel($level));

		return [
			"+$speed Mining Speed"
		];
	}

	public static function getBoostByLevel(int $level): int {
		return 20 * $level;
	}

	public function onActivate(AetherPlayer $player) : void{
		$player->getPveData()->setMiningSpeed($player->getPveData()->getMiningSpeed() + self::getBoostByLevel($this->getPotionLevel()));
	}


	public function onDeActivate(?AetherPlayer $player) : void{
		$player->getPveData()->setMiningSpeed($player->getPveData()->getMiningSpeed() - self::getBoostByLevel($this->getPotionLevel()));
	}

	public function getInputWithOutputs() : array{
		return [
			[VanillaItems::COAL(), SkyBlockPotions::HASTE()->setPotionLevel(1)],
		];
	}
}