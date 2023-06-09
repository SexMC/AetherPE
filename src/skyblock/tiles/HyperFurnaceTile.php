<?php

declare(strict_types=1);

namespace skyblock\tiles;

use pocketmine\block\Furnace as BlockFurnace;
use pocketmine\block\inventory\FurnaceInventory;
use pocketmine\block\tile\Container;
use pocketmine\block\tile\ContainerTrait;
use pocketmine\block\tile\Furnace;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\crafting\FurnaceRecipe;
use pocketmine\crafting\FurnaceType;
use pocketmine\inventory\CallbackInventoryListener;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\player\Player;
use pocketmine\world\World;

class HyperFurnaceTile extends Spawnable implements Container, Nameable{
	use NameableTrait;
	use ContainerTrait;

	public const TAG_BURN_TIME = "BurnTime";
	public const TAG_COOK_TIME = "CookTime";
	public const TAG_MAX_TIME = "MaxTime";

	/** @var FurnaceInventory */
	protected $inventory;
	private int $remainingFuelTime = 0;
	private int $cookTime = 0;
	private int $maxFuelTime = 0;

	private int $cookDuration = 20;

	public bool $isHyperFurnace = false;

	public function __construct(World $world, Vector3 $pos){
		parent::__construct($world, $pos);
		$this->inventory = new FurnaceInventory($this->position, FurnaceType::FURNACE());
		$this->inventory->getListeners()->add(CallbackInventoryListener::onAnyChange(
			static function(Inventory $unused) use ($world, $pos) : void{
				$world->scheduleDelayedBlockUpdate($pos, 1);
			})
		);
	}

	public function readSaveData(CompoundTag $nbt) : void{
		$this->remainingFuelTime = max(0, $nbt->getShort(self::TAG_BURN_TIME, $this->remainingFuelTime));

		$this->cookTime = $nbt->getShort(self::TAG_COOK_TIME, $this->cookTime);
		if($this->remainingFuelTime === 0){
			$this->cookTime = 0;
		}

		$this->maxFuelTime = $nbt->getShort(self::TAG_MAX_TIME, $this->maxFuelTime);
		if($this->maxFuelTime === 0){
			$this->maxFuelTime = $this->remainingFuelTime;
		}

		$this->loadName($nbt);
		$this->loadItems($nbt);

		if($this->remainingFuelTime > 0){
			$this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
		}

		$this->isHyperFurnace = (bool) $nbt->getByte("is_hyper_furnace", 0);

		$this->setName($this->getDefaultName());
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		$nbt->setShort(self::TAG_BURN_TIME, $this->remainingFuelTime);
		$nbt->setShort(self::TAG_COOK_TIME, $this->cookTime);
		$nbt->setShort(self::TAG_MAX_TIME, $this->maxFuelTime);
		$nbt->setByte("is_hyper_furnace", (int) $this->isHyperFurnace);
		$this->saveName($nbt);
		$this->saveItems($nbt);
	}

	public function getDefaultName() : string{
		return $this->isHyperFurnace ? "§r§aHyper Furnace" : "Furnace";
	}

	public function close() : void{
		if(!$this->closed){
			$this->inventory->removeAllViewers();

			parent::close();
		}
	}

	/**
	 * @return FurnaceInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	/**
	 * @return FurnaceInventory
	 */
	public function getRealInventory(){
		return $this->getInventory();
	}

	protected function checkFuel(Item $fuel) : void{
		/*$ev = new FurnaceBurnEvent($this, $fuel, $fuel->getFuelTime());
		$ev->call();
		if($ev->isCancelled()){
			return;
		}*/

		$this->maxFuelTime = $this->remainingFuelTime =  $fuel->getFuelTime();
		$this->onStartSmelting();

		if($this->remainingFuelTime > 0){
			$this->inventory->setFuel($fuel->getFuelResidue());
		}
	}

	protected function onStartSmelting() : void{
		$block = $this->getBlock();
		if($block instanceof BlockFurnace && !$block->isLit()){
			$block->setLit(true);
			$this->position->getWorld()->setBlock($block->getPosition(), $block);
		}
	}

	protected function onStopSmelting() : void{
		$block = $this->getBlock();
		if($block instanceof BlockFurnace && $block->isLit()){
			$block->setLit(false);
			$this->position->getWorld()->setBlock($block->getPosition(), $block);
		}
	}

	//abstract public function getFurnaceType() : FurnaceType;

	public function onUpdate() : bool{
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$prevCookTime = $this->cookTime;
		$prevRemainingFuelTime = $this->remainingFuelTime;
		$prevMaxFuelTime = $this->maxFuelTime;

		$ret = false;

		$fuel = $this->inventory->getFuel();
		$raw = $this->inventory->getSmelting();
		$product = $this->inventory->getResult();

		$smelt = $this->position->getWorld()->getServer()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE())->match($raw);
		$canSmelt = ($smelt instanceof FurnaceRecipe && $raw->getCount() > 0 && (($smelt->getResult()->equals($product) && $product->getCount() < $product->getMaxStackSize()) || $product->isNull()));

		if($this->remainingFuelTime <= 0 && $canSmelt && $fuel->getFuelTime() > 0 && $fuel->getCount() > 0){
			$this->checkFuel($fuel);
		}

		if($this->remainingFuelTime > 0){
			--$this->remainingFuelTime;

			if($smelt instanceof FurnaceRecipe && $canSmelt){
				++$this->cookTime;

				$cook = $this->isHyperFurnace ? $this->cookDuration : FurnaceType::FURNACE()->getCookDurationTicks();

				if($this->cookTime >= $cook){
					$product = $smelt->getResult()->setCount($product->getCount() + 1);

					//$ev = new FurnaceSmeltEvent($this, $raw, $product);
					//$ev->call();

					//if(!$ev->isCancelled()){
						$this->inventory->setResult($product);
						$raw->pop();
						$this->inventory->setSmelting($raw);
					//}

					$this->cookTime -= $cook;
				}
			}elseif($this->remainingFuelTime <= 0){
				$this->remainingFuelTime = $this->cookTime = $this->maxFuelTime = 0;
			}else{
				$this->cookTime = 0;
			}
			$ret = true;
		}else{
			$this->onStopSmelting();
			$this->remainingFuelTime = $this->cookTime = $this->maxFuelTime = 0;
		}

		$viewers = array_map(fn(Player $p) => $p->getNetworkSession()->getInvManager(), $this->inventory->getViewers());
		foreach($viewers as $v){
			if($v === null){
				continue;
			}
			if($prevCookTime !== $this->cookTime){
				$v->syncData($this->inventory, ContainerSetDataPacket::PROPERTY_FURNACE_SMELT_PROGRESS, $this->cookTime);
			}
			if($prevRemainingFuelTime !== $this->remainingFuelTime){
				$v->syncData($this->inventory, ContainerSetDataPacket::PROPERTY_FURNACE_REMAINING_FUEL_TIME, $this->remainingFuelTime);
			}
			if($prevMaxFuelTime !== $this->maxFuelTime){
				$v->syncData($this->inventory, ContainerSetDataPacket::PROPERTY_FURNACE_MAX_FUEL_TIME, $this->maxFuelTime);
			}
		}

		$this->timings->stopTiming();

		return $ret;
	}



}