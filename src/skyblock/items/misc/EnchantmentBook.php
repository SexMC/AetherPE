<?php

declare(strict_types=1);

namespace skyblock\items\misc;

use pocketmine\item\ItemIdentifier;
use skyblock\items\customenchants\CustomEnchantableTrait;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\ICustomEnchantable;
use skyblock\items\customenchants\types\Harvesting;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\utils\CustomEnchantUtils;

class EnchantmentBook extends SkyblockItem implements ICustomEnchantable{
	use CustomEnchantableTrait {
		addCustomEnchant as addCe;
	}

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->makeGlow();
	}

	public function addCustomEnchant(CustomEnchantInstance $instance) : self{
		$r =  $this->addCe($instance);
		$this->resetLore();

		return $r;
	}

	public function resetLore(array $lore = []) : void{

		$enchants = $this->getCustomEnchants();
		if(isset($enchants[0])){
			$ench = $enchants[0];
			$ce = $ench->getCustomEnchant();

			$lore = [
				"§r§3" . $ce->getIdentifier()->getName() . " " . CustomEnchantUtils::roman($ench->getLevel()),
				"§r§7" . wordwrap($ce->getDescription(), 40, "\n§7"),
				"§r",
				"§r§7Apply cost: §3" . floor(($ce->getRarity()->getTier() + 1) * $ench->getLevel() * 1.5) . " XP Levels",
				"§r§7Goes on: §3" . CustomEnchantUtils::itemTypeIntToString($ce->getApplicableTo()),
				"§r",
				"§r§7Use this on an item in an Anvil",
				"§r§7to apply it!",
			];

			$this->setCustomName("§r§l{$ce->getRarity()->getColor()}{$ce->getIdentifier()->getName()} " . CustomEnchantUtils::roman($ench->getLevel()));
			$this->properties->setRarity($ce->getRarity());
		}

		parent::resetLore($lore);
	}


	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setType(SkyblockItemProperties::ITEM_TYPE_BOOK);
	}
}