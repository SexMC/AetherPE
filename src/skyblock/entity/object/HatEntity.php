<?php

declare(strict_types=1);

namespace skyblock\entity\object;


use pocketmine\block\Block;
use pocketmine\entity\Attribute;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\Attribute as NetworkAttribute;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;

class HatEntity extends Living {

	public function __construct(Location $location, ?CompoundTag $nbt = null, protected Entity $player, protected Block $block){ parent::__construct($location, $nbt); }

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setCanSaveWithChunk(false);

		$properties = $this->getNetworkProperties();

		$id = RuntimeBlockMapping::getInstance()->toRuntimeId($this->block->getFullId());

		$properties->setInt(EntityMetadataProperties::MINECART_DISPLAY_BLOCK, $id);
		$properties->setByte(EntityMetadataProperties::MINECART_HAS_DISPLAY, 1);
		$properties->setGenericFlag(EntityMetadataFlags::INVISIBLE, true);
		$properties->setGenericFlag(EntityMetadataFlags::IMMOBILE, true);
		$properties->setGenericFlag(EntityMetadataFlags::SILENT, true);
		//$properties->setByte(EntityMetadataProperties::MINECART_DISPLAY_OFFSET, 1);
		$properties->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 0.35, 0));


		//$properties->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 0.4, 0));
		//$properties->setInt(EntityMetadataProperties::VARIANT, $id);
	}

	protected function syncNetworkData(EntityMetadataCollection $properties) : void{
		$id = RuntimeBlockMapping::getInstance()->toRuntimeId($this->block->getFullId());


		$properties->setInt(EntityMetadataProperties::MINECART_DISPLAY_BLOCK, $id);
		$properties->setByte(EntityMetadataProperties::MINECART_HAS_DISPLAY, 1);
		$properties->setGenericFlag(EntityMetadataFlags::INVISIBLE, true);
		$properties->setGenericFlag(EntityMetadataFlags::IMMOBILE, true);
		$properties->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 0.35, 0));
		$properties->setGenericFlag(EntityMetadataFlags::SILENT, true);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0, 0);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::MINECART;
	}

	public function onUpdate(int $currentTick) : bool{
		$this->location->yaw = $this->player->location->yaw;
		$this->location->pitch = $this->player->location->pitch;
		//$this->setRotation($this->player->location->yaw, $this->player->location->pitch);
		return parent::onUpdate($currentTick);
	}

	public function attack(EntityDamageEvent $source) : void{
		//return parent::attack($source);
	}

	protected function sendSpawnPacket(Player $player) : void{
		$player->getNetworkSession()->sendDataPacket(AddActorPacket::create(
			$this->getId(), //TODO: actor unique ID
			$this->getId(),
			static::getNetworkTypeId(),
			$this->location->asVector3(),
			$this->getMotion(),
			$this->location->pitch,
			$this->location->yaw,
			$this->location->yaw, //TODO: head yaw
			$this->location->yaw, //TODO: body yaw (wtf mojang?)
			array_map(function(Attribute $attr) : NetworkAttribute{
				return new NetworkAttribute($attr->getId(), $attr->getMinValue(), $attr->getMaxValue(), $attr->getValue(), $attr->getDefaultValue(), []);
			}, $this->attributeMap->getAll()),
			$this->getAllNetworkData(),
			new PropertySyncData([], []),
			[new EntityLink($this->player->getId(), $this->getId(), EntityLink::TYPE_PASSENGER, true, true)] //TODO: entity links
		));
	}

	public function getName() : string{
		return "hat";
	}
}