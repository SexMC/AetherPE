<?php

declare(strict_types=1);

namespace skyblock\entity\minion\fishing;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\utils\SkullType;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Water;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\world\particle\BubbleParticle;
use skyblock\blocks\custom\CustomBlockTile;
use skyblock\blocks\custom\types\FarmCrystalCustomBlock;
use skyblock\entity\minion\BaseMinion;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\ItemEditor;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\Main;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\CustomEnchantUtils;

class FishingMinion extends BaseMinion {
	use AwaitStdTrait;

	private FishingType $type;
	private AxisAlignedBB $bb;
	private array $fastGrowBlocks = [];

	protected function onInitialize() : void{
		$v = $this->getPosition()->asVector3();
		$minX = min($v->x - 2, $v->x + 2);
		$maxX = max($v->x - 2, $v->x + 2);

		$minZ = min($v->z - 2, $v->z + 2);
		$maxZ = max($v->z - 2, $v->z + 2);

		$this->bb = new AxisAlignedBB($minX, $v->y - 1, $minZ, $maxX, $v->y - 1, $maxZ);


		if($type = MinionHandler::getInstance()->getFishingType($this->getString("type", ""))){
			$this->type = $type;
		} else {
			$this->flagForDespawn();
			Main::debug("Could not find type for farmer minion. Despawning in world {$this->getWorld()->getFolderName()}");
		}

	}

	protected function setupAppearance() : void{
		$this->itemInHand = ItemEditor::glow(VanillaItems::FISHING_ROD());
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_HEAD, VanillaBlocks::MOB_HEAD()->setSkullType(SkullType::PLAYER())->asItem());
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_CHEST, VanillaItems::LEATHER_TUNIC()->setCustomColor(DyeColor::LIME()->getRgbValue()));
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_LEGS, VanillaItems::LEATHER_PANTS()->setCustomColor(DyeColor::LIME()->getRgbValue()));
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_FEET, VanillaItems::LEATHER_BOOTS()->setCustomColor(DyeColor::LIME()->getRgbValue()));
	}

	public function setupMinionLevelInstance() : void{
		$this->level = FishingMinionLevelInitializer::getInstance()->getLevel($this->type->getItem(), $this->getInt("level"));
	}

	protected function onTick() : void{
		$random = null;


		$y = (int) floor($this->bb->minY);
		for($x = $this->bb->minX; $x <= $this->bb->maxX; $x++){
			for($z = $this->bb->minZ; $z <= $this->bb->maxZ; $z++){
				$xx = (int) floor($x);
				$zz = (int) floor($z);

				$block = $this->getWorld()->getBlockAt($xx, $y, $zz);

				if(!$block instanceof Water){
					$this->setLocationPerfect(false);
					return;
				}

				if($random === null){
					$random = new Vector3($xx, $y, $zz);
				} else if(mt_rand(1, 5) === 1){
					$random = new Vector3($xx, $y, $zz);
				}
			}
		}

		if(!$this->isLocationPerfect()){
			$this->setLocationPerfect(true);
		}

		$catch = null;
		foreach(FishingMinionLevelInitializer::getInstance()->getRandomLoottable()->generate(1) as $v){
			$catch = $v;
		}

		if(!$catch instanceof Item){
			Main::getInstance()->getLogger()->error("Invalid fishing loottable, got null");
			return;
		}

		if(!$this->addItem($catch)){
			$this->setInventoryFull(true);
			return;
		}

		if($this->isInventoryFull()){
			$this->setInventoryFull(false);
		}

		$this->lookAt($random);
		$this->reduceFuel($this->getSpeedInSeconds());
		$this->setInt("resources", $this->getInt("resources", 0) + 1);

		for($i = 0; $i <= 3; $i++){
			$this->getWorld()->addParticle($random->add(mt_rand(1, 10) / (mt_rand(1, 2) === 1 ? -10 : 10), 1, mt_rand(1, 10) / (mt_rand(1, 2) === 1 ? -10 : 10)), new BubbleParticle());
		}
	}


	protected function getSavingKeys() : array{
		return array_merge(["type"], parent::getSavingKeys());
	}

	public function getSpeedInTicks() : int{
		$decrease = 0;

		$pos = $this->getPosition();
		$chunk = $pos->getWorld()->getChunk($pos->x >> 4, $pos->z >> 4);
		foreach ($chunk->getTiles() as $tile) {
			if (!$tile instanceof CustomBlockTile) {
				continue;
			}

			$sb = $tile->getSpecialBlock();
			if($sb instanceof FarmCrystalCustomBlock){
				$decrease = 2;
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
		$drops = [];

		foreach(FishingMinionLevelInitializer::getInstance()->getRandomLoottable()->generate($generatedResources) as $v){
			$drops[] = $v;
		}

		$this->addItem($drops, false);
		$this->setInt("resources", $this->getInt("resources") + $generatedResources);
		$this->reduceFuel($total * $this->getSpeedInSeconds());
	}

	public function getEggItem() : Item{
		$name = $this->type->getItem()->getName();

		$item = VanillaItems::VILLAGER_SPAWN_EGG();
		$item->setCustomName("§r§6" . $name . " Minion §c§l" . CustomEnchantUtils::roman($this->getInt("level")));
		$item->setLore([
			"§r§7Place this minion and it will",
			"§r§7start fishing. Requires water",
			"§r§7nearby.",
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