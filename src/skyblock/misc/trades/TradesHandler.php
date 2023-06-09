<?php

declare(strict_types=1);

namespace skyblock\misc\trades;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\traits\AetherHandlerTrait;

//TODO: implement unlocking/locking this
class TradesHandler {
	use AetherHandlerTrait;

	/** @var Trade[] */
	private array $trades = [];

	public function onEnable() : void{
		$this->register("Birch Leaves", VanillaBlocks::BIRCH_SAPLING()->asItem(), VanillaBlocks::BIRCH_LEAVES()->asItem());
		$this->register("Jungle Oak Leaves", VanillaBlocks::JUNGLE_SAPLING()->asItem(), VanillaBlocks::JUNGLE_LEAVES()->asItem());
		$this->register("Dark Oak Leaves", VanillaBlocks::DARK_OAK_SAPLING()->asItem(), VanillaBlocks::DARK_OAK_LEAVES()->asItem());
		$this->register("Oak Leaves", VanillaBlocks::OAK_SAPLING()->asItem(), VanillaBlocks::OAK_LEAVES()->asItem());
		$this->register("Spruce Leaves", VanillaBlocks::SPRUCE_SAPLING()->asItem(), VanillaBlocks::SPRUCE_LEAVES()->asItem());
		$this->register("Acacia Leaves", VanillaBlocks::ACACIA_SAPLING()->asItem(), VanillaBlocks::ACACIA_LEAVES()->asItem());
		$this->register("Vines", VanillaBlocks::JUNGLE_LEAVES()->asItem()->setCount(5), VanillaBlocks::VINES()->asItem());
		$this->register("Water Bucket", VanillaItems::CLOWNFISH()->setCount(1), VanillaItems::WATER_BUCKET()->setCount(1));
	}
	
	public function register(string $name, Item $input, Item $output): void {
		$this->trades[strtolower($name)] = new Trade(strtolower($name), $input, $output);
	}

	public function getAll(): array {
		return $this->trades;
	}

	public function getById(string $id): ?Trade {
		return $this->trades[strtolower($id)] ?? null;
	}

	public function getByOutput(Item $i): ?Trade {
		foreach($this->trades as $trade){
			if($trade->getOutput()->equals($i)){
				return $trade;
			}
		}

		return null;
	}
}