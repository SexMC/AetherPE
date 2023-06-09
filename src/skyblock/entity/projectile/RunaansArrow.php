<?php

declare(strict_types=1);

namespace skyblock\entity\projectile;

use pathfinder\algorithm\AlgorithmSettings;
use pathfinder\entity\Navigator;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\Server;
use skyblock\entity\boss\PveEntity;
use Throwable;

class RunaansArrow extends Living {

	private Navigator $navigator;

	public function __construct(private PveEntity $target, Location $location, ?CompoundTag $nbt = null){
		parent::__construct($location, $nbt);
		$this->navigator = new Navigator($this, null, null,
			(new AlgorithmSettings())
				->setTimeout(1)
				->setMaxTicks(5)
		);

		$this->navigator->setSpeed(1.3);
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setCanSaveWithChunk(false);
	}



	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0.5, 0.5);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::ARROW;
	}

	public function onUpdate(int $currentTick) : bool{
		if($this->target !== null){
			$distance = $this->getPosition()->distance($this->target->getPosition());
			if($distance < 20 ){
				if($this->ticksLived % 10 === 0 && $distance > 1){
					$this->navigator->setTargetVector3($this->target->getPosition());
				}

				try {
					$this->navigator->onUpdate();
					$this->lookAt($this->target->getPosition());
				} catch (Throwable $e) {
					$this->flagForDespawn();
					Server::getInstance()->getLogger()->logException($e);
				}
			}
		}

		return parent::onUpdate($currentTick);
	}

	public function getName() : string{
		return "Runaans Arrow";
	}
}