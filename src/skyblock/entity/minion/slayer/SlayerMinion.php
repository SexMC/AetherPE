<?php

declare(strict_types=1);

namespace skyblock\entity\minion\slayer;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\utils\SkullType;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\animation\DeathAnimation;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use skyblock\entity\minion\BaseMinion;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\Main;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\EntityUtils;

class SlayerMinion extends BaseMinion {

	protected AxisAlignedBB $bb;
	protected SlayerType $type;


	protected function onInitialize() : void{
		$v = $this->getPosition()->asVector3();
		$minX = min($v->x - 2, $v->x + 2);
		$maxX = max($v->x - 2, $v->x + 2);

		$minZ = min($v->z - 2, $v->z + 2);
		$maxZ = max($v->z - 2, $v->z + 2);

		$this->bb = new AxisAlignedBB($minX, $v->y - 2, $minZ, $maxX, $v->y + 3, $maxZ);


		if($type = MinionHandler::getInstance()->getSlayerType($this->getString("type", ""))){
			$this->type = $type;
		} else {
			$this->flagForDespawn();
			Main::debug("Could not find type for slayer minion. Despawning in world {$this->getWorld()->getFolderName()}");
		}

	}


	protected function setupAppearance() : void{
		$this->itemInHand = VanillaItems::STONE_SWORD();
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_HEAD, VanillaBlocks::MOB_HEAD()->setSkullType(SkullType::PLAYER())->asItem());
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_CHEST, VanillaItems::LEATHER_TUNIC()->setCustomColor(DyeColor::RED()->getRgbValue()));
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_LEGS, VanillaItems::LEATHER_PANTS()->setCustomColor(DyeColor::RED()->getRgbValue()));
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_FEET, VanillaItems::LEATHER_BOOTS()->setCustomColor(DyeColor::RED()->getRgbValue()));	}

	protected function onTick() : void{
		$y = (int) floor($this->bb->minY);
		for($x = $this->bb->minX; $x <= $this->bb->maxX; $x++){
			for($z = $this->bb->minZ; $z <= $this->bb->maxZ; $z++){
				$block1 = $this->getWorld()->getBlockAt((int) floor($x), $y + 2, (int) floor($z));
				$block2 = $this->getWorld()->getBlockAt((int) floor($x), $y + 3, (int) floor($z));

				if($block1->getId() === BlockLegacyIds::AIR && $block2->getId() === BlockLegacyIds::AIR){
					continue;
				}

				$this->setLocationPerfect(false);
				return;
			}
		}

		if(!$this->isLocationPerfect()){
			$this->setLocationPerfect(true);
		}

		$nearby = $this->getWorld()->getNearbyEntities($this->bb);
		/** @var Living $found */
		$found = null;
		foreach($nearby as $entity){
			if($entity::getNetworkTypeId() === $this->type->getEntityId() && !$entity->isClosed() && !$entity->isFlaggedForDespawn()){
				$found = $entity;
				break;
			}
		}

		if($found === null){
			$vec = new Vector3(
				mt_rand((int) floor($this->bb->minX), (int) floor($this->bb->maxX)),
				$this->getPosition()->getY(),
				mt_rand((int) floor($this->bb->minZ), (int) floor($this->bb->maxZ)),
			);

			EntityUtils::addEntity(Location::fromObject($vec->add(0.5, 0, 0.5), $this->getWorld()), $this->type->getEntityId(), 1);

			return;
		}

		//kill

		$drops = $found->getDrops();
		if(!$this->addItem($drops)){
			$this->setInventoryFull(true);
			return;
		}

		if($this->isInventoryFull()){
			$this->setInventoryFull(false);
		}

		$found->broadcastAnimation(new DeathAnimation($found));
		$found->flagForDespawn();

		$this->lookAt($found->getLocation());
		$this->reduceFuel($this->getSpeedInSeconds());
		$this->setInt("resources", $this->getInt("resources", 0) + 1);
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

		$generatedResources = (int) floor($total / $this->getSpeedInSeconds() / 2); //divide the total time by speed and then by two because it first spawns entity then kills it next tick time
		$e = EntityUtils::addEntity($this->getLocation(), $this->type->getEntityId(), 1);
		$drops = $e->getDrops();
		$e->flagForDespawn();

		foreach($drops as $drop){
			$drop->setCount($drop->getCount() * $generatedResources);
		}

		$this->addItem($drops, false);
		$this->setInt("resources", $this->getInt("resources") + $generatedResources);
		$this->reduceFuel($total * $this->getSpeedInSeconds());
	}

	public function getEggItem() : Item{
		$name = EntityUtils::getEntityNameFromID($this->type->getEntityId());

		$item = VanillaItems::VILLAGER_SPAWN_EGG();
		$item->setCustomName("§r§6" . $name . " Minion §c§l" . CustomEnchantUtils::roman($this->getInt("level")));
		$item->setLore([
			"§r§7Place this minion and it will",
			"§r§7start generating and slaying",
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

	protected function getSavingKeys() : array{
		return array_merge(["type"], parent::getSavingKeys());
	}

	public function setupMinionLevelInstance() : void{
		$this->level = SlayerMinionLevelInitializer::getInstance()->getLevel(EntityUtils::getEntityNameFromID($this->type->getEntityId()), $this->getInt("level"));
	}
}