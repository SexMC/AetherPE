<?php

declare(strict_types=1);

namespace skyblock\entity\minion\farmer;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use skyblock\entity\minion\MinionHandler;
use skyblock\entity\minion\MinionLevel;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedBakedPotato;
use skyblock\items\special\types\crafting\EnchantedCarrot;
use skyblock\items\special\types\crafting\EnchantedGoldenCarrot;
use skyblock\items\special\types\crafting\EnchantedHaybale;
use skyblock\items\special\types\crafting\EnchantedMelon;
use skyblock\items\special\types\crafting\EnchantedMelonBlock;
use skyblock\items\special\types\crafting\EnchantedPotato;
use skyblock\items\special\types\crafting\EnchantedPumpkin;
use skyblock\items\special\types\crafting\EnchantedSugar;
use skyblock\items\special\types\crafting\EnchantedSugarcane;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\CustomEnchantUtils;

class FarmingMinionLevelInitializer {
	use AetherHandlerTrait;

	private array $levels = [];

	public function onEnable() : void{
		$this->registerWheat();
		$this->registerSugarcane();
		$this->registerPotatoes();
		$this->registerCarrots();
		$this->registerMelons();
		$this->registerPumpkins();


		foreach($this->levels as $k => $v){
			$allEggs = [];
			$level = 1;
			/** @var MinionLevel $minionLevel */
			foreach($v as $minionLevel){
				$minion = $this->createNewMinion($level, MinionHandler::getInstance()->getFarmerType($k), "console");
				$egg = $minion->getEggItem();
				$allEggs[] = $egg;
				$minion->flagForDespawn();

				$array = [];
				for($i = 1; $i <= 9; $i++){
					$array[] = $minionLevel->getNeeds()[0];
				}

				if($level === 1){
					$array[4] = VanillaItems::WOODEN_HOE();
				} else {
					$array[4] = $allEggs[$level - 2];
				}

				RecipesHandler::getInstance()->registerRecipe("$k Minion " . CustomEnchantUtils::roman($level), $array, $egg, 0);
				$level++;
			}
		}
	}

	public function createNewMinion(int $level, FarmerType $type, string $owner, int $resources = 0): FarmerMinion {
		$nbt = new CompoundTag();
		$nbt->setInt("level", $level);
		$nbt->setInt("resources", $resources);
		$nbt->setString("owner", $owner);
		$nbt->setString("type", $type->getName());

		$pos = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
		$location = Location::fromObject($pos, $pos->getWorld());
		return new FarmerMinion($location, $nbt);
	}

	public function registerFarmerLevel(Block $block, int $level, float $baseSpeed, array $itemAroundIt): void {
		$this->levels[strtolower(str_replace(" ", "_", $block->getName()))][$level] = new MinionLevel($level, $baseSpeed, $itemAroundIt);
	}

	public function getLevel(Block $block, int $level): ?MinionLevel {
		return $this->levels[strtolower(str_replace(" ", "_", $block->getName()))][$level] ?? null;
	}

	public function getAllLevels() : array{
		return $this->levels;
	}

	public function registerPumpkins(): void {
		$item = VanillaBlocks::PUMPKIN();

		$this->registerFarmerLevel($item, 1, 32, [VanillaBlocks::PUMPKIN()->asItem()->setCount(10)]);
		$this->registerFarmerLevel($item, 2, 32, [VanillaBlocks::PUMPKIN()->asItem()->setCount(20)]);
		$this->registerFarmerLevel($item, 3, 30, [VanillaBlocks::PUMPKIN()->asItem()->setCount(40)]);
		$this->registerFarmerLevel($item, 4, 30, [VanillaBlocks::PUMPKIN()->asItem()->setCount(64)]);
		$this->registerFarmerLevel($item, 5, 27, [SkyblockItems::ENCHANTED_PUMPKIN()->setCount(1)]);
		$this->registerFarmerLevel($item, 6, 27, [SkyblockItems::ENCHANTED_PUMPKIN()->setCount(2)]);
		$this->registerFarmerLevel($item, 7, 24, [SkyblockItems::ENCHANTED_PUMPKIN()->setCount(4)]);
		$this->registerFarmerLevel($item, 8, 24, [SkyblockItems::ENCHANTED_PUMPKIN()->setCount(8)]);
		$this->registerFarmerLevel($item, 9, 20, [SkyblockItems::ENCHANTED_PUMPKIN()->setCount(16)]);
		$this->registerFarmerLevel($item, 10, 20, [SkyblockItems::ENCHANTED_PUMPKIN()->setCount(32)]);
		$this->registerFarmerLevel($item, 11, 16, [SkyblockItems::ENCHANTED_PUMPKIN()->setCount(64)]);
	}

	public function registerWheat(): void {
		$item = VanillaBlocks::WHEAT();

		$this->registerFarmerLevel($item, 1, 15, [VanillaItems::WHEAT()->setCount(10)]);
		$this->registerFarmerLevel($item, 2, 15, [VanillaItems::WHEAT()->setCount(20)]);
		$this->registerFarmerLevel($item, 3, 13, [VanillaItems::WHEAT()->setCount(40)]);
		$this->registerFarmerLevel($item, 4, 13, [VanillaItems::WHEAT()->setCount(64)]);
		$this->registerFarmerLevel($item, 5, 11, [VanillaBlocks::HAY_BALE()->asItem()->setCount(12)]);
		$this->registerFarmerLevel($item, 6, 11, [VanillaBlocks::HAY_BALE()->asItem()->setCount(24)]);
		$this->registerFarmerLevel($item, 7, 10, [VanillaBlocks::HAY_BALE()->asItem()->setCount(48)]);
		$this->registerFarmerLevel($item, 8, 10, [VanillaBlocks::HAY_BALE()->asItem()->setCount(64)]);
		$this->registerFarmerLevel($item, 9, 9, [SkyblockItems::ENCHANTED_HAY_BALE()->setCount(1)]);
		$this->registerFarmerLevel($item, 10, 9, [SkyblockItems::ENCHANTED_HAY_BALE()->setCount(2)]);
		$this->registerFarmerLevel($item, 11, 8, [SkyblockItems::ENCHANTED_HAY_BALE()->setCount(4)]);
	}

	public function registerCarrots(): void {
		$item = VanillaBlocks::CARROTS();

		$this->registerFarmerLevel($item, 1, 20, [VanillaItems::CARROT()->setCount(16)]);
		$this->registerFarmerLevel($item, 2, 20, [VanillaItems::CARROT()->setCount(32)]);
		$this->registerFarmerLevel($item, 3, 18, [VanillaItems::CARROT()->setCount(64)]);
		$this->registerFarmerLevel($item, 4, 18, [SkyblockItems::ENCHANTED_CARROT()->setCount(1)]);
		$this->registerFarmerLevel($item, 5, 16, [SkyblockItems::ENCHANTED_CARROT()->setCount(3)]);
		$this->registerFarmerLevel($item, 6, 16, [SkyblockItems::ENCHANTED_CARROT()->setCount(8)]);
		$this->registerFarmerLevel($item, 7, 14, [SkyblockItems::ENCHANTED_CARROT()->setCount(16)]);
		$this->registerFarmerLevel($item, 8, 14, [SkyblockItems::ENCHANTED_CARROT()->setCount(32)]);
		$this->registerFarmerLevel($item, 9, 12, [SkyblockItems::ENCHANTED_CARROT()->setCount(64)]);
		$this->registerFarmerLevel($item, 10, 12, [SkyblockItems::ENCHANTED_GOLDEN_CARROT()->setCount(1)]);
		$this->registerFarmerLevel($item, 11, 10, [SkyblockItems::ENCHANTED_GOLDEN_CARROT()->setCount(2)]);
	}

	public function registerMelons(): void {
		$item = VanillaBlocks::MELON();

		$this->registerFarmerLevel($item, 1, 24, [VanillaItems::MELON()->setCount(32)]);
		$this->registerFarmerLevel($item, 2, 24, [VanillaItems::MELON()->setCount(64)]);
		$this->registerFarmerLevel($item, 3, 22.5, [VanillaBlocks::MELON()->asItem()->setCount(16)]);
		$this->registerFarmerLevel($item, 4, 22.5, [VanillaBlocks::MELON()->asItem()->setCount(32)]);
		$this->registerFarmerLevel($item, 5, 21, [VanillaBlocks::MELON()->asItem()->setCount(64)]);
		$this->registerFarmerLevel($item, 6, 21, [SkyblockItems::ENCHANTED_MELON()->setCount(8)]);
		$this->registerFarmerLevel($item, 7, 18.5, [SkyblockItems::ENCHANTED_MELON()->setCount(16)]);
		$this->registerFarmerLevel($item, 8, 18.5, [SkyblockItems::ENCHANTED_MELON()->setCount(32)]);
		$this->registerFarmerLevel($item, 9, 16, [SkyblockItems::ENCHANTED_MELON()->setCount(64)]);
		$this->registerFarmerLevel($item, 10, 16, [SkyblockItems::ENCHANTED_MELON_BLOCK()->setCount(1)]);
		$this->registerFarmerLevel($item, 11, 13, [SkyblockItems::ENCHANTED_MELON_BLOCK()->setCount(2)]);
	}

	public function registerSugarcane(): void {
		$item = VanillaBlocks::SUGARCANE();

		$this->registerFarmerLevel($item, 1, 22, [VanillaBlocks::SUGARCANE()->asItem()->setCount(16)]);
		$this->registerFarmerLevel($item, 2, 22, [VanillaBlocks::SUGARCANE()->asItem()->setCount(32)]);
		$this->registerFarmerLevel($item, 3, 20, [VanillaBlocks::SUGARCANE()->asItem()->setCount(64)]);
		$this->registerFarmerLevel($item, 4, 20, [SkyblockItems::ENCHANTED_SUGAR()->setCount(1)]);
		$this->registerFarmerLevel($item, 5, 18, [SkyblockItems::ENCHANTED_SUGAR()->setCount(3)]);
		$this->registerFarmerLevel($item, 6, 18, [SkyblockItems::ENCHANTED_SUGAR()->setCount(8)]);
		$this->registerFarmerLevel($item, 7, 16, [SkyblockItems::ENCHANTED_SUGAR()->setCount(16)]);
		$this->registerFarmerLevel($item, 8, 16, [SkyblockItems::ENCHANTED_SUGAR()->setCount(32)]);
		$this->registerFarmerLevel($item, 9, 14.5, [SkyblockItems::ENCHANTED_SUGAR()->setCount(64)]);
		$this->registerFarmerLevel($item, 10, 14.5, [SkyblockItems::ENCHANTED_SUGARCANE()->setCount(1)]);
		$this->registerFarmerLevel($item, 11, 12, [SkyblockItems::ENCHANTED_SUGARCANE()->setCount(2)]);
	}

	public function registerPotatoes(): void {
		$item = VanillaBlocks::POTATOES();

		$this->registerFarmerLevel($item, 1, 20, [VanillaBlocks::POTATOES()->asItem()->setCount(16)]);
		$this->registerFarmerLevel($item, 2, 20, [VanillaBlocks::POTATOES()->asItem()->setCount(32)]);
		$this->registerFarmerLevel($item, 3, 18, [VanillaBlocks::POTATOES()->asItem()->setCount(64)]);
		$this->registerFarmerLevel($item, 4, 18, [SkyblockItems::ENCHANTED_POTATO()->setCount(1)]);
		$this->registerFarmerLevel($item, 5, 16, [SkyblockItems::ENCHANTED_POTATO()->setCount(3)]);
		$this->registerFarmerLevel($item, 6, 16, [SkyblockItems::ENCHANTED_POTATO()->setCount(8)]);
		$this->registerFarmerLevel($item, 7, 14, [SkyblockItems::ENCHANTED_POTATO()->setCount(16)]);
		$this->registerFarmerLevel($item, 8, 14, [SkyblockItems::ENCHANTED_POTATO()->setCount(32)]);
		$this->registerFarmerLevel($item, 9, 12, [SkyblockItems::ENCHANTED_SUGAR()->setCount(64)]);
		$this->registerFarmerLevel($item, 10, 12, [SkyblockItems::ENCHANTED_BAKED_POTATO()->setCount(1)]);
		$this->registerFarmerLevel($item, 11, 10, [SkyblockItems::ENCHANTED_BAKED_POTATO()->setCount(2)]);
	}
}