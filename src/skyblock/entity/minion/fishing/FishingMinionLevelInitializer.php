<?php

declare(strict_types=1);

namespace skyblock\entity\minion\fishing;

use muqsit\random\WeightedRandom;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use skyblock\entity\minion\MinionHandler;
use skyblock\entity\minion\MinionLevel;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedCookedFish;
use skyblock\items\special\types\crafting\EnchantedOakWood;
use skyblock\items\special\types\crafting\EnchantedRawFish;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\CustomEnchantUtils;

class FishingMinionLevelInitializer {
	use AetherHandlerTrait; //TODO:

	private array $levels = [];

	private WeightedRandom $random; //loottable of the fishing minion

	public function onEnable() : void{
		$this->setupLoot();


		$this->registerFish();


		foreach($this->levels as $k => $v){
			$allEggs = [];
			$level = 1;
			/** @var MinionLevel $minionLevel */
			foreach($v as $minionLevel){
				$minion = $this->createNewMinion($level, MinionHandler::getInstance()->getFishingType($k), "console");
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

	public function setupLoot(): void {
		$arr = [
			50 => VanillaItems::RAW_FISH(),
			25 => VanillaItems::RAW_SALMON(),
			12 => VanillaItems::PUFFERFISH(),
			4 => VanillaItems::CLOWNFISH(),
			3.1 => VanillaItems::PRISMARINE_CRYSTALS(),
			3.05 => VanillaItems::PRISMARINE_SHARD(),
			3.01 => VanillaBlocks::SPONGE()->asItem(),

		];

		$this->random = new WeightedRandom();

		foreach($arr as $k => $v){
			$this->random->add($v, $k);
		}

		$this->random->setup();
	}

	public function createNewMinion(int $level, FishingType $type, string $owner, int $resources = 0): FishingMinion {
		$nbt = new CompoundTag();
		$nbt->setInt("level", $level);
		$nbt->setInt("resources", $resources);
		$nbt->setString("owner", $owner);
		$nbt->setString("type", $type->getName());

		$pos = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
		$location = Location::fromObject($pos, $pos->getWorld());
		return new FishingMinion($location, $nbt);
	}

	public function registerFish(): void {
		$item = VanillaItems::RAW_FISH();

		$this->registerFishingLevel($item, 1, 78, [VanillaItems::RAW_FISH()->setCount(10)]);
		$this->registerFishingLevel($item, 2, 75, [VanillaItems::RAW_FISH()->setCount(20)]);
		$this->registerFishingLevel($item, 3, 72, [VanillaItems::RAW_FISH()->setCount(40)]);
		$this->registerFishingLevel($item, 4, 72, [VanillaItems::RAW_FISH()->setCount(64)]);
		$this->registerFishingLevel($item, 5, 68, [SkyblockItems::ENCHANTED_RAW_FISH()]);
		$this->registerFishingLevel($item, 6, 68, [SkyblockItems::ENCHANTED_RAW_FISH()->setCount(2)]);
		$this->registerFishingLevel($item, 7, 62, [SkyblockItems::ENCHANTED_RAW_FISH()->setCount(8)]);
		$this->registerFishingLevel($item, 8, 62, [SkyblockItems::ENCHANTED_RAW_FISH()->setCount(16)]);
		$this->registerFishingLevel($item, 9, 53, [SkyblockItems::ENCHANTED_RAW_FISH()->setCount(32)]);
		$this->registerFishingLevel($item, 10, 53, [SkyblockItems::ENCHANTED_RAW_FISH()->setCount(64)]);
		$this->registerFishingLevel($item, 11, 3, [SkyblockItems::ENCHANTED_COOKED_FISH()]);
	}


	public function registerFishingLevel(Item $block, int $level, float $baseSpeed, array $itemAroundIt): void {
		$this->levels[strtolower(str_replace(" ", "_", $block->getName()))][$level] = new MinionLevel($level, $baseSpeed, $itemAroundIt);
	}

	public function getLevel(Item $block, int $level): ?MinionLevel {
		return $this->levels[strtolower(str_replace(" ", "_", $block->getName()))][$level] ?? null;
	}

	/**
	 * @return array
	 */
	public function getAllLevels() : array{
		return $this->levels;
	}

	/**
	 * @return WeightedRandom
	 */
	public function getRandomLoottable() : WeightedRandom{
		return $this->random;
	}
}