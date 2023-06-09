<?php

declare(strict_types=1);

namespace skyblock\items\pets\types;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\events\pve\PveAttackPlayerEvent;
use skyblock\items\itemattribute\ItemAttributeHolder;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\pets\Pet;
use skyblock\items\pets\PetInstance;
use skyblock\items\PvEItemEditor;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\PveUtils;

class WolfPet extends Pet {

	public function getName() : string{
		return "Wolf Pet";
	}

	public function getIdentifier() : string{
		return "wolf";
	}

	public function applyStats(ItemAttributeHolder $item, int $level, int $rarity) : void{
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 0.1*$level));
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::CRITICAL_DAMAGE(), 0.1*$level));
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 0.5*$level));
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::SPEED(), 0.2*$level));
	}

	public function getAbilityText(int $level, int $rarity) : array{
		if($rarity === self::RARITY_COMMON){
			$arr = [
                "§r§6§l» §r§6Alpha Dog §l§6«",
				"§r§7Take §a" . (0.1 * $level)  . "% §r§7less damage from",
				"§r§7wolves",
			];
		} else if($rarity === self::RARITY_UNCOMMON){
			$arr = [
                "§r§6§l» §r§6Alpha Dog §l§6«",
				"§r§7Take §a" . (0.2 * $level)  . "% §r§7less damage from",
				"§r§7wolves",
			];
		} else {
			$arr = [
				"§r§6§l» §r§6Alpha Dog §l§6«",
				"§r§7Take §a" . (0.3 * $level)  . "% §r§7less damage from",
				"§r§7wolves",
			];
		}

		if($rarity >= self::RARITY_RARE){
			$arr = array_merge($arr, [
				"§r",
				"§r§6§l» §r§6Pack Leader §l§6«",
				"§r§7Gain §6" . (($rarity === self::RARITY_RARE ? 0.10 : 0.15) * $level)  . PveUtils::getCritDamage(),
				"§r§7for every nearby wolf monsters",
				"§r§8Max 10 spiders",
			]);
		}

		if($rarity === self::RARITY_LEGENDARY){
			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Combat Wisdom Boost §l§6«";
			$arr[] = "§r§7Grants §3" . (0.3 * $level) . " Combat Wisdom";
		}


		return $arr;
	}

	public function getDesiredEvents() : array{
		return [PlayerAttackPveEvent::class];
	}


	public function getMaxLevel() : int{
		return 100;
	}

	public function onUse(Player $player, PetInstance $instance, Event $event) : void{
		if($event instanceof PveAttackPlayerEvent){
			$e = $event->getEntity();

			if($e->getNetworkID() === EntityIds::WOLF){
				$r = $instance->getRarity();
				
				$event->divideDamage(($r === self::RARITY_COMMON ? 0.1 : ($r === self::RARITY_RARE ? 0.2 : 0.3)), $this->getName());
			}
		}
	}

	public function tryCall(Event $event) : void{
		if($event instanceof PveAttackPlayerEvent){
			$e = $event->getPlayer();

			if($e->getPetData()->getActivePetId() !== $this->getIdentifier()) return;


			$pet = $e->getPetData()->getPetCollection()[$this->getIdentifier()];

			if($pet?->getPet() instanceof $this){
				$this->onUse($e, $pet, $event);
			}
		}
	}

	public function onActivate(Player $player, PetInstance $pet) : bool{
		if($pet->getRarity() === self::RARITY_LEGENDARY){
			assert($player instanceof AetherPlayer);
			$player->getPveData()->setCombatWisdom($player->getPveData()->getCombatWisdom() + (0.3 * $pet->getLevel()));
		}

		return true;
	}

	public function onDeActivate(Player $player, PetInstance $pet) : bool{
		if($pet->getRarity() === self::RARITY_LEGENDARY){
			assert($player instanceof AetherPlayer);
			$player->getPveData()->setCombatWisdom($player->getPveData()->getCombatWisdom() - (0.3 * $pet->getLevel()));
		}

		return true;
	}

	public function getSkillType() : int{
		return self::TYPE_COMBAT;
	}

	public function getEntityId() : string{
		return EntityIds::WOLF;
	}

	public function getCraftingIngredient() : Item{
		return SkyblockItems::ENCHANTED_SPRUCE_WOOD()->setCount(64);
	}
}