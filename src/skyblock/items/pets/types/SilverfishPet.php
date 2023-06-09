<?php

declare(strict_types=1);

namespace skyblock\items\pets\types;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\itemattribute\ItemAttributeHolder;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\pets\Pet;
use skyblock\items\pets\PetInstance;
use skyblock\items\PvEItemEditor;
use skyblock\items\SkyblockItems;
use skyblock\misc\pve\PveDataRegenerator;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;

class SilverfishPet extends Pet {

	public function getCraftingIngredient() : Item{
		return SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(8);
	}

	public function getName() : string{
		return "Silverfish Pet";
	}

	public function getIdentifier() : string{
		return "silverfish";
	}

	public function applyStats(ItemAttributeHolder $item, int $level, int $rarity) : void{
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), $level));
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 0.2*$level));
	}

	public function getAbilityText(int $level, int $rarity) : array{
		$arr = [
            "§r§6§l» §r§6Defense Boost §l§6«",
			"§r§7Gain extra §a" . (1.2 * $level)  . " " . PveUtils::getDefense()
		];

		if($rarity >= self::RARITY_RARE){
			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Mining Wisdom Boost §l§6«";
			$arr[] = "§r§7Grants " . (0.25 * $level) . PveUtils::getMiningWisdom();
		}

		if($rarity === self::RARITY_LEGENDARY){
			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Dexterity §l§6«";
			$arr[] = "§r§7Gives permanent haste III";
		}


		return $arr;
	}

	public function getDesiredEvents() : array{
		return [];
	}


	public function getMaxLevel() : int{
		return 100;
	}

	public function onUse(Player $player, PetInstance $instance, Event $event) : void{}

	public function tryCall(Event $event) : void{}

	public function onActivate(Player $player, PetInstance $pet) : bool{
		assert($player instanceof AetherPlayer);

		if($pet->getRarity() === self::RARITY_LEGENDARY){
			$player->getEffects()->add(new EffectInstance(VanillaEffects::HASTE(), 999999, 2));
		}

		if($pet->getRarity() >= self::RARITY_RARE){
			$player->getPveData()->setMiningWisdom($player->getPveData()->getMiningWisdom() + (0.25 * $pet->getLevel()));
		}

		$player->getPveData()->setDefense($player->getPveData()->getDefense() + 1.2 * $pet->getLevel());

		return true;
	}

	public function onDeActivate(Player $player, PetInstance $pet) : bool{
		assert($player instanceof AetherPlayer);

		if($pet->getRarity() === self::RARITY_LEGENDARY){
			$player->getEffects()->remove(VanillaEffects::HASTE());
		}

		if($pet->getRarity() >= self::RARITY_RARE){
			$player->getPveData()->setMiningWisdom($player->getPveData()->getMiningWisdom() - (0.25 * $pet->getLevel()));
		}

		$player->getPveData()->setDefense($player->getPveData()->getDefense() - 1.2 * $pet->getLevel());

		return true;
	}

	public function getSkillType() : int{
		return self::TYPE_COMBAT;
	}

	public function getEntityId() : string{
		return EntityIds::SILVERFISH;
	}
}