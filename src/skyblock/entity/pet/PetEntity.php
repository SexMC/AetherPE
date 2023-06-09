<?php

declare(strict_types=1);

namespace skyblock\entity\pet;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Attribute;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\Attribute as NetworkAttribute;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use skyblock\entity\EntityData;
use skyblock\items\pets\PetInstance;

class PetEntity extends Living implements EntityData{

	public PetGlassEntity $glass;
	private ?Vector3 $last = null;

	public function __construct(private Player $player, public string $networkID, public PetInstance $instance, Location $location, ?CompoundTag $nbt = null){ parent::__construct($location, $nbt); }


	protected function initEntity(CompoundTag $nbt) : void{
		$this->setCanSaveWithChunk(false);
		$this->setScale(0.5 / $this->getSize()->getHeight());
		$this->setHasGravity(false);

		parent::initEntity($nbt);

		$this->glass = $e = new PetGlassEntity($this->location, null, $this, VanillaBlocks::GLASS());
		$e->spawnToAll();
	}

	protected function syncNetworkData(EntityMetadataCollection $properties) : void{
		parent::syncNetworkData($properties);
		$properties->setByte(EntityMetadataFlags::SILENT, 1);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(self::HEIGHTS[self::NETWORK_IDS[self::LEGACY_ID_MAP_BC[$this->networkID]]], self::WIDTHS[self::NETWORK_IDS[self::LEGACY_ID_MAP_BC[$this->networkID]]]);
	}

	public static function getNetworkTypeId() : string{
		return "aetherpe:pet_entity";
	}

	public function getName() : string{
		return "Pet Entity";
	}



	public function onUpdate(int $currentTick) : bool{

		if($this->last){
			if($this->last->distanceSquared($this->player->getPosition()) < 3){

				return parent::onUpdate($currentTick);
			}
		}

		/** @var Vector3 $vec */
		$this->last = $vec = $this->player->getPosition()->asVector3();
		/** @var Player $player */
		switch($this->player->getHorizontalFacing()) {
			case Facing::NORTH:
				$vec = $vec->add(0, 0, 1);
				break;
			case Facing::SOUTH:
				$vec = $vec->add(0, 0, -1);
				break;
			case Facing::WEST:
				$vec = $vec->add(1, 0, 0);
				break;
			case Facing::EAST:
				$vec = $vec->add(-1, 0, 0);
		}

		$vec = $vec->add(0, 2.8, 0);
		$this->lookAt($this->player->getPosition());

		$this->teleport($vec);
		$this->glass->teleport($vec);

		return parent::onUpdate($currentTick);
	}


	public function attack(EntityDamageEvent $source) : void{
		//make it take no dmg
	}

	protected function sendSpawnPacket(Player $player) : void{
		$player->getNetworkSession()->sendDataPacket(AddActorPacket::create(
			$this->getId(),
			$this->getId(),
			$this->networkID,
			$this->location->asVector3(),
			$this->getMotion(),
			$this->location->pitch,
			$this->location->yaw,
			$this->location->yaw,
			$this->location->yaw,
			array_map(function(Attribute $attr) : NetworkAttribute{
				return new NetworkAttribute($attr->getId(), $attr->getMinValue(), $attr->getMaxValue(), $attr->getValue(), $attr->getDefaultValue(), []);
			}, $this->attributeMap->getAll()),
			$this->getAllNetworkData(),
			new PropertySyncData([], []),
			[]
		));

		$player->getNetworkSession()->onMobArmorChange($this);
	}
}