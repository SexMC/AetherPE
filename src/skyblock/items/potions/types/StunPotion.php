<?php

declare(strict_types=1);

namespace skyblock\items\potions\types;

use pocketmine\block\BrewingStand;
use pocketmine\block\VanillaBlocks;
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

class StunPotion extends SkyBlockPotion {
	public static function getEffectsLore(int $level): array {
		return [
			"§r§7When applied to yourself, your",
			"§r§7hits have a §a" . ($level * 10) . "%§7 chance to",
			"§r§7stun enemies for §a1s",
		];
	}

	public function onActivate(AetherPlayer $player) : void{}


	public function onDeActivate(?AetherPlayer $player) : void{}

	public function getInputWithOutputs() : array{
		return [
			[VanillaBlocks::OBSIDIAN()->asItem(), SkyBlockPotions::STUN()->setPotionLevel(1)],
		];
	}

	public function getPotionName() : string{
		return "Stun";
	}
}