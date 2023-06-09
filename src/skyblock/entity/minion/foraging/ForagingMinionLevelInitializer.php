<?php

declare(strict_types=1);

namespace skyblock\entity\minion\foraging;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use skyblock\entity\minion\farmer\FarmerMinion;
use skyblock\entity\minion\farmer\FarmerType;
use skyblock\entity\minion\foraging\ForagingMinion;
use skyblock\entity\minion\MinionHandler;
use skyblock\entity\minion\MinionLevel;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedCoal;
use skyblock\items\special\types\crafting\EnchantedCoalBlock;
use skyblock\items\special\types\crafting\EnchantedCobblestone;
use skyblock\items\special\types\crafting\EnchantedDiamond;
use skyblock\items\special\types\crafting\EnchantedDiamondBlock;
use skyblock\items\special\types\crafting\EnchantedEmerald;
use skyblock\items\special\types\crafting\EnchantedEmeraldBlock;
use skyblock\items\special\types\crafting\EnchantedGold;
use skyblock\items\special\types\crafting\EnchantedGoldBlock;
use skyblock\items\special\types\crafting\EnchantedIron;
use skyblock\items\special\types\crafting\EnchantedIronBlock;
use skyblock\items\special\types\crafting\EnchantedLapis;
use skyblock\items\special\types\crafting\EnchantedLapisBlock;
use skyblock\items\special\types\crafting\EnchantedOakWood;
use skyblock\items\special\types\crafting\EnchantedObsidian;
use skyblock\items\special\types\crafting\EnchantedRedstone;
use skyblock\items\special\types\crafting\EnchantedRedstoneBlock;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\CustomEnchantUtils;

class ForagingMinionLevelInitializer {
	use AetherHandlerTrait; //TODO:

	private array $levels = [];

	public function onEnable() : void{
		$this->registerOak();
		$this->registerDarkOak();
		$this->registerAcacia();
		$this->registerJungle();
		$this->registerSpruce();
		$this->registerBirch();

		foreach($this->levels as $k => $v){
			$allEggs = [];
			$level = 1;
			/** @var MinionLevel $minionLevel */
			foreach($v as $minionLevel){
				$minion = $this->createNewMinion($level, MinionHandler::getInstance()->getForagerType($k), "console");
				$egg = $minion->getEggItem();
				$allEggs[] = $egg;
				$minion->flagForDespawn();

				$array = [];
				for($i = 1; $i <= 9; $i++){
					$array[] = $minionLevel->getNeeds()[0];
				}

				if($level === 1){
					$array[4] = VanillaItems::WOODEN_AXE();
				} else {
					$array[4] = $allEggs[$level - 2];
				}

				RecipesHandler::getInstance()->registerRecipe("$k Minion " . CustomEnchantUtils::roman($level), $array, $egg, 0);
				$level++;
			}
		}
	}

	public function createNewMinion(int $level, ForagerType $type, string $owner, int $resources = 0): ForagingMinion {
		$nbt = new CompoundTag();
		$nbt->setInt("level", $level);
		$nbt->setInt("resources", $resources);
		$nbt->setString("owner", $owner);
		$nbt->setString("type", $type->getName());

		$pos = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
		$location = Location::fromObject($pos, $pos->getWorld());
		return new ForagingMinion($location, $nbt);
	}

	public function registerOak(): void {
		$item = VanillaBlocks::OAK_LOG();

		$this->registerForagerLevel($item, 1, 48, [VanillaBlocks::OAK_LOG()->asItem()->setCount(10)]);
		$this->registerForagerLevel($item, 2, 48, [VanillaBlocks::OAK_LOG()->asItem()->setCount(20)]);
		$this->registerForagerLevel($item, 3, 45, [VanillaBlocks::OAK_LOG()->asItem()->setCount(40)]);
		$this->registerForagerLevel($item, 4, 45, [VanillaBlocks::OAK_LOG()->asItem()->setCount(64)]);
		$this->registerForagerLevel($item, 5, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()]);
		$this->registerForagerLevel($item, 6, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(2)]);
		$this->registerForagerLevel($item, 7, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(4)]);
		$this->registerForagerLevel($item, 8, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(8)]);
		$this->registerForagerLevel($item, 9, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(16)]);
		$this->registerForagerLevel($item, 10, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(32)]);
		$this->registerForagerLevel($item, 11, 30, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(64)]);
	}

	public function registerBirch(): void {
		$item = VanillaBlocks::BIRCH_LOG();

		$this->registerForagerLevel($item, 1, 48, [VanillaBlocks::BIRCH_LOG()->asItem()->setCount(10)]);
		$this->registerForagerLevel($item, 2, 48, [VanillaBlocks::BIRCH_LOG()->asItem()->setCount(20)]);
		$this->registerForagerLevel($item, 3, 45, [VanillaBlocks::BIRCH_LOG()->asItem()->setCount(40)]);
		$this->registerForagerLevel($item, 4, 45, [VanillaBlocks::BIRCH_LOG()->asItem()->setCount(64)]);
		$this->registerForagerLevel($item, 5, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()]);
		$this->registerForagerLevel($item, 6, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(2)]);
		$this->registerForagerLevel($item, 7, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(4)]);
		$this->registerForagerLevel($item, 8, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(8)]);
		$this->registerForagerLevel($item, 9, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(16)]);
		$this->registerForagerLevel($item, 10, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(32)]);
		$this->registerForagerLevel($item, 11, 30, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(64)]);
	}

	public function registerAcacia(): void {
		$item = VanillaBlocks::ACACIA_LOG();

		$this->registerForagerLevel($item, 1, 48, [VanillaBlocks::ACACIA_LOG()->asItem()->setCount(10)]);
		$this->registerForagerLevel($item, 2, 48, [VanillaBlocks::ACACIA_LOG()->asItem()->setCount(20)]);
		$this->registerForagerLevel($item, 3, 45, [VanillaBlocks::ACACIA_LOG()->asItem()->setCount(40)]);
		$this->registerForagerLevel($item, 4, 45, [VanillaBlocks::ACACIA_LOG()->asItem()->setCount(64)]);
		$this->registerForagerLevel($item, 5, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()]);
		$this->registerForagerLevel($item, 6, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(2)]);
		$this->registerForagerLevel($item, 7, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(4)]);
		$this->registerForagerLevel($item, 8, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(8)]);
		$this->registerForagerLevel($item, 9, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(16)]);
		$this->registerForagerLevel($item, 10, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(32)]);
		$this->registerForagerLevel($item, 11, 30, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(64)]);
	}

	public function registerJungle(): void {
		$item = VanillaBlocks::JUNGLE_LOG();

		$this->registerForagerLevel($item, 1, 48, [VanillaBlocks::JUNGLE_LOG()->asItem()->setCount(10)]);
		$this->registerForagerLevel($item, 2, 48, [VanillaBlocks::JUNGLE_LOG()->asItem()->setCount(20)]);
		$this->registerForagerLevel($item, 3, 45, [VanillaBlocks::JUNGLE_LOG()->asItem()->setCount(40)]);
		$this->registerForagerLevel($item, 4, 45, [VanillaBlocks::JUNGLE_LOG()->asItem()->setCount(64)]);
		$this->registerForagerLevel($item, 5, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()]);
		$this->registerForagerLevel($item, 6, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(2)]);
		$this->registerForagerLevel($item, 7, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(4)]);
		$this->registerForagerLevel($item, 8, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(8)]);
		$this->registerForagerLevel($item, 9, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(16)]);
		$this->registerForagerLevel($item, 10, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(32)]);
		$this->registerForagerLevel($item, 11, 30, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(64)]);
	}

	public function registerSpruce(): void {
		$item = VanillaBlocks::SPRUCE_LOG();

		$this->registerForagerLevel($item, 1, 48, [VanillaBlocks::SPRUCE_LOG()->asItem()->setCount(10)]);
		$this->registerForagerLevel($item, 2, 48, [VanillaBlocks::SPRUCE_LOG()->asItem()->setCount(20)]);
		$this->registerForagerLevel($item, 3, 45, [VanillaBlocks::SPRUCE_LOG()->asItem()->setCount(40)]);
		$this->registerForagerLevel($item, 4, 45, [VanillaBlocks::SPRUCE_LOG()->asItem()->setCount(64)]);
		$this->registerForagerLevel($item, 5, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()]);
		$this->registerForagerLevel($item, 6, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(2)]);
		$this->registerForagerLevel($item, 7, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(4)]);
		$this->registerForagerLevel($item, 8, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(8)]);
		$this->registerForagerLevel($item, 9, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(16)]);
		$this->registerForagerLevel($item, 10, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(32)]);
		$this->registerForagerLevel($item, 11, 30, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(64)]);
	}

	public function registerDarkOak(): void {
		$item = VanillaBlocks::DARK_OAK_LOG();

		$this->registerForagerLevel($item, 1, 48, [VanillaBlocks::DARK_OAK_LOG()->asItem()->setCount(10)]);
		$this->registerForagerLevel($item, 2, 48, [VanillaBlocks::DARK_OAK_LOG()->asItem()->setCount(20)]);
		$this->registerForagerLevel($item, 3, 45, [VanillaBlocks::DARK_OAK_LOG()->asItem()->setCount(40)]);
		$this->registerForagerLevel($item, 4, 45, [VanillaBlocks::DARK_OAK_LOG()->asItem()->setCount(64)]);
		$this->registerForagerLevel($item, 5, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()]);
		$this->registerForagerLevel($item, 6, 42, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(2)]);
		$this->registerForagerLevel($item, 7, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(4)]);
		$this->registerForagerLevel($item, 8, 39, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(8)]);
		$this->registerForagerLevel($item, 9, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(16)]);
		$this->registerForagerLevel($item, 10, 36, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(32)]);
		$this->registerForagerLevel($item, 11, 30, [SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(64)]);
	}

	public function registerForagerLevel(Block $block, int $level, float $baseSpeed, array $itemAroundIt): void {
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