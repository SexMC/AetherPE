<?php

declare(strict_types=1);

namespace skyblock\items;

use pocketmine\item\Item;
use pocketmine\Server;
use skyblock\utils\PveUtils;

class PvEItemEditor{

	public static function setDamage(Item $item, float $damage) : void{
		$item->getNamedTag()->setFloat("pve_damage", $damage);
		ItemEditor::updateCosmetics($item);
	}



	public static function setCritChance(Item $item, float $critchance) : void{
		$item->getNamedTag()->setFloat("pve_critchance", $critchance);
		ItemEditor::updateCosmetics($item);
	}

	public static function setCritDamage(Item $item, float $critdamage) : void{
		$item->getNamedTag()->setFloat("pve_critdamage", $critdamage);
		ItemEditor::updateCosmetics($item);
	}


	public static function setIntelligence(Item $item, float $intelligence) : void{
		$item->getNamedTag()->setFloat("pve_intelligence", $intelligence);
		ItemEditor::updateCosmetics($item);
	}



	public static function setStrength(Item $item, float $strength) : void{
		$item->getNamedTag()->setFloat("pve_strength", $strength);
		ItemEditor::updateCosmetics($item);
	}





	public static function setSeaCreatureChance(Item $item, float $v) : void{
		$item->getNamedTag()->setFloat("pve_sea_creature_chance", $v);
		ItemEditor::updateCosmetics($item);
	}



	public static function setDefense(Item $item, float $defense) : void{
		$item->getNamedTag()->setFloat("pve_defense", $defense);
		ItemEditor::updateCosmetics($item);
	}


	public static function setSpeed(Item $item, float $speed) : void{
		$item->getNamedTag()->setFloat("pve_speed", $speed);

		ItemEditor::updateCosmetics($item);
	}


	public static function setHealth(Item $item, float $health) : void{
		$item->getNamedTag()->setFloat("pve_health", $health);

		ItemEditor::updateCosmetics($item);
	}



	public static function setMiningSpeed(Item $item, float $miningspeed) : void{
		$item->getNamedTag()->setFloat("pve_miningspeed", $miningspeed);

		ItemEditor::updateCosmetics($item);
	}




	public static function setMiningFortune(Item $item, float $v) : void{
		$item->getNamedTag()->setFloat("pve_miningfortune", $v);
		ItemEditor::updateCosmetics($item);
	}


	public static function getCosmeticsLore(Item $item): array {
		try{
			$lore = [];

			/*if(($v = self::getDamage($item)) !== 0.0){
				$s = PveUtils::getDamage();
				$lore[] = "§r§l§6 »§r§6 Damage: " . PveUtils::getColor($s) . ($v > 0 ? "+" : "-") . number_format(abs($v)) . " " . $s;
			}*/

			/*if(($v = self::getStrength($item)) !== 0.0){
				$s = PveUtils::getStrength();
				$lore[] = "§r§l§6 »§r§6 Strength: " . PveUtils::getColor($s) . ($v > 0 ? "+" : "-") . number_format(abs($v)) . " " . $s;
			}*/

			/*if(($v = self::getCritChance($item)) !== 0.0){
				$s = PveUtils::getCritChance();
				$lore[] = "§r§l§6 »§r§6 Crit Chance: " . PveUtils::getColor($s) . ($v > 0 ? "+" : "-") . number_format(abs($v)) . "% " . $s;
			}*/

			/*if(($v = self::getCritDamage($item)) !== 0.0){
				$s = PveUtils::getCritDamage();
				$lore[] = "§r§l§6 »§r§6 Crit Damage: " . PveUtils::getColor($s) . ($v > 0 ? "+" : "-") . number_format(abs($v)) . " " . $s;
			}*/

			/*if(($v = self::getHealth($item)) !== 0.0){
				$s = PveUtils::getHealth();
				$lore[] = "§r§l§6 »§r§6 Health: " . PveUtils::getColor($s) . ($v > 0 ? "+" : "-") . number_format(abs($v)) . " " . $s;
			}*/

			/*if(($v = self::getDefense($item)) !== 0.0){
				$s = PveUtils::getDefense();
				$lore[] = "§r§l§6 »§r§6 Defence: " . ((PveUtils::getColor($s)) . (($v > 0 ? "+" : "-"))) . (number_format(abs($v))) . " " . $s;
			}*/

			/*if(($v = self::getIntelligence($item)) !== 0.0){
				$s = PveUtils::getintelligence();
				$lore[] = "§r§l§6 »§r§6 Intelligence: " . PveUtils::getColor($s) . ($v > 0 ? "+" : "-") . number_format(abs($v)) . " " . $s;
			}*/

			/*if(($v = self::getSpeed($item)) !== 0.0){
				$s = PveUtils::getSpeed();
				$lore[] = "§r§l§6 »§r§6 Speed: " . PveUtils::getColor($s) . ($v > 0 ? "+" : "-") . number_format(abs($v)) . " " . $s;
			}*/

			/*if(($v = self::getMiningSpeed($item)) !== 0.0){
				$s = PveUtils::getMiningSpeedSymbol();
				$lore[] = "§r§l§6 »§r§6 Mining Speed: " . PveUtils::getColor($s) . ($v > 0 ? "+" : "-") . number_format(abs($v)) . " " . $s;
			}*/

			/*if(($v = self::getAbilityDamage($item)) !== 0.0){
				$lore[] = "§r§l§6 »§r§6 Ability Damage: " . ($v > 0 ? "+" : "-") . number_format($v) . "";
			}*/

			/*if(($v = self::getBonusAttackSpeed($item)) !== 0.0){
				$lore[] = "§r§l§6 »§r§6 Attack Speed: " . ($v > 0 ? "+" : "-") . number_format($v) . "";
			}*/

			/*if(($v = self::getFarmingFortune($item)) !== 0.0){
				$lore[] = "§r§l§6 »§r§6 Farming Fortune: " . ($v > 0 ? "+" : "-") . number_format($v) . "";
			}*/

			/*if(($v = self::getMiningFortune($item)) !== 0.0){
				$lore[] = "§r§l§6 »§r§6 Mining Fortune: " . ($v > 0 ? "+" : "-") . number_format($v) . "";
			}*/

			/*if(($v = self::getFishingSpeed($item)) !== 0.0){
				$lore[] = "§r§l§6 »§r§6 Fishing Speed: " . ($v > 0 ? "+" : "-") . number_format($v) . "";
			}*/

			/*if(($v = self::getSeaCreatureChance($item)) !== 0.0){
				$lore[] = "§r§l§6 »§r§6 Sea Creature Chance: " . ($v > 0 ? "+" : "-") . number_format($v) . "%";
			}*/

			return $lore;
		} catch(\Exception $e){
			Server::getInstance()->getLogger()->logException($e);
		}

		return [];
	}


}