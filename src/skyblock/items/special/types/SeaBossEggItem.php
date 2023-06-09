<?php

declare(strict_types=1);

namespace skyblock\items\special\types;

use ffmpeg_animated_gif;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\items\special\SpecialItem;

class SeaBossEggItem extends SpecialItem {

	const TYPE_PATRICK = "patrick";
	const TYPE_GIBLE = "gible";
	const TYPE_SEAL = "seal";

	public string $type = "patrick";

	public static function getItem(string $type): Item {
		$item = VanillaBlocks::DRAGON_EGG()->asItem();

		$name = self::getName($type);
		$item->setCustomName("§r§l§3Sea Creature ({$name}§3§l) §r§7(Place)");
		$item->setLore(self::getLore($type));

		self::addNameTag($item);

		return $item;
	}

	public static function getLore(string $type): array {
		return match ($type) {
			self::TYPE_PATRICK => [
				"§r§7Place this boss in warzone, only",
				"§r§7your island is able to interact with the",
				"§r§7sea creature!",
				"§r",
				"§r§3§lLORE",
				"§r§7§oSmelly Sea creature",
				"§r§7§obetter have your noses",
				"§r§7§oclosed, cause it's very smelly",
				"§r",
				"§r§l§3DIFFICULTY",
				"§r§4Extremely Hard",
			],
			self::TYPE_SEAL => [
				"§r§7Place this boss in warzone, only",
				"§r§7your island is able to interact with the",
				"§r§7sea creature!",
				"§r",
				"§r§3§lLORE",
				"§r§7§oDelicate sea creature",
				"§r§7§ostill learning how to fight",
				"§r§7§olike the TopG Mr Patrick Tate",
				"§r",
				"§r§l§3DIFFICULTY",
				"§r§3Novice",
			],
			default => [
				"§r§7Place this boss in warzone, only",
				"§r§7your island is able to interact with the",
				"§r§7sea creature!",
				"§r",
				"§r§3§lLORE",
				"§r§7§oFatty Sea creature",
				"§r§7§obetter be prepared to",
				"§r§7§orun marathons around it",
				"§r",
				"§r§l§3DIFFICULTY",
				"§r§3Intermediate",
			],
		};
	}

	public static function getName(string $type): string {
		return match($type) {
			self::TYPE_PATRICK => "§cMr Patrick Tate",
			self::TYPE_SEAL => "§dMrs Seal Tate",
			default => "§eMrs Gible Tate",
		};
	}



	public static function getItemTag() : string{
		return "sea_boss_egg_item";
	}
}