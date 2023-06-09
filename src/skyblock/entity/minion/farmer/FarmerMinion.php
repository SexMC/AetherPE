<?php

declare(strict_types=1);

namespace skyblock\entity\minion\farmer;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Crops;
use pocketmine\block\NetherWartPlant;
use pocketmine\block\Sugarcane;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\utils\SkullType;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\AxisAlignedBB;
use pocketmine\world\generator\hell\Nether;
use skyblock\blocks\custom\CustomBlockTile;
use skyblock\blocks\custom\types\FarmCrystalCustomBlock;
use skyblock\entity\minion\BaseMinion;
use skyblock\entity\minion\MinionHandler;
use skyblock\entity\minion\MinionLevel;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\SkyBlockHoe;
use skyblock\items\tools\types\pve\TreecapacitorAxe;
use skyblock\Main;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\CustomEnchantUtils;
use SOFe\AwaitGenerator\Await;

class FarmerMinion extends BaseMinion {
	use AwaitStdTrait;

	private FarmerType $type;
	private AxisAlignedBB $bb;
	private array $fastGrowBlocks = [];

	protected function onInitialize() : void{
		$v = $this->getPosition()->asVector3();
		$minX = min($v->x - 2, $v->x + 2);
		$maxX = max($v->x - 2, $v->x + 2);

		$minZ = min($v->z - 2, $v->z + 2);
		$maxZ = max($v->z - 2, $v->z + 2);

		$this->bb = new AxisAlignedBB($minX, $v->y - 1, $minZ, $maxX, $v->y - 1, $maxZ);


		if($type = MinionHandler::getInstance()->getFarmerType($this->getString("type", ""))){
			$this->type = $type;
		} else {
			$this->flagForDespawn();
			Main::debug("Could not find type for farmer minion. Despawning in world {$this->getWorld()->getFolderName()}");
		}


		Await::f2c(function() {
			while(!$this->isClosed() && $this->getWorld()->isLoaded()){
				yield $this->getStd()->sleep(mt_rand(15, 20));

				/** @var Crops|NetherWartPlant|Sugarcane $block */
				foreach($this->fastGrowBlocks as $k => $block){
					if($this->getWorld()->getBlock($block->getPosition()) !== $block){
						unset($this->fastGrowBlocks[$k]);
						continue;
					}

					if($block->getAge() < $block::MAX_AGE){
						$block->onRandomTick();
					} else unset($this->fastGrowBlocks[$k]);
				}
			}
		});
	}

	protected function setupAppearance() : void{
		$this->itemInHand = VanillaItems::WOODEN_HOE();
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_HEAD, VanillaBlocks::MOB_HEAD()->setSkullType(SkullType::PLAYER())->asItem());
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_CHEST, VanillaItems::LEATHER_TUNIC()->setCustomColor(DyeColor::LIME()->getRgbValue()));
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_LEGS, VanillaItems::LEATHER_PANTS()->setCustomColor(DyeColor::LIME()->getRgbValue()));
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_FEET, VanillaItems::LEATHER_BOOTS()->setCustomColor(DyeColor::LIME()->getRgbValue()));
	}

	public function setupMinionLevelInstance() : void{
		$this->level = FarmingMinionLevelInitializer::getInstance()->getLevel($this->type->getBlock(), $this->getInt("level"));
	}

	protected function onTick() : void{
		/** @var Block[] $plantedFarmlandBlocks */
		$plantedMaxFarmlandBlocks = [];
		/** @var Block[] $emptyFarmlandBlocks */
		$emptyFarmlandBlocks = [];
		/** @var Block[] $dirtBlocks */
		$dirtBlocks = [];

		$y = (int) floor($this->bb->minY);
		for($x = $this->bb->minX; $x <= $this->bb->maxX; $x++){
			for($z = $this->bb->minZ; $z <= $this->bb->maxZ; $z++){
				$xx = (int) floor($x);
				$zz = (int) floor($z);
				if($xx === $this->getPosition()->getFloorX() && $zz === $this->getPosition()->getFloorZ()) {
					continue;
				}

				$block = $this->getWorld()->getBlockAt($xx, $y, $zz);

				if($block->getId() === BlockLegacyIds::DIRT || $block->getId() === BlockLegacyIds::GRASS){
					if($this->type->getBlock()->getId() !== BlockLegacyIds::SUGARCANE_BLOCK){
						$dirtBlocks[] = $block;
						continue;
					}
				}

				if($block->getId() === BlockLegacyIds::FARMLAND || $block->getId() === BlockLegacyIds::DIRT || $block->getId() === BlockLegacyIds::GRASS){
					$above = $this->getWorld()->getBlockAt($xx, $y+1, $zz);
					if($above->getId() === BlockLegacyIds::AIR){
						$emptyFarmlandBlocks[] = $above;
						continue;
					}


					if($above->getId() === $this->type->getBlock()->getId()){
						/** @var Crops|NetherWartPlant|Sugarcane $above */
						if($above->getAge() === $above::MAX_AGE){
							$plantedMaxFarmlandBlocks[] = $above;
						} else {
							$this->fastGrowBlocks[] = $above;
						}

						continue;
					}

					$this->setLocationPerfect(false);
					return;
				}

				if($block->getId() === BlockLegacyIds::FLOWING_WATER || $block->getId() === BlockLegacyIds::STILL_WATER){
					continue;
				}

				$this->setLocationPerfect(false);
				return;
			}
		}

		if(!$this->isLocationPerfect()){
			$this->setLocationPerfect(true);
		}

		if(!empty($dirtBlocks)){
			$random = $dirtBlocks[array_rand($dirtBlocks)];
			$this->lookAt($random->getPosition());
			$this->reduceFuel($this->getSpeedInSeconds());

			$this->getWorld()->setBlock($random->getPosition(), VanillaBlocks::FARMLAND());
			return;
		}

		$random = null;

		if($random === null && !empty($emptyFarmlandBlocks)){
			$random = $emptyFarmlandBlocks[array_rand($emptyFarmlandBlocks)];
			$this->getWorld()->setBlock($random->getPosition(), BlockFactory::getInstance()->get($this->type->getBlock()->getId(), 0), false);
		}



		if($random === null && !empty($plantedMaxFarmlandBlocks)){
			/** @var Block $random */
			$random = $plantedMaxFarmlandBlocks[array_rand($plantedMaxFarmlandBlocks)];
			if(!$this->addItem($random->getDrops(VanillaItems::DIAMOND_HOE()))){
				$this->setInventoryFull(true);
				return;
			}

			if($this->isInventoryFull()){
				$this->setInventoryFull(false);
			}

			for($i = 1; $i <= 3; $i++){
				$add = $random->getPosition()->add(0, $i, 0);
				if($this->getWorld()->getBlock($add)->getId() === $this->type->getBlock()->getId()){
					$this->getWorld()->setBlock($add, VanillaBlocks::AIR());
					continue;
				}

				break;
			}

			$this->getWorld()->setBlock($random->getPosition(), VanillaBlocks::AIR());
		}

		if($random !== null){
			$this->lookAt($random->getPosition());
			$this->reduceFuel($this->getSpeedInSeconds());
			$this->setInt("resources", $this->getInt("resources", 0) + 1);
		}
	}

	protected function getSavingKeys() : array{
		return array_merge(["type"], parent::getSavingKeys());
	}

	public function getSpeedInTicks() : int{
		$decrease = 0;

		$pos = $this->getPosition();
		$chunk = $pos->getWorld()->getChunk($pos->x >> 4, $pos->z >> 4);
		if($chunk !== null){
			foreach ($chunk->getTiles() as $tile) {
				if (!$tile instanceof CustomBlockTile) {
					continue;
				}

				$sb = $tile->getSpecialBlock();
				if($sb instanceof FarmCrystalCustomBlock){
					$decrease = 2;
				}

			}
		}
		return $this->getSpeedInSeconds() * (20 - $decrease);
	}

	protected function doOfflineCalculations() : void{
		$lastSave = $this->getInt("lastSave", -1);
		if($lastSave === -1) return;

		$total = time() - $lastSave;
		if($total <= 0) return;

		$fuelLeft = $this->getTotalFuelTimeLeftInSeconds();
		if($total * $this->getSpeedInSeconds() > $fuelLeft){
			$total = (int) floor($fuelLeft / $this->getSpeedInSeconds());
		}


		$generatedResources = (int) floor($total / $this->getSpeedInSeconds() / 2); //divide the total time by speed and then by two because it first places the block then breaks it so it's actually twice the speed to gain a resource
		if($this->type instanceof Crops){
			$this->type->setAge(Crops::MAX_AGE);
		}
		$drops = $this->type->getBlock()->getDrops(VanillaItems::DIAMOND_HOE());

		foreach($drops as $drop){
			$drop->setCount($drop->getCount() * $generatedResources);
		}

		$this->addItem($drops, false);
		$this->setInt("resources", $this->getInt("resources") + $generatedResources);
		$this->reduceFuel($total * $this->getSpeedInSeconds());
	}

	public function getEggItem() : Item{
		$name = $this->type->getBlock()->asItem()->getName();

		$item = VanillaItems::VILLAGER_SPAWN_EGG();
		$item->setCustomName("§r§6" . $name . " Minion §c§l" . CustomEnchantUtils::roman($this->getInt("level")));
		$item->setLore([
			"§r§7Place this minion and it will",
			"§r§7start generating and farming",
			"§r§7{$name}!",
			"§r",
			"§r§7Requires an open",
			"§r§7area to place $name.",
			"§r",
			"§r§7Minions also work when you are",
			"§r§7offline!",
			"§r",
			"§r§7Time Between Actions: §a" . $this->getSpeedInSeconds() . "s",
			"§r§7Max Storage: §e" . $this->getStorageSizeByLevel($this->getInt("level")) * 64,
			"§r§7Resources Generated: §b" . $this->getInt("resources", 0),
		]);

		$item->getNamedTag()->setInt("level", $this->getInt("level"));
		$item->getNamedTag()->setInt("resources", $this->getInt("resources", 0));
		$item->getNamedTag()->setString("type", $this->type->getName());

		$item->getNamedTag()->setString(SpecialItem::TAG_SPECIAL_ITEM, MinionEgg::getItemTag());

		return $item;
	}
}