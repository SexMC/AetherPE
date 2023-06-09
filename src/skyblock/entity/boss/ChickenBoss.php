<?php

declare(strict_types=1);

namespace skyblock\entity\boss;

use Attribute;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\player\Player;
use skyblock\entity\ai\MovingEntity;

class ChickenBoss extends MovingEntity {


	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setScale(5);
	}

	public function spawnTo(Player $player) : void{
		parent::spawnTo($player);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0.8, 0.6);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::CHICKEN;
	}

	public function getName() : string{
		return "Queen Chicken Boss";
	}
}