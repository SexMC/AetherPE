<?php

declare(strict_types=1);

namespace skyblock\entity\minion\miner;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use skyblock\entity\minion\farmer\FarmerMinion;
use skyblock\entity\minion\farmer\FarmerType;
use skyblock\entity\minion\MinionHandler;
use skyblock\entity\minion\MinionLevel;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedClay;
use skyblock\items\special\types\crafting\EnchantedCoal;
use skyblock\items\special\types\crafting\EnchantedCoalBlock;
use skyblock\items\special\types\crafting\EnchantedCobblestone;
use skyblock\items\special\types\crafting\EnchantedDiamond;
use skyblock\items\special\types\crafting\EnchantedDiamondBlock;
use skyblock\items\special\types\crafting\EnchantedEmerald;
use skyblock\items\special\types\crafting\EnchantedEmeraldBlock;
use skyblock\items\special\types\crafting\EnchantedFlint;
use skyblock\items\special\types\crafting\EnchantedGold;
use skyblock\items\special\types\crafting\EnchantedGoldBlock;
use skyblock\items\special\types\crafting\EnchantedIron;
use skyblock\items\special\types\crafting\EnchantedIronBlock;
use skyblock\items\special\types\crafting\EnchantedLapis;
use skyblock\items\special\types\crafting\EnchantedLapisBlock;
use skyblock\items\special\types\crafting\EnchantedObsidian;
use skyblock\items\special\types\crafting\EnchantedRedstone;
use skyblock\items\special\types\crafting\EnchantedRedstoneBlock;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\CustomEnchantUtils;

class MinerMinionLevelInitializer {
	use AetherHandlerTrait; //TODO:

	private array $levels = [];

	public function onEnable() : void{
		$this->registerCoal();
		$this->registerCobblestone();
		$this->registerDiamond();
		$this->registerEmerald();
		$this->registerGold();
		$this->registerIron();
		$this->registerLapis();
		$this->registerRedstone();
		$this->registerObby();
		$this->registerClay();
		$this->registerGravel();

		foreach($this->levels as $k => $v){
			$allEggs = [];
			$level = 1;
			/** @var MinionLevel $minionLevel */
			foreach($v as $minionLevel){
				$minion = $this->createNewMinion($level, MinionHandler::getInstance()->getMinerType($k), "console");
				$egg = $minion->getEggItem();
				$allEggs[] = $egg;
				$minion->flagForDespawn();

				$array = [];
				for($i = 1; $i <= 9; $i++){
					$array[] = $minionLevel->getNeeds()[0];
				}

				if($level === 1){
					$array[4] = VanillaItems::WOODEN_PICKAXE();
				} else {
					$array[4] = $allEggs[$level - 2];
				}

				RecipesHandler::getInstance()->registerRecipe("$k Minion " . CustomEnchantUtils::roman($level), $array, $egg, 0);
				$level++;
			}
		}
	}

	public function createNewMinion(int $level, MinerType $type, string $owner, int $resources = 0): MinerMinion {
		$nbt = new CompoundTag();
		$nbt->setInt("level", $level);
		$nbt->setInt("resources", $resources);
		$nbt->setString("owner", $owner);
		$nbt->setString("type", $type->getName());

		$pos = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
		$location = Location::fromObject($pos, $pos->getWorld());
		return new MinerMinion($location, $nbt);
	}

	//this is a minion for the fishing collection
	public function registerClay(): void {
		$item = VanillaBlocks::CLAY();

		$this->registerMinerLevel($item, 1, 29, [VanillaItems::CLAY()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 29, [VanillaItems::CLAY()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 27, [VanillaItems::CLAY()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 27, [VanillaItems::CLAY()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 25, [SkyblockItems::ENCHANTED_CLAY()->setCount(1)]);
		$this->registerMinerLevel($item, 6, 25, [SkyblockItems::ENCHANTED_CLAY()->setCount(2)]);
		$this->registerMinerLevel($item, 7, 23, [SkyblockItems::ENCHANTED_CLAY()->setCount(4)]);
		$this->registerMinerLevel($item, 8, 23, [SkyblockItems::ENCHANTED_CLAY()->setCount(8)]);
		$this->registerMinerLevel($item, 9, 21, [SkyblockItems::ENCHANTED_CLAY()->setCount(16)]);
		$this->registerMinerLevel($item, 10, 21, [SkyblockItems::ENCHANTED_CLAY()->setCount(32)]);
		$this->registerMinerLevel($item, 11, 18, [SkyblockItems::ENCHANTED_CLAY()->setCount(64)]);

	}

	public function registerGravel(): void {
		$item = VanillaBlocks::GRAVEL();

		$this->registerMinerLevel($item, 1, 29, [VanillaBlocks::GRAVEL()->asItem()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 29, [VanillaBlocks::GRAVEL()->asItem()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 27, [VanillaBlocks::GRAVEL()->asItem()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 27, [VanillaBlocks::GRAVEL()->asItem()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 25, [SkyblockItems::ENCHANTED_FLINT()->setCount(1)]);
		$this->registerMinerLevel($item, 6, 25, [SkyblockItems::ENCHANTED_FLINT()->setCount(2)]);
		$this->registerMinerLevel($item, 7, 23, [SkyblockItems::ENCHANTED_FLINT()->setCount(4)]);
		$this->registerMinerLevel($item, 8, 23, [SkyblockItems::ENCHANTED_FLINT()->setCount(8)]);
		$this->registerMinerLevel($item, 9, 21, [SkyblockItems::ENCHANTED_FLINT()->setCount(16)]);
		$this->registerMinerLevel($item, 10, 21, [SkyblockItems::ENCHANTED_FLINT()->setCount(32)]);
		$this->registerMinerLevel($item, 11, 18, [SkyblockItems::ENCHANTED_FLINT()->setCount(64)]);

	}


	public function registerRedstone(): void {
		$item = VanillaBlocks::REDSTONE_ORE();

		$this->registerMinerLevel($item, 1, 29, [VanillaItems::REDSTONE_DUST()->setCount(32)]);
		$this->registerMinerLevel($item, 2, 29, [VanillaItems::REDSTONE_DUST()->setCount(64)]);
		$this->registerMinerLevel($item, 3, 27, [SkyblockItems::ENCHANTED_REDSTONE()->setCount(1)]);
		$this->registerMinerLevel($item, 4, 27, [SkyblockItems::ENCHANTED_REDSTONE()->setCount(3)]);
		$this->registerMinerLevel($item, 5, 25, [SkyblockItems::ENCHANTED_REDSTONE()->setCount(8)]);
		$this->registerMinerLevel($item, 6, 25, [SkyblockItems::ENCHANTED_REDSTONE()->setCount(16)]);
		$this->registerMinerLevel($item, 7, 23, [SkyblockItems::ENCHANTED_REDSTONE()->setCount(32)]);
		$this->registerMinerLevel($item, 8, 23, [SkyblockItems::ENCHANTED_REDSTONE()->setCount(64)]);
		$this->registerMinerLevel($item, 9, 21, [SkyblockItems::ENCHANTED_REDSTONE_BLOCK()->setCount(1)]);
		$this->registerMinerLevel($item, 10, 21, [SkyblockItems::ENCHANTED_REDSTONE_BLOCK()->setCount(2)]);
		$this->registerMinerLevel($item, 11, 18, [SkyblockItems::ENCHANTED_REDSTONE_BLOCK()->setCount(4)]);
	}

	public function registerLapis(): void {
		$item = VanillaBlocks::LAPIS_LAZULI_ORE();

		$this->registerMinerLevel($item, 1, 29, [VanillaItems::LAPIS_LAZULI()->setCount(32)]);
		$this->registerMinerLevel($item, 2, 29, [VanillaItems::LAPIS_LAZULI()->setCount(64)]);
		$this->registerMinerLevel($item, 3, 27, [SkyblockItems::ENCHANTED_LAPIS_LAZULI()->setCount(1)]);
		$this->registerMinerLevel($item, 4, 27, [SkyblockItems::ENCHANTED_LAPIS_LAZULI()->setCount(3)]);
		$this->registerMinerLevel($item, 5, 25, [SkyblockItems::ENCHANTED_LAPIS_LAZULI()->setCount(8)]);
		$this->registerMinerLevel($item, 6, 25, [SkyblockItems::ENCHANTED_LAPIS_LAZULI()->setCount(16)]);
		$this->registerMinerLevel($item, 7, 23, [SkyblockItems::ENCHANTED_LAPIS_LAZULI()->setCount(32)]);
		$this->registerMinerLevel($item, 8, 23, [SkyblockItems::ENCHANTED_LAPIS_LAZULI()->setCount(64)]);
		$this->registerMinerLevel($item, 9, 21, [SkyblockItems::ENCHANTED_LAPIS_BLOCK()->setCount(1)]);
		$this->registerMinerLevel($item, 10, 21,[SkyblockItems::ENCHANTED_LAPIS_BLOCK()->setCount(2)]);
		$this->registerMinerLevel($item, 11, 18,[SkyblockItems::ENCHANTED_LAPIS_BLOCK()->setCount(4)]);
	}

	public function registerCoal(): void {
		$item = VanillaBlocks::COAL_ORE();

		$this->registerMinerLevel($item, 1, 15, [VanillaItems::COAL()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 15, [VanillaItems::COAL()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 13, [VanillaItems::COAL()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 13, [VanillaItems::COAL()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 12, [SkyblockItems::ENCHANTED_COAL()]);
		$this->registerMinerLevel($item, 6, 12, [SkyblockItems::ENCHANTED_COAL()->setCount(3)]);
		$this->registerMinerLevel($item, 7, 10, [SkyblockItems::ENCHANTED_COAL()->setCount(8)]);
		$this->registerMinerLevel($item, 8, 10, [SkyblockItems::ENCHANTED_COAL()->setCount(16)]);
		$this->registerMinerLevel($item, 9, 9, [SkyblockItems::ENCHANTED_COAL()->setCount(32)]);
		$this->registerMinerLevel($item, 10, 9, [SkyblockItems::ENCHANTED_COAL()->setCount(64)]);
		$this->registerMinerLevel($item, 11, 7, [SkyblockItems::ENCHANTED_COAL_BLOCK()]);
	}

	public function registerIron(): void {
		$item = VanillaBlocks::IRON_ORE();

		$this->registerMinerLevel($item, 1, 17, [VanillaItems::IRON_INGOT()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 17, [VanillaItems::IRON_INGOT()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 15, [VanillaItems::IRON_INGOT()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 15, [VanillaItems::IRON_INGOT()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 14, [SkyblockItems::ENCHANTED_IRON()]);
		$this->registerMinerLevel($item, 6, 14, [SkyblockItems::ENCHANTED_IRON()->setCount(3)]);
		$this->registerMinerLevel($item, 7, 12, [SkyblockItems::ENCHANTED_IRON()->setCount(8)]);
		$this->registerMinerLevel($item, 8, 12, [SkyblockItems::ENCHANTED_IRON()->setCount(16)]);
		$this->registerMinerLevel($item, 9, 10, [SkyblockItems::ENCHANTED_IRON()->setCount(32)]);
		$this->registerMinerLevel($item, 10, 10,[SkyblockItems::ENCHANTED_IRON()->setCount(64)]);
		$this->registerMinerLevel($item, 11, 8, [SkyblockItems::ENCHANTED_IRON_BLOCK()]);
	}

	public function registerObby(): void {
		$item = VanillaBlocks::OBSIDIAN();

		$this->registerMinerLevel($item, 1, 45, [VanillaBlocks::OBSIDIAN()->asItem()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 45, [VanillaBlocks::OBSIDIAN()->asItem()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 42, [VanillaBlocks::OBSIDIAN()->asItem()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 42, [VanillaBlocks::OBSIDIAN()->asItem()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 39, [SkyblockItems::ENCHANTED_OBSIDIAN()->setCount(1)]);
		$this->registerMinerLevel($item, 6, 39, [SkyblockItems::ENCHANTED_OBSIDIAN()->setCount(2)]);
		$this->registerMinerLevel($item, 7, 35, [SkyblockItems::ENCHANTED_OBSIDIAN()->setCount(4)]);
		$this->registerMinerLevel($item, 8, 35, [SkyblockItems::ENCHANTED_OBSIDIAN()->setCount(8)]);
		$this->registerMinerLevel($item, 9, 30, [SkyblockItems::ENCHANTED_OBSIDIAN()->setCount(16)]);
		$this->registerMinerLevel($item, 10, 30, [SkyblockItems::ENCHANTED_OBSIDIAN()->setCount(32)]);
		$this->registerMinerLevel($item, 11, 24, [SkyblockItems::ENCHANTED_OBSIDIAN()->setCount(64)]);
	}

	public function registerDiamond(): void {
		$item = VanillaBlocks::DIAMOND_ORE();

		$this->registerMinerLevel($item, 1, 29, [VanillaItems::DIAMOND()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 29, [VanillaItems::DIAMOND()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 27, [VanillaItems::DIAMOND()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 27, [VanillaItems::DIAMOND()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 25, [SkyblockItems::ENCHANTED_DIAMOND()]);
		$this->registerMinerLevel($item, 6, 25, [SkyblockItems::ENCHANTED_DIAMOND()->setCount(3)]);
		$this->registerMinerLevel($item, 7, 22, [SkyblockItems::ENCHANTED_DIAMOND()->setCount(8)]);
		$this->registerMinerLevel($item, 8, 22, [SkyblockItems::ENCHANTED_DIAMOND()->setCount(16)]);
		$this->registerMinerLevel($item, 9, 19, [SkyblockItems::ENCHANTED_DIAMOND()->setCount(32)]);
		$this->registerMinerLevel($item, 10, 19, [SkyblockItems::ENCHANTED_DIAMOND()->setCount(64)]);
		$this->registerMinerLevel($item, 11, 15, [SkyblockItems::ENCHANTED_DIAMOND_BLOCK()]);
	}

	public function registerEmerald(): void {
		$item = VanillaBlocks::EMERALD_ORE();

		$this->registerMinerLevel($item, 1, 28, [VanillaItems::EMERALD()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 28, [VanillaItems::EMERALD()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 26, [VanillaItems::EMERALD()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 26, [VanillaItems::EMERALD()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 24, [SkyblockItems::ENCHANTED_EMERALD()]);
		$this->registerMinerLevel($item, 6, 24, [SkyblockItems::ENCHANTED_EMERALD()->setCount(3)]);
		$this->registerMinerLevel($item, 7, 21, [SkyblockItems::ENCHANTED_EMERALD()->setCount(8)]);
		$this->registerMinerLevel($item, 8, 21, [SkyblockItems::ENCHANTED_EMERALD()->setCount(16)]);
		$this->registerMinerLevel($item, 9, 17, [SkyblockItems::ENCHANTED_EMERALD()->setCount(32)]);
		$this->registerMinerLevel($item, 10, 17,[SkyblockItems::ENCHANTED_EMERALD()->setCount(64)]);
		$this->registerMinerLevel($item, 11, 14,[SkyblockItems::ENCHANTED_EMERALD_BLOCK()]);
	}

	public function registerGold(): void {
		$item = VanillaBlocks::GOLD_ORE();

		$this->registerMinerLevel($item, 1, 22, [VanillaItems::GOLD_INGOT()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 22, [VanillaItems::GOLD_INGOT()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 20, [VanillaItems::GOLD_INGOT()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 20, [VanillaItems::GOLD_INGOT()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 18, [SkyblockItems::ENCHANTED_GOLD()]);
		$this->registerMinerLevel($item, 6, 18, [SkyblockItems::ENCHANTED_GOLD()->setCount(3)]);
		$this->registerMinerLevel($item, 7, 16, [SkyblockItems::ENCHANTED_GOLD()->setCount(8)]);
		$this->registerMinerLevel($item, 8, 16, [SkyblockItems::ENCHANTED_GOLD()->setCount(16)]);
		$this->registerMinerLevel($item, 9, 14, [SkyblockItems::ENCHANTED_GOLD()->setCount(32)]);
		$this->registerMinerLevel($item, 10, 14,[SkyblockItems::ENCHANTED_GOLD()->setCount(64)]);
		$this->registerMinerLevel($item, 11, 11,[SkyblockItems::ENCHANTED_GOLD_BLOCK()]);
	}


	public function registerCobblestone(): void {
		$item = VanillaBlocks::COBBLESTONE();

		$this->registerMinerLevel($item, 1, 15, [VanillaBlocks::COBBLESTONE()->asItem()->setCount(10)]);
		$this->registerMinerLevel($item, 2, 15, [VanillaBlocks::COBBLESTONE()->asItem()->setCount(20)]);
		$this->registerMinerLevel($item, 3, 13, [VanillaBlocks::COBBLESTONE()->asItem()->setCount(40)]);
		$this->registerMinerLevel($item, 4, 13, [VanillaBlocks::COBBLESTONE()->asItem()->setCount(64)]);
		$this->registerMinerLevel($item, 5, 12, [SkyblockItems::ENCHANTED_COBBLESTONE()]);
		$this->registerMinerLevel($item, 6, 12, [SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(2)]);
		$this->registerMinerLevel($item, 7, 10, [SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(4)]);
		$this->registerMinerLevel($item, 8, 10, [SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(8)]);
		$this->registerMinerLevel($item, 9, 9, [SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(16)]);
		$this->registerMinerLevel($item, 10, 9, [SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(32)]);
		$this->registerMinerLevel($item, 11, 7, [SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(64)]);
	}

	public function registerMinerLevel(Block $block, int $level, float $baseSpeed, array $itemAroundIt): void {
		$this->levels[strtolower(str_replace(" ", "_", $block->getName()))][$level] = new MinionLevel($level, $baseSpeed, $itemAroundIt);
	}

	public function getLevel(Block $block, int $level): ?MinionLevel {
		return $this->levels[strtolower(str_replace(" ", "_", $block->getName()))][$level] ?? null;
	}

	/**
	 * @return array
	 */
	public function getAllLevels() : array{
		return $this->levels;
	}
}