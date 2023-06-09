<?php

declare(strict_types=1);

namespace skyblock\entity\minion;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use skyblock\entity\minion\farmer\FarmerType;
use skyblock\entity\minion\farmer\FarmingMinionLevelInitializer;
use skyblock\entity\minion\fishing\FishingType;
use skyblock\entity\minion\fishing\FishingMinionLevelInitializer;
use skyblock\entity\minion\foraging\ForagerType;
use skyblock\entity\minion\foraging\ForagingMinionLevelInitializer;
use skyblock\entity\minion\miner\MinerMinionLevelInitializer;
use skyblock\entity\minion\miner\MinerType;
use skyblock\entity\minion\slayer\SlayerMinionLevelInitializer;
use skyblock\entity\minion\slayer\SlayerType;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\EntityUtils;

class MinionHandler{
	use AetherHandlerTrait;


	private array $miners = [];
	private array $farmers = [];
	private array $slayers = [];
	private array $foragers = [];
	private array $fishing = [];

	public function onEnable() : void{
		$this->registerMiners();
		$this->registerFarmers();
		$this->registerSlayers();
		$this->registerForagers();
		$this->registerFishing();



		FarmingMinionLevelInitializer::initialise();
		MinerMinionLevelInitializer::initialise();
		SlayerMinionLevelInitializer::initialise();
		ForagingMinionLevelInitializer::initialise();
		FishingMinionLevelInitializer::initialise();
	}

	public function registerForagers(): void {
		$blocks = [
			VanillaBlocks::OAK_LOG(),
			VanillaBlocks::DARK_OAK_LOG(),
			VanillaBlocks::JUNGLE_LOG(),
			VanillaBlocks::ACACIA_LOG(),
			VanillaBlocks::SPRUCE_LOG(),
			VanillaBlocks::BIRCH_LOG(),
		];

		foreach($blocks as $block){
			$this->foragers[strtolower(str_replace(" ", "_", $block->getName()))] = new ForagerType($block->getName(), $block);
		}
	}

	public function registerMiners(): void {
		$blocks = [
			VanillaBlocks::COAL_ORE(),
			VanillaBlocks::IRON_ORE(),
			VanillaBlocks::GOLD_ORE(),
			VanillaBlocks::REDSTONE_ORE(),
			VanillaBlocks::LAPIS_LAZULI_ORE(),
			VanillaBlocks::DIAMOND_ORE(),
			VanillaBlocks::EMERALD_ORE(),
			VanillaBlocks::COBBLESTONE(),
			VanillaBlocks::OBSIDIAN(),

			VanillaBlocks::CLAY(),
			VanillaBlocks::GRAVEL(),
		];

		foreach($blocks as $block){
			$this->miners[strtolower(str_replace(" ", "_", $block->getName()))] = new MinerType($block->getName(), $block);
		}
	}

	public function getMinerType(string $type): ?MinerType {
		return $this->miners[strtolower(str_replace(" ", "_", $type))] ?? null;
	}

	public function getMinerTypeByBlock(Block $block): ?MinerType {
		return $this->miners[strtolower(str_replace(" ", "_", $block->getName()))] ?? null;
	}

	public function getAllMinerTypes(): array {
		return $this->miners;
	}



	public function registerFarmers(): void {
		/** @var array<block; miniontype, int: base speed> $blocks */
		$blocks = [
			VanillaBlocks::WHEAT(),
			VanillaBlocks::CARROTS(),
			VanillaBlocks::SUGARCANE(),
			VanillaBlocks::POTATOES(),
			VanillaBlocks::MELON(),
			VanillaBlocks::PUMPKIN()
		];

		foreach($blocks as $block){

			$this->farmers[strtolower(str_replace(" ", "_", $block->getName()))] = new FarmerType($block->getName(), $block);
		}
	}

	public function getFarmerType(string $type): ?FarmerType {
		return $this->farmers[strtolower(str_replace(" ", "_", $type))] ?? null;
	}

	public function getFarmerTypeByBlock(Block $type): ?FarmerType {
		return $this->farmers[strtolower(str_replace(" ", "_", $type->getName()))] ?? null;
	}

	public function getAllFarmerTypes(): array {
		return $this->farmers;
	}




	public function getForagerType(string $type): ?ForagerType {
		return $this->foragers[strtolower(str_replace(" ", "_", $type))] ?? null;
	}

	public function getForagerTypeByBlock(Block $type): ?ForagerType {
		return $this->foragers[strtolower(str_replace(" ", "_", $type->getName()))] ?? null;
	}

	public function getAllForagerTypes(): array {
		return $this->foragers;
	}


	public function registerSlayers(): void {
		/** @var array<block; miniontype, int: base speed> $blocks */
		$blocks = [
			EntityIds::PIG,
			EntityIds::SHEEP,
			EntityIds::CHICKEN,
			EntityIds::COW,
			EntityIds::RABBIT,
			EntityIds::MOOSHROOM,


			EntityIds::ZOMBIE,
			EntityIds::SPIDER,
			EntityIds::CAVE_SPIDER,
			EntityIds::SLIME,
			EntityIds::CREEPER,
			EntityIds::ENDERMAN,
			EntityIds::SKELETON,
		];

		foreach($blocks as $array){
			$entityID = $array;

			$name = EntityUtils::getEntityNameFromID($entityID);
			$this->slayers[strtolower(str_replace(" ", "_", $name))] = new SlayerType($name, $entityID);
		}
	}

	public function getSlayerType(string $type): ?SlayerType {
		return $this->slayers[strtolower(str_replace(" ", "_", $type))] ?? null;
	}

	public function getAllSlayerTypes(): array {
		return $this->slayers;
	}


	public function registerFishing(): void {
		/** @var array<block; miniontype, int: base speed> $blocks */
		$blocks = [
			VanillaItems::RAW_FISH(),
		];

		foreach($blocks as $block){

			$this->fishing[strtolower(str_replace(" ", "_", $block->getName()))] = new FishingType($block->getName(), $block);
		}
	}

	public function getFishingType(string $type): ?FishingType {
		return $this->fishing[strtolower(str_replace(" ", "_", $type))] ?? null;
	}

	public function getFishingTypeByItem(Item $type): ?FishingType {
		return $this->fishing[strtolower(str_replace(" ", "_", $type->getName()))] ?? null;
	}

	public function getAllFishingTypes(): array {
		return $this->fishing;
	}
}