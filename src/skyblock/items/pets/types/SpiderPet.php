<?php

declare(strict_types=1);

namespace skyblock\items\pets\types;

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
use skyblock\sessions\Session;
use skyblock\utils\PveUtils;

class SpiderPet extends Pet {

	private array $entityIds = []; //array where all slowed entity ids r stored so we don't slow en multiple times for web weaver

	public function getCraftingIngredient() : Item{
		return SkyblockItems::ENCHANTED_STRING()->setCount(64);
	}

	public function getName() : string{
		return "Spider Pet";
	}

	public function getIdentifier() : string{
		return "spider";
	}

	public function applyStats(ItemAttributeHolder $item, int $level, int $rarity) : void{
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 0.1*$level +0.1));
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::CRITICAL_CHANCE(), 0.1*$level + 0.1));
	}

	public function getAbilityText(int $level, int $rarity) : array{
		$arr = [
			"§r§6§l» §r§6One With The Spider §l§6«",
			"§r§7Gain §a" . (0.1 * $level)  . " " . PveUtils::getStrength() . "§7 for",
			"§r§7every nearby spider.",
			"§r§8Max 10 spiders",
		];

		if($rarity >= self::RARITY_RARE){
			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Web Weaver §l§6«";
			$arr[] = "§r§7Upon hitting a monster it";
			$arr[] = "§r§7becomes §a" . (0.4 * $level) . "% §7slower";
		}

		if($rarity === self::RARITY_LEGENDARY){
			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Spider Whisperer §l§6«";
			$arr[] = "§r§7Spider minions work " . (0.3 * $level) . "%";
			$arr[] = "§r§7faster while on your island.";
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
		if($event instanceof PlayerAttackPveEvent){
			$e = $event->getEntity();

			if(isset($this->entityIds[$e->getId()])){
				return;
			}

			$this->entityIds[$e->getId()] = true;
			$e->speed *= 1 - ($instance->getLevel() * 0.004);
		}
	}

	public function tryCall(Event $event) : void{
		if($event instanceof PlayerAttackPveEvent){
			$e = $event->getPlayer();

			if($e->getPetData()->getActivePetId() !== $this->getIdentifier()) return;


			$pet = $e->getPetData()->getPetCollection()[$this->getIdentifier()];

			if($pet?->getPet() instanceof $this){
				$this->onUse($e, $pet, $event);
			}
		}
	}

	public function onActivate(Player $player, PetInstance $pet) : bool{
		return true;
	}

	public function getSkillType() : int{
		return self::TYPE_COMBAT;
	}

	public function getEntityId() : string{
		return EntityIds::SPIDER;
	}
}