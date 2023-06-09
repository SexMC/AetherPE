<?php

declare(strict_types=1);

namespace skyblock\misc\recipes;

use pocketmine\block\VanillaBlocks;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use ReflectionClass;
use skyblock\blocks\custom\types\FarmCrystalCustomBlock;
use skyblock\items\accessory\types\ExperienceArtifact;
use skyblock\items\accessory\types\FarmingTalismanAccessory;
use skyblock\items\accessory\types\HealingRingAccessory;
use skyblock\items\accessory\types\HealingTalismanAccessory;
use skyblock\items\accessory\types\SpeedArtifactAccessory;
use skyblock\items\accessory\types\SpeedRingAccessory;
use skyblock\items\accessory\types\SpeedTalismanAccessory;
use skyblock\items\accessory\types\VaccineTalismanAccessory;
use skyblock\items\accessory\types\WoodAffinityTalismanAccessory;
use skyblock\items\armor\angler\AnglerSet;
use skyblock\items\armor\ArmorSet;
use skyblock\items\armor\emerald\EmeraldSet;
use skyblock\items\armor\farm\FarmSet;
use skyblock\items\armor\farm_suit\FarmSuitSet;
use skyblock\items\armor\golem\GolemSet;
use skyblock\items\armor\growth\GrowthSet;
use skyblock\items\armor\hardened_diamond\HardenedDiamondSet;
use skyblock\items\armor\leaflet\LeafletSet;
use skyblock\items\armor\miner\MinerSet;
use skyblock\items\armor\pumpkin\PumpkinSet;
use skyblock\items\armor\speedster\SpeedsterSet;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Angler;
use skyblock\items\customenchants\types\Autosmelt;
use skyblock\items\customenchants\types\BaneOfArthropods;
use skyblock\items\customenchants\types\BlastProtection;
use skyblock\items\customenchants\types\Caster;
use skyblock\items\customenchants\types\Cleave;
use skyblock\items\customenchants\types\Critical;
use skyblock\items\customenchants\types\Cubism;
use skyblock\items\customenchants\types\Efficiency;
use skyblock\items\customenchants\types\EnderSlayer;
use skyblock\items\customenchants\types\Experience;
use skyblock\items\customenchants\types\FirstStrike;
use skyblock\items\customenchants\types\Growth;
use skyblock\items\customenchants\types\Harvesting;
use skyblock\items\customenchants\types\Knockback;
use skyblock\items\customenchants\types\Magnet;
use skyblock\items\customenchants\types\Power;
use skyblock\items\customenchants\types\Protection;
use skyblock\items\customenchants\types\Punch;
use skyblock\items\customenchants\types\Rainbow;
use skyblock\items\customenchants\types\Respiration;
use skyblock\items\customenchants\types\Scavenger;
use skyblock\items\customenchants\types\Sharpness;
use skyblock\items\customenchants\types\SilkTouch;
use skyblock\items\customenchants\types\Smite;
use skyblock\items\customenchants\types\Thunderlord;
use skyblock\items\Equipment;
use skyblock\items\masks\types\ChickenHeadMask;
use skyblock\items\masks\types\ClownfishMask;
use skyblock\items\masks\types\FarmerMask;
use skyblock\items\masks\types\FishMask;
use skyblock\items\masks\types\LanternMask;
use skyblock\items\masks\types\PufferfishMask;
use skyblock\items\masks\types\SkeletonMask;
use skyblock\items\masks\types\SlimeHatMask;
use skyblock\items\masks\types\SpiderMask;
use skyblock\items\masks\types\ZombiesHeartMask;
use skyblock\items\potions\SkyBlockPotion;
use skyblock\items\SkyblockItemFactory;
use skyblock\items\SkyblockItems;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\CarrotCandy;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\fishing\SpikedBait;
use skyblock\items\special\types\HotpotatoBook;
use skyblock\items\special\types\HyperFurnace;
use skyblock\items\special\types\minion\BioMinionFuel;
use skyblock\items\special\types\minion\CoalMinionFuel;
use skyblock\items\special\types\minion\DieselMinionFuel;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\misc\minion\StorageChest;
use skyblock\items\misc\storagesack\StorageSack;

use skyblock\items\special\types\upgrades\UltimateCarrotCandyUpgrade;

use skyblock\Main;
use skyblock\misc\collection\CollectionHandler;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class RecipesHandler{
	use AwaitStdTrait;
	use AetherHandlerTrait;

	/** @var array<string, Recipe> */
	private array $recipes = [];

	private array $recipesByIdAndCustomName = [];

	private array $classified = [];

	private array $unlockings = [];

	public function onEnable() : void{
		$this->registerSpawners();
		//$this->registerEnchantedItems();
		$this->registerMinionAndFuel();

		$this->registerCeBooks();
		$this->registerSpecialSets();
		$this->registerAccessories();
		$this->registerMasks();
		$this->registerCustomBlocks();
		$this->registerWeaponAndTools();
		$this->registerStorageSacks();

		$this->registerPets();

		$this->registerRandomStuff();


		Await::f2c(function(){
			yield $this->getStd()->sleep(15);
			Main::debug("Started sorting recipes");
			$start = microtime(true);
			$totalFound = 0;
			foreach($this->recipes as $recipe){
				$found = false;

				$special = SpecialItem::getSpecialItem($recipe->getOutput());
				if($special instanceof MinionEgg){
					$lvl = $recipe->getOutput()->getNamedTag()->getInt("level", -1);

					if($lvl > 1){
						continue;
					}
				}


				yield $this->getStd()->sleep(1);


				foreach(CollectionHandler::getInstance()->getAllCollections() as $type => $list){
					foreach($list as $collection){
						if(Utils::$isDev){
							//yield $this->getStd()->sleep(1);
						}


						foreach($collection->getUnlockRecipes() as $index => $l){


							if(!is_array($l)){
								$l = [$l];
							}

							foreach($l as $it){
								if($it instanceof Item){


									if($this->getRecipeByItem($it)?->getName() === $recipe->getName()){
										$totalFound++;
										$found = true;


										if(str_contains(strtolower($recipe->getName()), "enchantment")){
											$this->classified["enchanting"][] = $recipe;
										} else if($recipe->getOutput() instanceof SkyBlockPotion){
											//TODO: potion recipes viewing
											$this->classified["alchemy"][] = $recipe;
										}
										else {
											$this->classified[$type][] = $recipe;
										}

										$this->unlockings[$recipe->getName()] = $collection->getName() . " " . CustomEnchantUtils::roman($index);

										break 4;
									}
								}
							}

						}
					}
				}

				if($found === false){
					$this->classified["unclassified"][] = $recipe;
				}
			}


			$all = sizeof($this->recipes);
			Main::debug("Classified recipes ($totalFound/$all) took: " . number_format(microtime(true) - $start, 2) . "s");
		});
		
		
		foreach(Server::getInstance()->getCraftingManager()->getShapedRecipes() as $r){
			foreach($r as $k => $recipe){
				$res = $recipe->getResults()[0];

				$sbItem = SkyblockItemFactory::getInstance()->getOrNull($res->getId(), $res->getMeta());

				if($sbItem){
					if(!$res->equals($sbItem)){
						if($sbItem instanceof Equipment){
							$class = new ReflectionClass($recipe);
							$prop = $class->getProperty("results");
							$prop->setAccessible(true);
							$prop->setValue($recipe, [ItemFactory::getInstance()->get($res->getId(), $res->getMeta())]);
							var_dump("updated for: " . $res->getName());
						}
					}
				}


			}
		}
	}

	public function registerStorageSacks() : void{
		$i = SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(2);
		$this->registerRecipe("Small Combat Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => SkyblockItems::ENCHANTED_LEATHER()->setCount(2), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::COMBAT_SACK())->setCapacity(StorageSack::SIZE_SMALL), 20 * 60);

		$i = SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(4);
		$this->registerRecipe("Medium Combat Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::COMBAT_SACK())->setCapacity(StorageSack::SIZE_SMALL), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::COMBAT_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 30 * 60);

		$i = SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(8);
		$this->registerRecipe("Large Combat Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::COMBAT_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::COMBAT_SACK())->setCapacity(StorageSack::SIZE_LARGE), 40 * 60);


		$i = SkyblockItems::ENCHANTED_BIRCH_WOOD()->setCount(2);
		$this->registerRecipe("Small Foraging Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => SkyblockItems::ENCHANTED_LEATHER()->setCount(2), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::FORAGING_SACK())->setCapacity(StorageSack::SIZE_SMALL), 20 * 60);

		$i = SkyblockItems::ENCHANTED_BIRCH_WOOD()->setCount(4);
		$this->registerRecipe("Medium Foraging Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::FORAGING_SACK())->setCapacity(StorageSack::SIZE_SMALL), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::FORAGING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 30 * 60);

		$i = SkyblockItems::ENCHANTED_BIRCH_WOOD()->setCount(8);
		$this->registerRecipe("Large Foraging Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::FORAGING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::FORAGING_SACK())->setCapacity(StorageSack::SIZE_LARGE), 40 * 60);


		$i = SkyblockItems::ENCHANTED_HAY_BALE()->setCount(2);
		$this->registerRecipe("Small Agronomy Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => SkyblockItems::ENCHANTED_LEATHER()->setCount(2), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::AGRONOMY_SACK())->setCapacity(StorageSack::SIZE_SMALL), 20 * 60);

		$i = SkyblockItems::ENCHANTED_HAY_BALE()->setCount(4);
		$this->registerRecipe("Medium Agronomy Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::AGRONOMY_SACK())->setCapacity(StorageSack::SIZE_SMALL), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::AGRONOMY_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 30 * 60);

		$i = SkyblockItems::ENCHANTED_HAY_BALE()->setCount(8);
		$this->registerRecipe("Large Agronomy Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::AGRONOMY_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::AGRONOMY_SACK())->setCapacity(StorageSack::SIZE_LARGE), 40 * 60);


		$i = SkyblockItems::ENCHANTED_COAL()->setCount(2);
		$this->registerRecipe("Small Mining Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => SkyblockItems::ENCHANTED_LEATHER()->setCount(2), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::MINING_SACK())->setCapacity(StorageSack::SIZE_SMALL), 20 * 60);

		$i = SkyblockItems::ENCHANTED_COAL()->setCount(4);
		$this->registerRecipe("Medium Mining Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::MINING_SACK())->setCapacity(StorageSack::SIZE_SMALL), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::MINING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 30 * 60);

		$i = SkyblockItems::ENCHANTED_COAL()->setCount(8);
		$this->registerRecipe("Large Mining Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::MINING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::MINING_SACK())->setCapacity(StorageSack::SIZE_LARGE), 40 * 60);


		$i = SkyblockItems::ENCHANTED_MUTTON()->setCount(2);
		$this->registerRecipe("Small Husbandry Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => SkyblockItems::ENCHANTED_LEATHER()->setCount(2), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::HUSBANDRY_SACK())->setCapacity(StorageSack::SIZE_SMALL), 20 * 60);

		$i = SkyblockItems::ENCHANTED_MUTTON()->setCount(4);
		$this->registerRecipe("Medium Husbandry Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::HUSBANDRY_SACK())->setCapacity(StorageSack::SIZE_SMALL), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::HUSBANDRY_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 30 * 60);

		$i = SkyblockItems::ENCHANTED_MUTTON()->setCount(8);
		$this->registerRecipe("Large Husbandry Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::HUSBANDRY_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::HUSBANDRY_SACK())->setCapacity(StorageSack::SIZE_LARGE), 40 * 60);


		$i = SkyblockItems::ENCHANTED_PUFFERFISH()->setCount(2);
		$this->registerRecipe("Fishing Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => SkyblockItems::ENCHANTED_LEATHER()->setCount(2), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::FISHING_SACK())->setCapacity(StorageSack::SIZE_SMALL), 20 * 60);

		$i = SkyblockItems::ENCHANTED_PUFFERFISH()->setCount(4);
		$this->registerRecipe("Fishing Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::FISHING_SACK())->setCapacity(StorageSack::SIZE_SMALL), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::FISHING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 30 * 60);

		$i = SkyblockItems::ENCHANTED_PUFFERFISH()->setCount(8);
		$this->registerRecipe("Fishing Sack", [
			0 => $i, 1 => $i, 2 => $i,
			3 => $i, 4 => (SkyblockItems::FISHING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM), 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
		], (SkyblockItems::FISHING_SACK())->setCapacity(StorageSack::SIZE_LARGE), 40 * 60);

	}

	public function registerCustomBlocks() : void{
		$item = SkyblockItems::ENCHANTED_PUMPKIN()->setCount(12);
		$this->registerRecipe("Farm Crystal Custom Block",
			[0 => $item, 1 => $item, 2 => $item, 3 => $item, 4 => SkyblockItems::ENCHANTED_QUARTZ(), 5 => $item, 6 => $item, 7 => $item, 8 => $item], FarmCrystalCustomBlock::getItem(), 60 * 120);

	}

	public function registerWeaponAndTools() : void{
		$this->registerRecipe("Cleaver Sword", [
			0 => VanillaItems::GOLD_INGOT(), 1 => VanillaItems::GOLD_INGOT(), 2 => VanillaItems::GOLD_INGOT(), 5 => VanillaItems::GOLD_INGOT(),
			4 => VanillaItems::STICK(), 7 => VanillaItems::STICK(),
		], SkyblockItems::CLEAVER_SWORD(), 5 * 60);

		$this->registerRecipe("Flint Shovel", [
			1 => VanillaItems::FLINT()->setCount(15),
			4 => VanillaItems::STICK(),
			7 => VanillaItems::STICK(),
		], SkyblockItems::FLINT_SHOVEL(), 5 * 60);

		$this->registerRecipe("Pigman Sword", [
			1 => SkyblockItems::ENCHANTED_GRILLED_PORK()->setCount(24),
			4 => SkyblockItems::ENCHANTED_GRILLED_PORK()->setCount(24),
			7 => VanillaItems::STICK(),
		], SkyblockItems::PIGMAN_SWORD(), 45 * 60);

		$this->registerRecipe("Zombie Sword", [
			1 => ZombiesHeartMask::getItem(),
			4 => ZombiesHeartMask::getItem(),
			7 => VanillaItems::STICK(),
		], SkyblockItems::ZOMBIE_SWORD(), 50 * 60);

		$this->registerRecipe("Aspect Of The End Sword", [
			1 => SkyblockItems::ENCHANTED_EYE_OF_ENDER()->setCount(16),
			4 => SkyblockItems::ENCHANTED_EYE_OF_ENDER()->setCount(16),
			7 => SkyblockItems::ENCHANTED_DIAMOND(),
		], SkyblockItems::ASPECT_OF_THE_END_SWORD(), 1);

		$this->registerRecipe("Spider Sword", [
			1 => VanillaItems::SPIDER_EYE(),
			4 => VanillaItems::SPIDER_EYE(),
			7 => VanillaItems::STICK(),
		], SkyblockItems::SPIDER_SWORD(), 1 * 60);

		$this->registerRecipe("Golem Sword", [
			1 => SkyblockItems::ENCHANTED_IRON_BLOCK(),
			4 => SkyblockItems::ENCHANTED_IRON_BLOCK(),
			7 => VanillaItems::STICK(),
		], SkyblockItems::GOLEM_SWORD(), 15 * 60);

		$i = SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(64);
		$this->registerRecipe("Explosive Bow", [
			1 => $i, 2 => VanillaItems::STRING(),
			0 => $i, 5 => VanillaItems::STRING(),
			3 => $i, 8 => VanillaItems::STRING(),
			7 => $i,
			6 => $i,
		], SkyblockItems::EXPLOSIVE_BOW(), 35 * 60);

		$i = SkyblockItems::ENCHANTED_ENDER_PEARL()->setCount(4);
		$this->registerRecipe("Ender Bow", [
			1 => $i, 2 => VanillaItems::STRING(),
			5 => VanillaItems::STRING(),
			3 => $i, 8 => VanillaItems::STRING(),
			7 => $i,
		], SkyblockItems::ENDER_BOW(), 35 * 60);

		$i = SkyblockItems::ENCHANTED_BONE()->setCount(64);
		$this->registerRecipe("Runaan's Bow", [
			1 => $i, 2 => SkyblockItems::ENCHANTED_STRING()->setCount(64),
			5 => SkyblockItems::ENCHANTED_STRING()->setCount(64),
			3 => $i, 8 => SkyblockItems::ENCHANTED_STRING()->setCount(64),
			7 => $i,
		], SkyblockItems::RUNAANS_BOW(), 1);

		$i = SkyblockItems::ENCHANTED_BONE()->setCount(32);
		$this->registerRecipe("Hurricane Bow", [
			1 => $i, 2 => VanillaItems::STRING()->setCount(32),
			5 => VanillaItems::STRING()->setCount(32),
			3 => $i, 8 => VanillaItems::STRING()->setCount(32),
			7 => $i,
		], SkyblockItems::HURRICANE_BOW(), 35 * 60);


		$i = SkyblockItems::ENCHANTED_ACACIA_WOOD()->setCount(1);
		$this->registerRecipe("Savanna Bow", [
			1 => $i, 2 => VanillaItems::STRING(),
			0 => $i, 5 => VanillaItems::STRING(),
			3 => $i, 8 => VanillaItems::STRING(),
			7 => $i,
			6 => $i,
		], SkyblockItems::SAVANNA_BOW(), 15 * 60);

		$i = VanillaItems::ROTTEN_FLESH();
		$this->registerRecipe("Zombie Pickaxe", [
			0 => $i, 1 => $i, 2 => $i,
			4 => VanillaItems::STICK(), 7 => VanillaItems::STICK(),
		], SkyblockItems::ZOMBIE_PICKAXE(), 5 * 60);

		$i = SkyblockItems::ENCHANTED_JUNGLE_WOOD();
		$this->registerRecipe("Jungle Axe", [
			1 => $i, 2 => $i, 5 => $i,
			4 => VanillaItems::STICK(),
			7 => VanillaItems::STICK(),
		], SkyblockItems::JUNGLE_AXE(), 15 * 60);

		$i = SkyblockItems::ENCHANTED_OBSIDIAN()->setCount(64);
		$this->registerRecipe("Treecapacitor", [
			0 => $i, 1 => $i, 2 => $i, 3 => $i, 5 => $i,
			6 => $i, 7 => $i, 8 => $i,
			4 => SkyblockItems::JUNGLE_AXE(),

		], SkyblockItems::TREECAPACITOR(), 35 * 60);


		$this->registerRecipe("Farmers Rod", [
			2 => SkyblockItems::ENCHANTED_CLAY(),
			4 => VanillaBlocks::HAY_BALE()->asItem()->setCount(32),
			5 => VanillaItems::STRING(),
			6 => VanillaBlocks::HAY_BALE()->asItem()->setCount(32),
			8 => VanillaItems::STRING(),


		], SkyblockItems::FARMERS_ROD(), 35 * 60);


		$this->registerRecipe("Ink Wand", [
			1 => SkyblockItems::ENCHANTED_INK_SAC()->setCount(32),
			4 => SkyblockItems::ENCHANTED_INK_SAC()->setCount(32),
			7 => VanillaItems::STICK(),
		], SkyblockItems::INK_WAND(), 60 * 120);


		$item = VanillaBlocks::LILY_PAD()->asItem()->setCount(64);
		$this->registerRecipe("Rod Of Champions", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => VanillaItems::FISHING_ROD(),
			5 => $item, 6 => $item, 7 => $item, 8 => $item
		], SkyblockItems::ROD_OF_CHAMPIONS(), 60 * 20);

		$item = SkyblockItems::ENCHANTED_LILY_PAD()->setCount(16);
		$this->registerRecipe("Rod Of Legends", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => SkyblockItems::ROD_OF_CHAMPIONS(),
			5 => $item, 6 => $item, 7 => $item, 8 => $item
		], SkyblockItems::ROD_OF_LEGENDS(), 60 * 20);
	}

	public function registerMasks() : void{

		$item = VanillaItems::EGG()->setCount(1);
		$this->registerRecipe("Chicken Head Mask", [0 => $item, 1 => $item, 2 => $item, 3 => $item, 5 => $item], ChickenHeadMask::getItem(), 60 * 60);


		$item = SkyblockItems::ENCHANTED_PUMPKIN()->setCount(32);
		$this->registerRecipe("Lantern Mask", [1 => $item, 3 => $item, 4 => $item, 5 => $item, 7 => VanillaBlocks::TORCH()->asItem()], LanternMask::getItem(), 60 * 30);

		//$item = SkyblockItems::ENCHANTED_PUMPKIN()->setCount(64);
		//$this->registerRecipe("Farmer Mask", [1 => $item, 2 => $item, 3 => $item, 4 => $item], FarmerMask::getItem(), 60 * 60);


		$item = VanillaItems::BONE()->setCount(4);
		$this->registerRecipe("Skeleton Mask", [1 => $item, 3 => $item, 4 => $item, 5 => $item, 7 => $item], SkeletonMask::getItem(), 60 * 7);

		$item = SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(48);
		$this->registerRecipe("Zombie's Heart Mask", [1 => $item, 3 => $item, 4 => $item, 5 => $item, 7 => $item], ZombiesHeartMask::getItem(), 60 * 55);

		$item = VanillaItems::SPIDER_EYE()->setCount(2);
		$this->registerRecipe("Spider Mask", [1 => $item, 3 => $item, 4 => $item, 5 => $item, 7 => $item], SpiderMask::getItem(), 60 * 60);

		$item = VanillaItems::SLIMEBALL()->setCount(8);
		$this->registerRecipe("Slime Hat Mask", [1 => $item, 3 => $item, 4 => $item, 5 => $item, 7 => $item], SlimeHatMask::getItem(), 60 * 20);

		$item = VanillaItems::CLOWNFISH()->setCount(1);
		$this->registerRecipe("Clownfish Hat Mask", [1 => $item, 3 => $item, 4 => $item, 5 => $item, 7 => $item], ClownfishMask::getItem(), 60 * 5);

		$item = VanillaItems::PUFFERFISH()->setCount(2);
		$this->registerRecipe("Pufferfish Hat Mask", [1 => $item, 3 => $item, 4 => $item, 5 => $item, 7 => $item], PufferfishMask::getItem(), 60 * 15);

		$item = VanillaItems::RAW_FISH()->setCount(2);
		$this->registerRecipe("Fish Mask", [1 => $item, 3 => $item, 4 => $item, 5 => $item, 7 => $item], FishMask::getItem(), 60 * 15);
	}

	public function registerRandomStuff() : void{
		$item = VanillaItems::EGG()->setCount(16);

		$this->registerRecipe("Enchanted Egg", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => $item,
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], SkyblockItems::ENCHANTED_EGG(), 1);

		$item = SkyblockItems::ENCHANTED_EGG()->setCount(16);

		$this->registerRecipe("Super Enchanted Egg", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => $item,
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], SkyblockItems::SUPER_ENCHANTED_EGG(), 1);
		
		$item = SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(8);
		$this->registerRecipe("Compactor", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => SkyblockItems::ENCHANTED_COAL()->setCount(8),
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], SkyblockItems::HYPER_FURNACE(), 30);

		$this->registerRecipe("Hot Potato Book", [
			0 => SkyblockItems::ENCHANTED_BAKED_POTATO(),
			1 => VanillaItems::PAPER(),
			3 => VanillaItems::PAPER(),
			4 => VanillaItems::PAPER(),
		], SkyblockItems::HOT_POTATO_BOOK(), 60 * 60);

		$i = VanillaItems::STRING();
		$this->registerRecipe("Cobweb", [
			$i, $i, $i,
			$i, $i, $i,
			$i, $i, $i,
		], VanillaBlocks::COBWEB()->asItem(), 20);


		$i = SkyblockItems::ENCHANTED_REDSTONE_BLOCK()->setCount(2);
		$this->registerRecipe("Personal Compactor 4000", [
			$i, $i, $i,
			$i, SkyblockItems::SUPER_COMPACTOR_3000(), $i,
			$i, $i, $i,
		], SkyblockItems::PERSONAL_COMPACTOR_4000(), 60 * 60);

		$i = VanillaItems::LAPIS_LAZULI()->setCount(2);
		$this->registerRecipe("Experience Bottle", [
			$i, $i, $i,
			$i, VanillaItems::GLASS_BOTTLE(), $i,
			$i, $i, $i,
		], VanillaItems::EXPERIENCE_BOTTLE(), 20);


		$this->registerRecipe("Spiked Bait", [
			4 => VanillaItems::RAW_FISH(),
			7 => VanillaItems::PUFFERFISH(),
		], SpikedBait::getItem(), 30);
	}

	public function registerPets() : void{
		$item = VanillaItems::CARROT()->setCount(64);
		$this->registerRecipe("Simple Carrot Candy", [
			$item, $item, $item,
			$item, $item, $item,
			$item, $item, $item,
		], SkyblockItems::SIMPLE_CARROT_CANDY(), 60);

		$item = SkyblockItems::ENCHANTED_CARROT()->setCount(40);
		$this->registerRecipe("Great Carrot Candy", [
			$item, $item, $item,
			$item, SkyblockItems::SIMPLE_CARROT_CANDY(), $item,
			$item, $item, $item,
		], SkyblockItems::GREAT_CARROT_CANDY(), 60 * 5);

		$item = SkyblockItems::ENCHANTED_GOLDEN_CARROT()->setCount(3);
		$this->registerRecipe("Superb Carrot Candy", [
			$item, $item, $item,
			$item, SkyblockItems::GREAT_CARROT_CANDY(), $item,
			$item, $item, $item,
		], SkyblockItems::SUPERB_CARROT_CANDY(), 60 * 15);

		$item = SkyblockItems::SUPERB_CARROT_CANDY();
		$this->registerRecipe("Superb Carrot Candy", [
			$item, $item, $item,
			$item, SkyblockItems::ULTIMATE_CARROT_CANDY(), $item,
			$item, $item, $item,
		], SkyblockItems::ULTIMATE_CARROT_CANDY(), 60 * 30);
	}


	public function registerAccessories() : void{
		$seed = VanillaItems::WHEAT_SEEDS();
		$haybale = VanillaBlocks::HAY_BALE()->asItem();
		$this->registerRecipe("Farming Talisman", [$haybale, $seed, $haybale, $seed, $haybale, $seed, $haybale, $seed, $haybale], SkyblockItems::FARMING_TALISMAN(), 15 * 60);


		$item = VanillaBlocks::SUGARCANE()->asItem()->setCount(12);
		$this->registerRecipe("Speed Talisman", [$item, $item, $item, $item, $item, $item, $item, $item, $item], SkyblockItems::SPEED_TALISMAN(), 60 * 20);


		$item = SkyblockItems::ENCHANTED_SUGAR()->setCount(12);
		$this->registerRecipe("Speed Ring", [$item, $item, $item, $item, SkyblockItems::SPEED_TALISMAN(), $item, $item, $item, $item], SkyblockItems::SPEED_RING(), 60 * 20);


		$item = SkyblockItems::ENCHANTED_SUGARCANE()->setCount(6);
		$this->registerRecipe("Speed Artifact", [$item, $item, $item, $item, SkyblockItems::SPEED_RING(), $item, $item, $item, $item], SkyblockItems::SPEED_ARTIFACT(), 60 * 20);

		$item = VanillaItems::POISONOUS_POTATO();
		$this->registerRecipe("Vaccine Talisman", [$item, $item, $item, $item, $item, $item, $item, $item, $item], SkyblockItems::VACCINE_TALISMAN(), 60 * 20);

		$item = SkyblockItems::ENCHANTED_LAPIS_BLOCK();
		$this->registerRecipe("Experience Artifact", [$item, $item, $item, $item, $item, $item, $item, $item, $item], SkyblockItems::EXPERIENCE_ARTIFACT(), 50 * 60);

		$item = VanillaBlocks::OAK_LEAVES()->asItem();
		$this->registerRecipe("Wood Affinity Talisman", [$item, $item, $item, $item, SkyblockItems::ENCHANTED_OAK_WOOD(), $item, $item, $item, $item], SkyblockItems::WOOD_AFFINITY_TALISMAN(), 50 * 60);


		$item = VanillaBlocks::LILY_PAD()->asItem()->setCount(16);
		$this->registerRecipe("Healing Talisman", [$item, $item, $item, $item, $item, $item, $item, $item, $item], SkyblockItems::HEALING_TALISMAN(), 3 * 60);


		$item = SkyblockItems::ENCHANTED_LILY_PAD();
		$this->registerRecipe("Healing Ring", [1 => $item, 3 => $item, 4 => SkyblockItems::HEALING_TALISMAN(), 5 => $item, 7 => $item], SkyblockItems::HEALING_RING(), 8 * 60);
	}

	public function registerSpecialSets() : void{
		$form = [
			ArmorSet::PIECE_HELMET => [0, 1, 2, 3, 5],
			ArmorSet::PIECE_LEGGINGS => [0, 1, 2, 3, 5, 8, 6],
			ArmorSet::PIECE_BOOTS => [3, 5, 6, 7, 8],
			ArmorSet::PIECE_CHESTPLATE => [0, 2, 3, 4, 5, 7, 8, 6]
		];

		$defaultSets = [
			[FarmSuitSet::getInstance(), VanillaBlocks::HAY_BALE()->asItem()],
			[SpeedsterSet::getInstance(), SkyblockItems::ENCHANTED_SUGARCANE()],
			[FarmSet::getInstance(), SkyblockItems::ENCHANTED_HAY_BALE()],
			[MinerSet::getInstance(), SkyblockItems::ENCHANTED_COBBLESTONE()],
			[PumpkinSet::getInstance(), VanillaBlocks::PUMPKIN()->asItem()],
			[HardenedDiamondSet::getInstance(), SkyblockItems::ENCHANTED_DIAMOND()],
			//can't add zombie to it cuz it has multiple items in recipe [\skyblock\items\armor\zombie\ZombieSet::getInstance()],
			[GrowthSet::getInstance(), SkyblockItems::ENCHANTED_DARK_OAK_WOOD()->setCount(64)],
			[AnglerSet::getInstance(), VanillaItems::RAW_FISH()],
			[GolemSet::getInstance(), SkyblockItems::ENCHANTED_IRON()->setCount(10)],
			[LeafletSet::getInstance(), VanillaBlocks::OAK_LEAVES()->asItem()],
			[EmeraldSet::getInstance(), SkyblockItems::ENCHANTED_EMERALD_BLOCK()->setCount(1)]
		];

		foreach($defaultSets as $set){
			/** @var ArmorSet $instance */
			$instance = $set[0];
			$ingredient = $set[1];

			foreach($instance->getPieceItems() as $key => $item){
				if(isset($form[$key])){
					$r = [];

					foreach($form[$key] as $v){
						$r[$v] = $ingredient;
					}

					$this->registerRecipe($rName = (ucwords(str_replace("_", " ", $instance->getIdentifier())) . " " . $key), $r, $item, 0);
					Main::debug("Register set recipe: " . $rName);
				}
			}
		}


		//zombie set start
		$zombieSet = \skyblock\items\armor\zombie\ZombieSet::getInstance()->getPieceItems();
		$item = SkyblockItems::ENCHANTED_ROTTEN_FLESH();
		$this->registerRecipe("Zombie Helmet", [0 => ZombiesHeartMask::getItem(), 1 => $item, 2 => $item, 3 => $item, 5 => ZombiesHeartMask::getItem()], $zombieSet[ArmorSet::PIECE_HELMET], 90 * 30);
		$this->registerRecipe("Zombie Boots", [3 => ZombiesHeartMask::getItem(), 5 => $item, 6 => $item, 7 => $item, 8 => ZombiesHeartMask::getItem()], $zombieSet[ArmorSet::PIECE_BOOTS], 90 * 30);
		$this->registerRecipe("Zombie Leggings", [0 => ZombiesHeartMask::getItem(), 1 => ZombiesHeartMask::getItem(), 2 => ZombiesHeartMask::getItem(), 3 => $item, 5 => $item, 8 => $item, 6 => $item], $zombieSet[ArmorSet::PIECE_LEGGINGS], 90 * 30);
		$this->registerRecipe("Zombie Chestplate", [0 => ZombiesHeartMask::getItem(), 2 => ZombiesHeartMask::getItem(), 3 => ZombiesHeartMask::getItem(), 4 => ZombiesHeartMask::getItem(), 5 => $item, 7 => $item, 8 => $item, 6 => $item], $zombieSet[ArmorSet::PIECE_CHESTPLATE], 90 * 30);
		//zombie  end
	}

	public function registerCeBooks() : void{
		$this->registerRecipe("Harvesting Custom Enchantment", [
			0 => VanillaItems::WHEAT()->setCount(16),
			1 => VanillaBlocks::SUGARCANE()->asItem()->setCount(16),
			3 => VanillaBlocks::SUGARCANE()->asItem()->setCount(16),
			4 => VanillaBlocks::SUGARCANE()->asItem()->setCount(16),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::HARVESTING(), 5)), 10 * 60);

		$this->registerRecipe("Cubism Custom Enchantment", [
			0 => VanillaBlocks::PUMPKIN()->asItem()->setCount(32),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::CUBISM(), 4)), 10 * 60);

		$this->registerRecipe("Auto Smelt Enchantment", [
			0 => VanillaBlocks::COAL()->asItem()->setCount(5),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::AUTO_SMELT(), 1)), 10 * 60);

		$this->registerRecipe("Execute Enchantment", [
			0 => VanillaItems::DIAMOND()->setCount(40),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
			8 => VanillaItems::FLINT()->setCount(40),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::EXECUTE(), 4)), 10 * 60);

		$this->registerRecipe("Critical Enchantment", [
			0 => SkyblockItems::ENCHANTED_DIAMOND()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::CRITICAL(), 4)), 10 * 60);

		$this->registerRecipe("Scavenger Enchantment", [
			0 => SkyblockItems::ENCHANTED_GOLD()->setCount(3),
			1 => VanillaItems::PAPER()->setCount(5),
			3 => VanillaItems::PAPER()->setCount(5),
			4 => VanillaItems::PAPER()->setCount(5),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::SCAVENGER(), 2)), 10 * 60);

		$this->registerRecipe("Rainbow Enchantment", [
			0 => VanillaBlocks::WOOL()->asItem()->setCount(48),
			1 => VanillaItems::PAPER()->setCount(5),
			3 => VanillaItems::PAPER()->setCount(5),
			4 => VanillaItems::PAPER()->setCount(5),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::RAINBOW(), 1)), 10 * 60);

		$this->registerRecipe("Blast Protection Enchantment", [
			0 => VanillaItems::GUNPOWDER()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(5),
			3 => VanillaItems::PAPER()->setCount(5),
			4 => VanillaItems::PAPER()->setCount(5),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::BLAST_PROTECTION(), 4)), 10 * 60);

		$this->registerRecipe("Thunderlord Enchantment", [
			0 => SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(5),
			3 => VanillaItems::PAPER()->setCount(5),
			4 => VanillaItems::PAPER()->setCount(5),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::THUNDERLORD(), 4)), 20 * 60);


		$this->registerRecipe("Ender Slayer Enchantment", [
			0 => SkyblockItems::ENCHANTED_ENDER_PEARL()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::ENDER_SLAYER(), 4)), 10 * 60);

		$this->registerRecipe("Power Enchantment", [
			0 => VanillaItems::BONE()->setCount(64),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::POWER(), 4)), 10 * 60);

		$this->registerRecipe("Smite Enchantment", [
			0 => VanillaItems::ROTTEN_FLESH()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(2),
			3 => VanillaItems::PAPER()->setCount(2),
			4 => VanillaItems::PAPER()->setCount(2),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::SMITE(), 4)), 10 * 60);

		$this->registerRecipe("Silk Touch Enchantment", [
			0 => SkyblockItems::ENCHANTED_STRING(),
			1 => VanillaItems::PAPER()->setCount(2),
			3 => VanillaItems::PAPER()->setCount(2),
			4 => VanillaItems::PAPER()->setCount(2),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::SILK_TOUCH(), 1)), 10 * 60);

		$this->registerRecipe("Bane Of Arthropods Enchantment", [
			0 => VanillaItems::SPIDER_EYE()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(2),
			3 => VanillaItems::PAPER()->setCount(2),
			4 => VanillaItems::PAPER()->setCount(2),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::BANE_OF_ARTHROPODS(), 4)), 15 * 60);

		$this->registerRecipe("Protection Enchantment", [
			0 => VanillaItems::IRON_INGOT()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::PROTECTION(), 4)), 10 * 60);


		$this->registerRecipe("Efficiency Enchantment", [
			0 => VanillaItems::REDSTONE_DUST()->setCount(32),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::EFFICIENCY(), 4)), 25 * 60);


		$this->registerRecipe("Experience Enchantment", [
			0 => VanillaItems::LAPIS_LAZULI()->setCount(32),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::EXPERIENCE(), 2)), 25 * 60);


		$this->registerRecipe("Knockback Enchantment", [
			0 => VanillaItems::SLIMEBALL()->setCount(32),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::KNOCKBACK(), 1)), 25 * 60);


		$this->registerRecipe("Punch Enchantment", [
			0 => SkyblockItems::ENCHANTED_SLIMEBALL(),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::PUNCH(), 1)), 40 * 60);


		$this->registerRecipe("Growth Enchantment", [
			0 => SkyblockItems::ENCHANTED_DARK_OAK_WOOD()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::GROWTH(), 4)), 40 * 60);


		$this->registerRecipe("Respiration Enchantment", [
			0 => VanillaItems::WATER_BUCKET(),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
			8 => VanillaItems::RAW_FISH()->setCount(2),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::RESPIRATION(), 2)), 40 * 60);


		$this->registerRecipe("Magnet Enchantment", [
			0 => VanillaItems::WATER_BUCKET(),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
			8 => VanillaItems::CLOWNFISH()->setCount(10),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::MAGNET(), 4)), 20 * 60);


		$this->registerRecipe("Sharpness Enchantment", [
			0 => VanillaItems::FLINT()->setCount(10),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
			8 => VanillaItems::IRON_SWORD(),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::SHARPNESS(), 4)), 5 * 60);

		$this->registerRecipe("First Strike Enchantment", [
			0 => SkyblockItems::ENCHANTED_FLINT()->setCount(4),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::FIRST_STRIKE(), 3)), 5 * 60);


		$this->registerRecipe("Cleave Enchantment", [
			0 => VanillaItems::WATER_BUCKET(),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
			8 => VanillaItems::PUFFERFISH()->setCount(40),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::CLEAVE(), 4)), 20 * 60);


		$this->registerRecipe("Caster Enchantment", [
			0 => VanillaBlocks::LILY_PAD()->asItem()->setCount(8),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
			8 => VanillaItems::INK_SAC()->setCount(40),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::CASTER(), 4)), 20 * 60);


		$this->registerRecipe("Angler Enchantment", [
			0 => VanillaBlocks::LILY_PAD()->asItem()->setCount(16),
			1 => VanillaItems::PAPER()->setCount(8),
			3 => VanillaItems::PAPER()->setCount(8),
			4 => VanillaItems::PAPER()->setCount(8),
			8 => VanillaItems::INK_SAC()->setCount(64),
		], (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::ANGLER(), 4)), 20 * 60);
	}

	public function registerMinionAndFuel() : void{
		$item = VanillaBlocks::PUMPKIN()->asItem();
		$this->registerRecipe("Bio Minion Fuel", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => VanillaItems::ROTTEN_FLESH(),
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], BioMinionFuel::getItem(), 45, true);

		$item = VanillaBlocks::COBBLESTONE()->asItem()->setCount(16);
		$this->registerRecipe("Coal Minion Fuel", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => SkyblockItems::ENCHANTED_COAL(),
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], CoalMinionFuel::getItem(), 45, true);

		$item = VanillaItems::BONE()->setCount(8);
		$this->registerRecipe("Diesel Minion Fuel", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => SkyblockItems::ENCHANTED_STRING(),
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], DieselMinionFuel::getItem(), 45, true);


		$item = VanillaBlocks::COBBLESTONE()->asItem()->setCount(8);
		$this->registerRecipe("Auto Smelter", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => VanillaItems::COAL(),
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], SkyblockItems::AUTO_SMELTER(), 10);

		$item = SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(64);
		$this->registerRecipe("Super Compactor 3000", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			5 => $item, 6 => $item, 7 => SkyblockItems::ENCHANTED_REDSTONE_BLOCK(), 8 => $item,
		], SkyblockItems::SUPER_COMPACTOR_3000(), 15 * 60);

		$item = SkyblockItems::ENCHANTED_COBBLESTONE()->setCount(1);
		$this->registerRecipe("Compactor", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => SkyblockItems::ENCHANTED_REDSTONE(),
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], SkyblockItems::COMPACTOR(), 45);

		$item = VanillaBlocks::VINES()->asItem()->setCount(1);
		$this->registerRecipe("Diamond Spreading", [
			0 => $item, 1 => $item, 2 => $item, 3 => $item,
			4 => SkyblockItems::ENCHANTED_DIAMOND(),
			5 => $item, 6 => $item, 7 => $item, 8 => $item,
		], SkyblockItems::DIAMOND_SPREADING(), 10);

		$item = SkyblockItems::ENCHANTED_IRON_BLOCK();
		$this->registerRecipe("Enchanted Hopper", [
			0 => $item, 2 => $item, 3 => $item,
			4 => VanillaBlocks::CHEST()->asItem(),
			5 => $item, 7 => $item,
		], SkyblockItems::ENCHANTED_HOPPER(), 10 * 60);

		$item = SkyblockItems::ENCHANTED_IRON();
		$this->registerRecipe("Budget Hopper", [
			0 => $item, 2 => $item, 3 => $item,
			4 => VanillaBlocks::CHEST()->asItem(),
			5 => $item, 7 => $item,
		], SkyblockItems::BUDGET_HOPPER(), 10 * 60);


		$item = VanillaBlocks::OAK_LOG()->asItem()->setCount(8);
		$this->registerRecipe("Small Storage Chest", [
			0 => $item, 2 => $item, 3 => $item,
			5 => $item, 7 => $item,
		], (SkyblockItems::STORAGE_CHEST())->setType(StorageChest::TYPE_SMALL), 10 * 60);

		$item = SkyblockItems::ENCHANTED_OAK_WOOD();
		$this->registerRecipe("Medium Storage Chest", [
			0 => $item, 2 => $item, 3 => $item,
			4 => (SkyblockItems::STORAGE_CHEST())->setType(StorageChest::TYPE_SMALL),
			5 => $item, 7 => $item,
		], (SkyblockItems::STORAGE_CHEST())->setType(StorageChest::TYPE_MEDIUM), 25 * 60);

		$item = SkyblockItems::ENCHANTED_OAK_WOOD()->setCount(32);
		$this->registerRecipe("Medium Storage Chest", [
			0 => $item, 2 => $item, 3 => $item,
			4 => (SkyblockItems::STORAGE_CHEST())->setType(StorageChest::TYPE_MEDIUM),
			5 => $item, 7 => $item,
		], (SkyblockItems::STORAGE_CHEST())->setType(StorageChest::TYPE_LARGE), 50 * 60);
	}

	/*public function registerEnchantedItems() : void{




		$piece = VanillaBlocks::HAY_BALE()->asItem()->setCount(16);
		$this->registerRecipe("Enchanted Hay Bale", [
			0 => $piece, 1 => $piece, 2 => $piece, 3 => $piece, 4 => $piece,
			5 => $piece, 6 => $piece, 7 => $piece, 8 => $piece,
		], EnchantedHaybale::getItem(), 0);

		$piece = VanillaItems::WHEAT()->setCount(10);
		$this->registerRecipe("Enchanted Bread", [
			0 => $piece, 1 => $piece, 2 => $piece, 3 => $piece, 4 => $piece, 5 => $piece
		], EnchantedBread::getItem(), 0);

		$piece = VanillaItems::POTATO()->setCount(32);
		$this->registerRecipe("Enchanted Potato", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_POTATO(), 0);

		$piece = VanillaItems::CLAY()->setCount(32);
		$this->registerRecipe("Enchanted Clay", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedClay::getItem(), 0);

		$piece = SkyblockItems::ENCHANTED_SUGAR()->setCount(32);
		$this->registerRecipe("Enchanted Sugarcane", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_SUGARCANE(), 0);

		$piece = SkyblockItems::ENCHANTED_POTATO()->setCount(32);
		$this->registerRecipe("Enchanted Baked Potato", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedBakedPotato::getItem(), 0);

		$piece = VanillaItems::BLAZE_POWDER()->setCount(16);
		$this->registerRecipe("Enchanted Eye Of Ender", [
			1 => $piece, 3 => $piece, 4 => EnchantedEyeOfEnder::getItem()->setCount(16), 5 => $piece, 7 => $piece
		], EnchantedEyeOfEnder::getItem(), 0);


		$piece = SkyblockItems::ENCHANTED_SPIDER_EYE()->setCount(64);
		$this->registerRecipe("Enchanted Fermented Spider Eye", [
			1 => VanillaBlocks::BROWN_MUSHROOM()->asItem()->setCount(64),
			4 => VanillaItems::SUGAR()->setCount(64),
			7 => $piece
		], EnchantedFermentedSpiderEye::getItem(), 0);

		$piece = VanillaItems::EMERALD()->setCount(32);
		$this->registerRecipe("Enchanted Emerald", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedEmerald::getItem(), 0);

		$piece = VanillaItems::FLINT()->setCount(32);
		$this->registerRecipe("Enchanted Flint", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedFlint::getItem(), 0);

		$piece = VanillaItems::NETHER_QUARTZ()->setCount(32);
		$this->registerRecipe("Enchanted Quartz", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_QUARTZ(), 0);

		$piece = SkyblockItems::ENCHANTED_SLIMEBALL()->setCount(32);
		$this->registerRecipe("Enchanted Slime Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedSlimeBlock::getItem(), 0);

		$piece = EnchantedEmerald::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Emerald Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedEmeraldBlock::getItem(), 0);

		$piece = VanillaItems::LAPIS_LAZULI()->setCount(32);
		$this->registerRecipe("Enchanted Lapis", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedLapis::getItem(), 0);

		$piece = EnchantedLapisBlock::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Lapis Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedLapisBlock::getItem(), 0);

		$piece = VanillaItems::IRON_INGOT()->setCount(32);
		$this->registerRecipe("Enchanted Iron", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedIron::getItem(), 0);

		$piece = EnchantedIron::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Iron Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedIronBlock::getItem(), 0);

		$piece = VanillaItems::GOLD_INGOT()->setCount(32);
		$this->registerRecipe("Enchanted Gold", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedGold::getItem(), 0);

		$piece = EnchantedGold::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Gold Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedGoldBlock::getItem(), 0);

		$piece = VanillaItems::CARROT()->setCount(32);
		$this->registerRecipe("Enchanted Carrot", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedCarrot::getItem(), 0);

		$piece = VanillaItems::REDSTONE_DUST()->setCount(32);
		$this->registerRecipe("Enchanted Redstone", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_REDSTONE(), 0);


		$piece = VanillaBlocks::PUMPKIN()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Pumpkin", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_PUMPKIN(), 0);

		$piece = SkyblockItems::ENCHANTED_REDSTONE()->setCount(32);
		$this->registerRecipe("Enchanted Redstone Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_REDSTONE_BLOCK(), 0);

		$piece = VanillaBlocks::OBSIDIAN()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Obsidian", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_OBSIDIAN(), 0);

		$piece = VanillaItems::BONE()->setCount(32);
		$this->registerRecipe("Enchanted Bone", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedBone::getItem(), 0);

		$piece = VanillaItems::ROTTEN_FLESH()->setCount(32);
		$this->registerRecipe("Enchanted Rotten Flesh", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_ROTTEN_FLESH(), 0);

		$piece = VanillaItems::SLIMEBALL()->setCount(32);
		$this->registerRecipe("Enchanted Slimeball", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_SLIMEBALL(), 0);

		$piece = VanillaItems::SPIDER_EYE()->setCount(32);
		$this->registerRecipe("Enchanted Spider Eye", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_SPIDER_EYE(), 0);

		$piece = VanillaItems::ENDER_PEARL()->setCount(4);
		$this->registerRecipe("Enchanted Ender Pearl", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedEnderPearl::getItem(), 0);

		$piece = VanillaItems::MELON()->setCount(32);
		$this->registerRecipe("Enchanted Melon", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_MELON(), 0);

		$piece = SkyblockItems::ENCHANTED_MELON()->setCount(32);
		$this->registerRecipe("Enchanted Melon Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_MELON_BLOCK(), 0);

		$piece = EnchantedCoal::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Coal Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedCoalBlock::getItem(), 0);

		$piece = EnchantedDiamond::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Diamond Block", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedDiamondBlock::getItem(), 0);

		$piece = VanillaBlocks::COBBLESTONE()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Cobblestone", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedCobblestone::getItem(), 0);

		$piece = VanillaItems::DIAMOND()->setCount(32);
		$this->registerRecipe("Enchanted Diamond", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedDiamond::getItem(), 0);

		$piece = VanillaItems::GUNPOWDER()->setCount(32);
		$this->registerRecipe("Enchanted Gunpowder", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedGunpowder::getItem(), 0);

		$piece = VanillaItems::BLAZE_ROD()->setCount(32);
		$this->registerRecipe("Enchanted Blaze Rod", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedBlazeRod::getItem(), 0);

		$piece = VanillaItems::MAGMA_CREAM()->setCount(32);
		$this->registerRecipe("Enchanted Magma Cream", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedMagmaCream::getItem(), 0);

		$piece = VanillaBlocks::SAND()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Sand", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_SAND(), 0);

		$piece = VanillaBlocks::RED_SAND()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Red Sand", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedRedSand::getItem(), 0);

		$piece = EnchantedCactusGreen::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Cactus", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedCactus::getItem(), 0);

		$piece = VanillaItems::GREEN_DYE()->setCount(32);
		$this->registerRecipe("Enchanted Cactus Green", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedCactusGreen::getItem(), 0);

		$piece = VanillaItems::RAW_PORKCHOP()->setCount(32);
		$this->registerRecipe("Enchanted Porkchop", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedPorkchop::getItem(), 0);

		$piece = VanillaItems::RAW_MUTTON()->setCount(32);
		$this->registerRecipe("Enchanted Mutton", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_MUTTON(), 0);

		$piece = SkyblockItems::ENCHANTED_MUTTON()->setCount(32);
		$this->registerRecipe("Enchanted Cooked Mutton", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedCookedMutton::getItem(), 0);

		$piece = VanillaItems::RABBIT_FOOT();
		$this->registerRecipe("Enchanted Rabbit Foot", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_RABBIT_FOOT(), 0);

		$piece = VanillaItems::RABBIT_HIDE();
		$this->registerRecipe("Enchanted Rabbit Hide", [
			0 => $piece, 1 => $piece, 2 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 6 => $piece, 7 => $piece, 8 => $piece,
		], SkyblockItems::ENCHANTED_RABBIT_HIDE(), 0);

		$piece = VanillaItems::RAW_RABBIT()->setCount(32);
		$this->registerRecipe("Enchanted Rabbit", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedRabbit::getItem(), 0);

		$piece = EnchantedPorkchop::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Grilled Pork", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedGrilledPork::getItem(), 0);

		$piece = VanillaItems::RAW_CHICKEN()->setCount(32);
		$this->registerRecipe("Enchanted Chicken", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedChicken::getItem(), 0);

		$piece = VanillaItems::RAW_BEEF()->setCount(32);
		$this->registerRecipe("Enchanted Beef", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedBeef::getItem(), 0);

		$piece = VanillaItems::LEATHER()->setCount(64);
		$this->registerRecipe("Enchanted Leather", [
			1 => $piece, 2 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 6 => $piece, 7 => $piece, 8 => $piece,
		], EnchantedLeather::getItem(), 0);


		$piece = EnchantedCarrot::getItem()->setCount(32);
		$this->registerRecipe("Enchanted Golden Carrot", [
			1 => $piece, 3 => $piece, 4 => VanillaItems::GOLDEN_CARROT()->setCount(32), 5 => $piece, 7 => $piece
		], EnchantedGoldenCarrot::getItem(), 0);

		$piece = VanillaItems::GLISTERING_MELON()->setCount(32);
		$this->registerRecipe("Enchanted Glistering Melon", [
			0 => $piece, 1 => $piece, 2 => $piece, 3 => $piece, 5 => $piece, 6 => $piece, 7 => $piece, 8 => $piece
		], EnchantedGlisteringMelon::getItem(), 0);

		$piece = VanillaBlocks::SUGARCANE()->asItem()->setCount(64);
		$this->registerRecipe("Enchanted Paper", [
			2 => $piece, 5 => $piece, 6 => $piece,
		], SkyblockItems::ENCHANTED_PAPER(), 0);


		$piece = VanillaBlocks::ACACIA_LOG()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Acacia Wood", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedAcaciaWood::getItem(), 0);

		$piece = VanillaBlocks::DARK_OAK_LOG()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Dark Oak Wood", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedDarkOakWood::getItem(), 0);

		$piece = VanillaBlocks::OAK_LOG()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Oak Wood", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_OAK_WOOD(), 0);

		$piece = VanillaBlocks::JUNGLE_LOG()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Jungle Wood", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedJungleWood::getItem(), 0);

		$piece = VanillaBlocks::SPRUCE_LOG()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Spruce Wood", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedSpruceWood::getItem(), 0);

		$piece = VanillaBlocks::BIRCH_LOG()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Birch", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedBirchWood::getItem(), 0);


		$piece = VanillaItems::PUFFERFISH()->setCount(32);
		$this->registerRecipe("Enchanted Pufferfish", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_PUFFERFISH(), 0);

		$piece = VanillaItems::RAW_FISH()->setCount(32);
		$this->registerRecipe("Enchanted Raw Fish", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], SkyblockItems::ENCHANTED_RAW_FISH(), 0);

		$piece = SkyblockItems::ENCHANTED_RAW_FISH()->setCount(32);
		$this->registerRecipe("Enchanted Cooked Fish", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedCookedFish::getItem(), 0);

		$piece = VanillaBlocks::LILY_PAD()->asItem()->setCount(32);
		$this->registerRecipe("Enchanted Lily Pad", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedLilyPad::getItem(), 0);

		$piece = VanillaItems::INK_SAC()->setCount(32);
		$this->registerRecipe("Enchanted Ink Sac", [
			1 => $piece, 3 => $piece, 4 => $piece, 5 => $piece, 7 => $piece
		], EnchantedInkSac::getItem(), 0);
	}*/

	public function registerSpawners() : void{

	}

	public function registerRecipe(string $name, array $entries, Item $output, int $craftTime, bool $autoUnlock = false) : void{
		$this->recipes[strtolower($name)] = $r = new Recipe($name, $entries, $output, $craftTime, $autoUnlock);

		$this->recipesByIdAndCustomName[strtolower($output->getCustomName() . $output->getId() . $output->getMeta())] = $r;

		$list = [
			"A", "B", "C",
			"D", "E", "F",
			"G", "H", "I"
		];

		$map = ["ABC", "DEF", "GHI"];

		$string = implode(";", $map);

		$item = [];

		foreach($list as $k => $v){
			if(!isset($entries[$k])){
				$string = str_replace($list[$k], " ", $string);
			} else {
				$item[$list[$k]] = $entries[$k];
			}
		}

		
		$r = new ShapedRecipe(explode(";", $string), $item, [$output]);
		Server::getInstance()->getCraftingManager()->registerShapedRecipe($r);
	}

	/**
	 * @return Recipe[]
	 */
	public function getRecipes() : array{
		return $this->recipes;
	}

	public function getRecipe(string $name) : ?Recipe{
		return $this->recipes[strtolower($name)] ?? null;
	}

	public function getRecipeByItem(Item $output) : ?Recipe{
		return $this->recipesByIdAndCustomName[strtolower($output->getCustomName() . $output->getId() . $output->getMeta())] ?? null;
	}

	/**
	 * @return array<string, string>
	 */
	public function getUnlockings() : array{
		return $this->unlockings;
	}


	public function getUnlockingByRecipe(Recipe $recipe) : string{
		if(isset($this->unlockings[$recipe->getName()])){
			return $this->unlockings[$recipe->getName()];
		}

		if(SpecialItem::getSpecialItem($recipe->getOutput()) instanceof MinionEgg){
			$item = clone $recipe->getOutput();
			$name = $item->getCustomName();
			$name = substr($name, 0, strpos($name, "§c§l"));
			$name .= "§c§lI";
			$item->setCustomName($name);
			$rec = $this->getRecipeByItem($item);
			if($rec){
				return $this->unlockings[$rec->getName()] ?? "unknown";
			}
		}

		return "unknown";
	}

	/**
	 * @return array<string, Recipe[]>
	 */
	public function getClassified() : array{
		return $this->classified;
	}
}