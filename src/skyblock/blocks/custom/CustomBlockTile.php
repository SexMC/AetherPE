<?php

declare(strict_types=1);

namespace skyblock\blocks\custom;

use pocketmine\block\tile\Tile;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use skyblock\entity\object\TextEntity;

class CustomBlockTile extends Tile {
	public const CUSTOM_BLOCK_TAG = "special_block_id";
	public const CUSTOM_BLOCK_DATA_TAG = "special_block_data";
	public const CUSTOM_BLOCK_TIME_PLACED_TAG = "special_block_time_placed";
	public const CUSTOM_BLOCK_LAST_TIME = "custom_block_last_time";
	public const CUSTOM_BLOCK_PLACER = "custom_block_placer";

	private string $specialId = "";
	private int $timePlaced;
	private int $lastTime; //last time something has been given
	private ?TextEntity $entityNameTag = null;
	private CompoundTag $blockData;
	private string $placer;

	public function __construct(World $world, Vector3 $pos) {
		parent::__construct($world, $pos);
		$this->blockData = new CompoundTag();
		$this->timePlaced = time();
		$this->lastTime = time();
	}

	public function getSpecialBlock() : ?CustomBlock {
		return CustomBlockHandler::getInstance()->getBlock($this->specialId);
	}

	public function getSpecialId() : string {
		return $this->specialId;
	}

	public function setSpecialId(string $id) : self {
		$this->specialId = $id;
		$this->updateNameTag();

		return $this;
	}

	public function getPlacer() : string{
		return $this->placer;
	}

	public function setPlacer(string $placer) : void{
		$this->placer = $placer;
	}

	public function getBlockData() : CompoundTag {
		return $this->blockData;
	}

	public function getTimePlaced() : int {
		return $this->timePlaced;
	}

	public function getLastTime() : int{
		return $this->lastTime;
	}

	public function setLastTime(int $lastTime) : void{
		$this->lastTime = $lastTime;

		$this->updateNameTag();
	}

	public function updateNameTag() : void {
		$entity = $this->entityNameTag;
		if ($this->entityNameTag === null) {
			$entity = new TextEntity(Location::fromObject($this->position->add(0.5, 0.9, 0.5), $this->position->getWorld()));
			$entity->setDespawnAfter(99999 * 20);
			$entity->spawnToAll();
		}

		$specialBlock = $this->getSpecialBlock();
		if ($specialBlock !== null) {
			$entity->setNameTag($specialBlock->getNameTag($this));
			$this->entityNameTag = $entity;
		}
	}

	public function readSaveData(CompoundTag $nbt) : void {
		$this->specialId = $nbt->getString(self::CUSTOM_BLOCK_TAG, "");
		$this->placer = $nbt->getString(self::CUSTOM_BLOCK_PLACER, "");
		$this->timePlaced = $nbt->getInt(self::CUSTOM_BLOCK_TIME_PLACED_TAG, time());
		$this->lastTime = $nbt->getInt(self::CUSTOM_BLOCK_LAST_TIME, time());
		$this->blockData = $nbt->getCompoundTag(self::CUSTOM_BLOCK_DATA_TAG) ?? new CompoundTag();
		$block = $this->getSpecialBlock();
		if ($block === null) {
			$this->close();
			return;
		}

		$this->updateNameTag();

		$block->load($this);
	}

	protected function writeSaveData(CompoundTag $nbt) : void {
		$nbt->setString(self::CUSTOM_BLOCK_TAG, $this->specialId);
		$nbt->setString(self::CUSTOM_BLOCK_PLACER, $this->placer);
		$nbt->setInt(self::CUSTOM_BLOCK_TIME_PLACED_TAG, $this->timePlaced);
		$nbt->setInt(self::CUSTOM_BLOCK_LAST_TIME, $this->lastTime);
		$nbt->setTag(self::CUSTOM_BLOCK_DATA_TAG, $this->blockData);
		if($this->entityNameTag?->isClosed() === false){
			$this->entityNameTag?->flagForDespawn();
		}
	}

	public function close() : void {
		if($this->entityNameTag?->isClosed() === false){
			$this->entityNameTag?->flagForDespawn();
		}
		parent::close();
	}
}