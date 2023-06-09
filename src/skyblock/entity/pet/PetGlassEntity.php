<?php

declare(strict_types=1);

namespace skyblock\entity\pet;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use skyblock\entity\object\HatEntity;

class PetGlassEntity extends HatEntity {

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setHasGravity(false);

		$this->setScale(0.1);
	}

	protected function syncNetworkData(EntityMetadataCollection $properties) : void{
		parent::syncNetworkData($properties);

		$properties->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 0.6, 0));
	}

	public function onUpdate(int $currentTick) : bool{
		//$this->location->yaw = $this->player->location->yaw;
		//$this->location->pitch = $this->player->location->pitch;

		//$this->setRotation($this->player->location->yaw, $this->player->location->pitch);
		return parent::onUpdate($currentTick);
	}
}