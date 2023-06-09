<?php

declare(strict_types=1);

namespace skyblock\entity\minion\foraging;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\utils\SkullType;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Axe;
use pocketmine\item\Item;
use pocketmine\item\Pickaxe;
use pocketmine\item\VanillaItems;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use skyblock\entity\minion\BaseMinion;
use skyblock\entity\minion\farmer\FarmingMinionLevelInitializer;
use skyblock\entity\minion\foraging\ForagerType;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItems;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\SkyBlockAxe;
use skyblock\Main;
use skyblock\menus\minions\MinionMenu;
use skyblock\utils\CustomEnchantUtils;

class ForagingMinion extends BaseMinion {

	protected AxisAlignedBB $bb;
	protected Block $block;

	protected ForagerType $type;

	protected function onInitialize() : void{
		$v = $this->getPosition()->asVector3();
		$minX = min($v->x - 2, $v->x + 2);
		$maxX = max($v->x - 2, $v->x + 2);

		$minZ = min($v->z - 2, $v->z + 2);
		$maxZ = max($v->z - 2, $v->z + 2);

		$this->bb = new AxisAlignedBB($minX, $v->y - 1, $minZ, $maxX, $v->y - 1, $maxZ);


		if($type = MinionHandler::getInstance()->getForagerType($this->getString("type", ""))){
			$this->type = $type;
		} else {
			$this->flagForDespawn();
			Main::debug("Could not find type for foraging minion. Despawning in world {$this->getWorld()->getFolderName()}");
		}
	}

	public function setupMinionLevelInstance() : void{
		$this->level = ForagingMinionLevelInitializer::getInstance()->getLevel($this->type->getBlock(), $this->getInt("level"));
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
		$drops = $this->type->getBlock()->getDrops(VanillaItems::DIAMOND_AXE());

		foreach($drops as $drop){
			$drop->setCount($drop->getCount() * $generatedResources);
		}

		$this->addItem($drops, false);
		$this->setInt("resources", $this->getInt("resources") + $generatedResources);
		$this->reduceFuel($total * $this->getSpeedInSeconds());
	}

	protected function onTick() : void{
		/** @var Block[] $ownBlocks */
		$ownBlocks = [];
		/** @var Block[] $airBlocks */
		$airBlocks = [];

		$y = (int) floor($this->bb->minY);
		for($x = $this->bb->minX; $x <= $this->bb->maxX; $x++){
			for($z = $this->bb->minZ; $z <= $this->bb->maxZ; $z++){
				$block = $this->getWorld()->getBlockAt((int) floor($x), $y, (int) floor($z));

				if($block->getId() === BlockLegacyIds::AIR){
					$airBlocks[] = $block;
					continue;
				}

				if($block->getId() === $this->type->getBlock()->getId()){
					$ownBlocks[] = $block;
					continue;
				}

				$this->setLocationPerfect(false);
				return;
			}
		}

		if(!$this->isLocationPerfect()){
			$this->setLocationPerfect(true);
		}


		if(!empty($airBlocks)){
			$random = $airBlocks[array_rand($airBlocks)];
			$this->getWorld()->setBlock($random->getPosition(), $this->type->getBlock());
			$this->lookAt($random->getPosition());
			$this->reduceFuel($this->getSpeedInSeconds());

			return;
		}

		$random = $ownBlocks[array_rand($ownBlocks)];
		if(!$this->addItem($random->getDrops(VanillaItems::DIAMOND_AXE()))){
			$this->setInventoryFull(true);
			return;
		}

		if($this->isInventoryFull()){
			$this->setInventoryFull(false);
		}

		$this->getWorld()->setBlock($random->getPosition(), VanillaBlocks::AIR());
		$this->lookAt($random->getPosition());
		$this->reduceFuel($this->getSpeedInSeconds());
		$this->setInt("resources", $this->getInt("resources", 0) + 1);
	}

	protected function setupAppearance() : void{
		$this->itemInHand = VanillaItems::WOODEN_AXE();
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_HEAD, VanillaBlocks::MOB_HEAD()->setSkullType(SkullType::PLAYER())->asItem());
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_CHEST, VanillaItems::LEATHER_TUNIC()->setCustomColor(DyeColor::GRAY()->getRgbValue()));
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_LEGS, VanillaItems::LEATHER_PANTS()->setCustomColor(DyeColor::GRAY()->getRgbValue()));
		$this->getArmorInventory()->setItem(ArmorInventory::SLOT_FEET, VanillaItems::LEATHER_BOOTS()->setCustomColor(DyeColor::GRAY()->getRgbValue()));
	}

	protected function getSavingKeys() : array{
		return array_merge(parent::getSavingKeys(), [
			"type",
		]);
	}

	public function getEggItem() : Item{
		$name = $this->type->getBlock()->asItem()->getName();

		$item = VanillaItems::VILLAGER_SPAWN_EGG();
		$item->setCustomName("§r§6" . $name . " Minion §c§l" . CustomEnchantUtils::roman($this->getInt("level")));
		$item->setLore([
			"§r§7Place this minion and it will",
			"§r§7start generating and chopping",
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