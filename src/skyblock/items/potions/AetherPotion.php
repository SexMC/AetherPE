<?php

declare(strict_types=1);

namespace skyblock\items\potions;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\player\AetherPlayer;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\TimeUtils;

abstract class AetherPotion {

	public const TAG_TYPE = "aether_potion_type";
	public const TAG_LEVEL = "aether_potion_level";
	public const TAG_DURATION = "aether_potion_duration";
	public const TAG_REDSTONE_USED = "aether_potion_redstone_used";
	public const TAG_GLOW_STONE_DUST_USED = "aether_potion_glow_stone_used";

	public abstract static function getName(): string;
	public abstract static function getEffectsLore(int $level): array;
	public abstract function getInputWithOutputs(): array;






	public function onActivate(AetherPlayer $player, int $level, int $duration): void {}

	public function onDeActivate(?AetherPlayer $player, int $level, int $duration): void {}
}