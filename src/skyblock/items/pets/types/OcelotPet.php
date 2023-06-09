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

class OcelotPet extends Pet {

	private array $entityIds = []; //array where all slowed entity ids r stored so we don't slow en multiple times for web weaver

	public function getCraftingIngredient() : Item{
		return SkyblockItems::ENCHANTED_JUNGLE_WOOD()->setCount(64);
	}

	public function getName() : string{
		return "Ocelot Pet";
	}

	public function getIdentifier() : string{
		return "ocelot";
	}

	public function applyStats(ItemAttributeHolder $item, int $level, int $rarity) : void{
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 0.5*$level));
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::SPEED(), 0.5*$level));
	}

	public function getAbilityText(int $level, int $rarity) : array{
		if($rarity === self::RARITY_COMMON){
			$arr = [
                "§r§6§l» §r§6Foraging Wisdom Boost §l§6«",
				"§r§7Grants §3+" . (0.2 * $level)  . " foraging wisdom",
			];
		} else if($rarity === self::RARITY_RARE || $rarity === self::RARITY_UNCOMMON){
			$arr = [
                "§r§6§l» §r§6Foraging Wisdom Boost §l§6«",
				"§r§7Grants §3+" . (0.25 * $level)  . " foraging wisdom",
			];
		} else {
			$arr = [
                "§r§6§l» §r§6Foraging Wisdom Boost §l§6«",
				"§r§7Grants §3+" . (0.3 * $level)  . " foraging wisdom",
			];
		}

		//TODO: this pet is not done and will be added on a later update


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


			$pet = PetInstance::fromItem($e->getInventory()->getItemInHand());

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
		return EntityIds::OCELOT;
	}
}