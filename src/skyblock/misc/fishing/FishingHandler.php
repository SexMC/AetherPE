<?php

declare(strict_types=1);

namespace skyblock\misc\fishing;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use skyblock\traits\AetherHandlerTrait;
use muqsit\random\WeightedRandom;

//TODO: finish loottable: https://hypixel-skyblock.fandom.com/wiki/Fishing#Anywhere_
class FishingHandler {
	use AetherHandlerTrait;
	
	
	private WeightedRandom $randomNormal;
	private WeightedRandom $randomGood;
	private WeightedRandom $randomGreat;

	public function onEnable() : void{
		//WEIGHTED VALUES ARE AS FOLLOWING: [item: Item::class, fishing_skill_xp_gain: int]

		$this->randomNormal = new WeightedRandom();
		$this->randomGood = new WeightedRandom();
		$this->randomGreat = new WeightedRandom();

		$this->setupNormalCatch();

		$this->setupGoodCatch();


		$this->randomGreat->setup();
		$this->randomGood->setup();
		$this->randomNormal->setup();
	}
	
	private function setupNormalCatch(): void {
		$this->randomNormal->add([VanillaItems::RAW_FISH(), 25], 60);
		$this->randomNormal->add([VanillaItems::RAW_SALMON(), 35], 25);
		$this->randomNormal->add([VanillaItems::PUFFERFISH(), 50], 13);
		$this->randomNormal->add([VanillaItems::CLOWNFISH(), 100], 4);
		$this->randomNormal->add([VanillaItems::CLAY(), 30], 6);
	}


	private function setupGoodCatch(): void {
		$this->randomGood->add([VanillaBlocks::SPONGE()->asItem(), 160], 5);
		$this->randomGood->add([VanillaBlocks::SEA_LANTERN()->asItem(), 160], 8);
		$this->randomGood->add([VanillaItems::PRISMARINE_CRYSTALS(), 50], 5);
		$this->randomGood->add([VanillaItems::PRISMARINE_SHARD(), 50], 5);
	}

	public function getRandomGood() : WeightedRandom{
		return $this->randomGood;
	}

	public function getRandomGreat() : WeightedRandom{
		return $this->randomGreat;
	}

	public function getRandomNormal() : WeightedRandom{
		return $this->randomNormal;
	}
}