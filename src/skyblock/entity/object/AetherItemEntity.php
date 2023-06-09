<?php

declare(strict_types=1);

namespace skyblock\entity\object;

use pocketmine\entity\object\ItemEntity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use skyblock\player\AetherPlayer;

class AetherItemEntity extends ItemEntity{
	public const TAG_OWNING_PROFILE = "owning_profile";

	private ?string $owningProfile = null;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);

		$item = $this->getItem();
		if($item->getNamedTag()->getTag(self::TAG_OWNING_PROFILE) instanceof StringTag){
			$this->owningProfile = $item->getNamedTag()->getString(self::TAG_OWNING_PROFILE);
			$item->getNamedTag()->removeTag(self::TAG_OWNING_PROFILE);
		}

		$this->setPickupDelay(10); //0.5s
		$this->setDespawnDelay(1800); //90s
	}

	public function canPickup(AetherPlayer $player): bool {
		return $this->owningProfile === null || $player->getSelectedProfileId() === $this->owningProfile;
	}

	public function spawnTo(Player $player) : void{
		if(!$player instanceof AetherPlayer){
			return; //Should never happen but just in case
		}

		if ($this->canPickup($player)) {
			parent::spawnTo($player);
		}
	}

	public function onCollideWithPlayer(Player $player) : void{
		if(!$player instanceof AetherPlayer){
			return; //Should never happen but just in case
		}

		if ($this->canPickup($player)) {
			parent::onCollideWithPlayer($player);
		}
	}
}