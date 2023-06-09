<?php

declare(strict_types=1);

namespace skyblock\items;

use skyblock\items\customenchants\CustomEnchantableTrait;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\items\customenchants\ICustomEnchantable;
use skyblock\items\itemattribute\ItemAttributeHolder;
use skyblock\items\itemattribute\ItemAttributeTrait;
use skyblock\utils\CustomEnchantUtils;

abstract class Equipment extends SkyblockItem implements ItemAttributeHolder, ICustomEnchantable{
	use ItemAttributeTrait;
	use CustomEnchantableTrait;

	public function resetLore(array $lore = []) : void{
		foreach($this->getItemAttributes() as $attributeInstance) {
			$v = $attributeInstance->getValue();
			$attribute = $attributeInstance->getAttribute();
			$unit = $attribute->isPercentage() ? "%" : "";

			$lore[] = "§r§l§6 »§r§6 {$attribute->getName()}: " . $attribute->getColor() . ($v > 0 ? "+" : "-") . number_format($v, ((((float) ((int) $v)) === $v ? 0 : 1))) . "$unit {$attribute->getSymbol()} {$attribute->getName()}";
		}


		//start order ces by rarity
		$arr = [];
		foreach($this->getCustomEnchants() as $ce){
			$tier = $ce->getCustomEnchant()->getRarity()->getTier();

			if(!isset($arr[$tier])) $arr[$tier] = [];
			$arr[$tier][] = $ce;
		}
		//end order ces by rarity

		$ceString = "";
		foreach ($arr as $ces) {
			/** @var CustomEnchantInstance $enchantmentInstance */
			foreach($ces as $enchantmentInstance){
				$add = "";

				if($enchantmentInstance->getCustomEnchant()->getRarity()->getTier() === ICustomEnchant::RARITY_MASTERY){
					$add = "§l";
				}

				$k = "§r" . $enchantmentInstance->getCustomEnchant()->getRarity()->getColor() . $add . $enchantmentInstance->getCustomEnchant()->getIdentifier()->getName() . " " . CustomEnchantUtils::roman($enchantmentInstance->getLevel()) . "§7, ";
				$ceString .= str_replace(" ", "_-", $k) . " ";
			}
		}

		if($ceString !== ""){
			if(!empty($lore)){
				$lore[] = "§r";
			}

			$lore[] = str_replace(",", ", ", mb_substr(str_replace(["_- ", "_-"], ["", " "], wordwrap($ceString, 60, "\n§7")), 0, -3));
		}


		parent::resetLore($lore);
	}
}