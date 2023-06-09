<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseToggleableEnchant;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherEffect;

class Scotopia extends BaseToggleableEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Gives you scotopic vision (night vision)");
		$this->setApplicableTo(self::ITEM_HELMET);
		$this->setMaxLevel(1);
		
		return new CustomEnchantIdentifier("scotopia", "Scotopia");
	}

	function toggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		$player->getEffects()->add(AetherEffect::fromPlayer($player, new EffectInstance(VanillaEffects::NIGHT_VISION(), 99999 * 60)));
	}

	function unToggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		$player->getEffects()->remove(VanillaEffects::NIGHT_VISION());
	}
}