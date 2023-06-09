<?php

declare(strict_types=1);

namespace skyblock\forms\commands;

use dktapps\pmforms\MenuOption;
use skyblock\forms\AetherMenuForm;
use skyblock\misc\pve\fishing\HotspotHandler;
use skyblock\utils\Utils;

class HotspotForm extends AetherMenuForm {


	public function __construct(){
		parent::__construct("§b§lHotspots", $this->getText(), [new MenuOption("§cClose")]);
	}


	public function getText(): string {
		$arr = [
			"§r§7Hotspots are fishing areas in the grinding world.",
			"§r§7Fishing in a hotspot will grant you extra bonuses.",
			"§r§7",
		];

		$hotspot = HotspotHandler::getInstance()->getCurrentHotspot();
		$boost = HotspotHandler::getInstance()->getBoost();
		if($hotspot !== null && $boost !== null){
			$arr[] = "§r§7Current hotspot: §c" . $hotspot->getName();
			$arr[] = "§r§7 - §a+" . number_format($boost->extraFishingSkillXP, 2) . Utils::getRandomColor() .  " Fishing Skill XP";
			$arr[] = "§r§7 - §a+" . number_format($boost->extraSeaBossSpawnEggChance, 2) . "%%%" . Utils::getRandomColor() . " Sea Boss Egg Chance";
			$arr[] = "§r§7 - §a+" . number_format($boost->extraTreasureLootChance, 2) . "%%%" . Utils::getRandomColor() . " Treasure Loot Chance";
			$arr[] = "§r§7 - §a+" . number_format($boost->fasterFishingInTicks / 20, 2) . "s " . Utils::getRandomColor() . "Faster Fishing Speed";
		} else $arr[] = "§r§7Current hotspot: §cNone";


		return implode("\n", $arr);
	}
}