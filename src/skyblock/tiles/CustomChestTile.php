<?php

declare(strict_types=1);

namespace skyblock\tiles;

use pocketmine\block\tile\Chest;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\Position;
use pocketmine\world\World;
use skyblock\caches\block\ChunkChestCache;
use skyblock\entity\object\TextEntity;

class CustomChestTile extends Chest {

	const TAG_CHUNK_CHEST = "tag_chunk_chest";

	protected bool $isChunkChest = false;

	protected string $uniqueId;

	protected ?TextEntity $entity = null;
	
	public function __construct(World $world, Vector3 $pos){
		$this->uniqueId = uniqid();

		parent::__construct($world, $pos);

		if($this->isChunkChest()){
			$this->init();
		}
	}

	protected function init() {
		ChunkChestCache::getInstance()->add(CustomChestTile::getCacheIdentifier($this->position), $this, $this->uniqueId);

		$this->entity = new TextEntity(Location::fromObject($this->getPosition()->add(0.5, 1, 0.5), $this->getPosition()->getWorld()));
		$this->entity->setDespawnAfter(9999999999999);
		$this->entity->spawnToAll();
		$this->entity->setText("§a§lChunk Chest");

	}

	public function close() : void{
		if($this->isChunkChest()){
			ChunkChestCache::getInstance()->remove(CustomChestTile::getCacheIdentifier($this->position), $this->uniqueId);

			if($this->entity !== null){
				if(!$this->entity->isClosed()){
					if(!$this->entity->isFlaggedForDespawn()){
						$this->entity->flagForDespawn();
					}
				}
			}
		}

		parent::close();
	}

	public function setIsChunkChest(bool $isChunkChest) : void{
		$this->isChunkChest = $isChunkChest;

		if($this->isChunkChest()){
			$this->init();
		}
	}

	public function isChunkChest() : bool{
		return $this->isChunkChest;
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		$nbt->setByte(self::TAG_CHUNK_CHEST, (int) $this->isChunkChest());
		parent::writeSaveData($nbt);
	}

	public function readSaveData(CompoundTag $nbt) : void{
		$this->setIsChunkChest((bool) $nbt->getByte(self::TAG_CHUNK_CHEST, 0));
		parent::readSaveData($nbt);
	}

	public static function getCacheIdentifier(Position $pos): string {
		return $pos->getWorld()->getDisplayName() . ($pos->getFloorX() >> 4) . ($pos->getFloorZ() >> 4);
	}
}