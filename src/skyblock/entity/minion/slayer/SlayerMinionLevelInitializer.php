<?php

declare(strict_types=1);

namespace skyblock\entity\minion\slayer;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\Server;
use skyblock\entity\minion\farmer\FarmerMinion;
use skyblock\entity\minion\farmer\FarmerType;
use skyblock\entity\minion\miner\MinerMinion;
use skyblock\entity\minion\miner\MinerType;
use skyblock\entity\minion\MinionHandler;
use skyblock\entity\minion\MinionLevel;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedBeef;
use skyblock\items\special\types\crafting\EnchantedBone;
use skyblock\items\special\types\crafting\EnchantedChicken;
use skyblock\items\special\types\crafting\EnchantedCoal;
use skyblock\items\special\types\crafting\EnchantedCoalBlock;
use skyblock\items\special\types\crafting\EnchantedCobblestone;
use skyblock\items\special\types\crafting\EnchantedCookedMutton;
use skyblock\items\special\types\crafting\EnchantedDiamond;
use skyblock\items\special\types\crafting\EnchantedDiamondBlock;
use skyblock\items\special\types\crafting\EnchantedEmerald;
use skyblock\items\special\types\crafting\EnchantedEmeraldBlock;
use skyblock\items\special\types\crafting\EnchantedEnderPearl;
use skyblock\items\special\types\crafting\EnchantedEyeOfEnder;
use skyblock\items\special\types\crafting\EnchantedFermentedSpiderEye;
use skyblock\items\special\types\crafting\EnchantedGold;
use skyblock\items\special\types\crafting\EnchantedGoldBlock;
use skyblock\items\special\types\crafting\EnchantedGrilledPork;
use skyblock\items\special\types\crafting\EnchantedGunpowder;
use skyblock\items\special\types\crafting\EnchantedIron;
use skyblock\items\special\types\crafting\EnchantedIronBlock;
use skyblock\items\special\types\crafting\EnchantedLapis;
use skyblock\items\special\types\crafting\EnchantedLapisBlock;
use skyblock\items\special\types\crafting\EnchantedLeather;
use skyblock\items\special\types\crafting\EnchantedMutton;
use skyblock\items\special\types\crafting\EnchantedObsidian;
use skyblock\items\special\types\crafting\EnchantedPorkchop;
use skyblock\items\special\types\crafting\EnchantedRabbitFoot;
use skyblock\items\special\types\crafting\EnchantedRabbitHide;
use skyblock\items\special\types\crafting\EnchantedRedstone;
use skyblock\items\special\types\crafting\EnchantedRedstoneBlock;
use skyblock\items\special\types\crafting\EnchantedRottenFlesh;
use skyblock\items\special\types\crafting\EnchantedSlimeball;
use skyblock\items\special\types\crafting\EnchantedSlimeBlock;
use skyblock\items\special\types\crafting\EnchantedSpiderEye;
use skyblock\items\special\types\crafting\EnchantedString;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\EntityUtils;

class SlayerMinionLevelInitializer{
	use AetherHandlerTrait;


	private array $levels = [];

	public function onEnable() : void{
		$this->registerSlime();
		$this->registerSkeleton();
		$this->registerZombie();
		$this->registerEnderman();
		$this->registerSpider();
		$this->registerCaveSpider();
		$this->registerCreeper();

		$this->registerRabbit();
		$this->registerSheep();
		$this->registerChicken();
		$this->registerPig();
		$this->registerCow();

		foreach($this->levels as $k => $v){
			$allEggs = [];
			$level = 1;
			/** @var MinionLevel $minionLevel */
			foreach($v as $minionLevel){

				$minion = $this->createNewMinion($level, MinionHandler::getInstance()->getSlayerType($k), "console");
				$egg = $minion->getEggItem();
				$allEggs[] = $egg;
				$minion->flagForDespawn();

				$array = [];
				for($i = 1; $i <= 9; $i++){
					$array[] = $minionLevel->getNeeds()[0];
				}

				if($level === 1){
					$array[4] = VanillaItems::WOODEN_SWORD();
				} else {
					$array[4] = $allEggs[$level - 2];
				}

				RecipesHandler::getInstance()->registerRecipe("$k Minion " . CustomEnchantUtils::roman($level), $array, $egg, 0);
				$level++;
			}
		}
	}


	public function registerChicken(): void {
		$item = EntityIds::CHICKEN;

		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::RAW_CHICKEN()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::RAW_CHICKEN()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::RAW_CHICKEN()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::RAW_CHICKEN()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_CHICKEN()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_CHICKEN()->setCount(2)]);
		$this->registerSlayerLevel($item, 7, 20, [SkyblockItems::ENCHANTED_CHICKEN()->setCount(4)]);
		$this->registerSlayerLevel($item, 8, 20, [SkyblockItems::ENCHANTED_CHICKEN()->setCount(8)]);
		$this->registerSlayerLevel($item, 9, 18, [SkyblockItems::ENCHANTED_CHICKEN()->setCount(16)]);
		$this->registerSlayerLevel($item, 10, 18, [SkyblockItems::ENCHANTED_CHICKEN()->setCount(32)]);
		$this->registerSlayerLevel($item, 11, 15, [SkyblockItems::ENCHANTED_CHICKEN()->setCount(64)]);
	}

	public function registerCreeper(): void {
		$item = EntityIds::CREEPER;

		$this->registerSlayerLevel($item, 1, 27, [VanillaItems::GUNPOWDER()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 27, [VanillaItems::GUNPOWDER()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 25, [VanillaItems::GUNPOWDER()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 25, [VanillaItems::GUNPOWDER()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 23, [SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 23, [SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(4)]);
		$this->registerSlayerLevel($item, 7, 21, [SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(8)]);
		$this->registerSlayerLevel($item, 8, 21, [SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(16)]);
		$this->registerSlayerLevel($item, 9, 18, [SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(32)]);
		$this->registerSlayerLevel($item, 10, 18, [SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(48)]);
		$this->registerSlayerLevel($item, 11, 14, [SkyblockItems::ENCHANTED_GUNPOWDER()->setCount(64)]);
	}

	public function registerCow(): void {
		$item = EntityIds::COW;

		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::RAW_BEEF()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::RAW_BEEF()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::RAW_BEEF()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::RAW_BEEF()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_BEEF()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_BEEF()->setCount(4)]);
		$this->registerSlayerLevel($item, 7, 20, [SkyblockItems::ENCHANTED_BEEF()->setCount(8)]);
		$this->registerSlayerLevel($item, 8, 20, [SkyblockItems::ENCHANTED_BEEF()->setCount(16)]);
		$this->registerSlayerLevel($item, 9, 17, [SkyblockItems::ENCHANTED_BEEF()->setCount(32)]);
		$this->registerSlayerLevel($item, 10, 17, [SkyblockItems::ENCHANTED_BEEF()->setCount(64)]);
		$this->registerSlayerLevel($item, 11, 13, [SkyblockItems::ENCHANTED_LEATHER()->setCount(32)]);
	}

	public function registerRabbit(): void {
		$item = EntityIds::RABBIT;


		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::RAW_RABBIT()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::RAW_RABBIT()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::RAW_RABBIT()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::RAW_RABBIT()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_RABBIT_FOOT()->setCount(4)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_RABBIT_FOOT()->setCount(8)]);
		$this->registerSlayerLevel($item, 7, 20, [SkyblockItems::ENCHANTED_RABBIT_FOOT()->setCount(16)]);
		$this->registerSlayerLevel($item, 8, 20, [SkyblockItems::ENCHANTED_RABBIT_FOOT()->setCount(32)]);
		$this->registerSlayerLevel($item, 9, 17, [SkyblockItems::ENCHANTED_RABBIT_FOOT()->setCount(64)]);
		$this->registerSlayerLevel($item, 10, 17, [SkyblockItems::ENCHANTED_RABBIT_HIDE()->setCount(32)]);
		$this->registerSlayerLevel($item, 11, 13, [SkyblockItems::ENCHANTED_RABBIT_HIDE()->setCount(64)]);
	}

	public function registerPig(): void {
		$item = EntityIds::PIG;


		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::RAW_PORKCHOP()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::RAW_PORKCHOP()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::RAW_PORKCHOP()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::RAW_PORKCHOP()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_PORKCHOP()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_PORKCHOP()->setCount(3)]);
		$this->registerSlayerLevel($item, 7, 20, [SkyblockItems::ENCHANTED_PORKCHOP()->setCount(8)]);
		$this->registerSlayerLevel($item, 8, 20, [SkyblockItems::ENCHANTED_PORKCHOP()->setCount(16)]);
		$this->registerSlayerLevel($item, 9, 17, [SkyblockItems::ENCHANTED_PORKCHOP()->setCount(32)]);
		$this->registerSlayerLevel($item, 10, 17, [SkyblockItems::ENCHANTED_PORKCHOP()->setCount(64)]);
		$this->registerSlayerLevel($item, 11, 13, [SkyblockItems::ENCHANTED_GRILLED_PORK()->setCount(1)]);
	}

	public function registerSheep(): void {
		$item = EntityIds::SHEEP;


		$this->registerSlayerLevel($item, 1, 24, [VanillaItems::RAW_MUTTON()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 24, [VanillaItems::RAW_MUTTON()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 22, [VanillaItems::RAW_MUTTON()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 22, [VanillaItems::RAW_MUTTON()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 20, [SkyblockItems::ENCHANTED_MUTTON()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 20, [SkyblockItems::ENCHANTED_MUTTON()->setCount(3)]);
		$this->registerSlayerLevel($item, 7, 18, [SkyblockItems::ENCHANTED_MUTTON()->setCount(8)]);
		$this->registerSlayerLevel($item, 8, 18, [SkyblockItems::ENCHANTED_MUTTON()->setCount(16)]);
		$this->registerSlayerLevel($item, 9, 16, [SkyblockItems::ENCHANTED_MUTTON()->setCount(32)]);
		$this->registerSlayerLevel($item, 10, 16, [SkyblockItems::ENCHANTED_MUTTON()->setCount(64)]);
		$this->registerSlayerLevel($item, 11, 12, [SkyblockItems::ENCHANTED_COOKED_MUTTON()->setCount(1)]);
	}

	public function registerEnderman(): void {
		$item = EntityIds::ENDERMAN;

		$this->registerSlayerLevel($item, 1, 32, [VanillaItems::ENDER_PEARL()->setCount(8)]);
		$this->registerSlayerLevel($item, 2, 32, [VanillaItems::ENDER_PEARL()->setCount(16)]);
		$this->registerSlayerLevel($item, 3, 30, [SkyblockItems::ENCHANTED_ENDER_PEARL()->setCount(1)]);
		$this->registerSlayerLevel($item, 4, 30, [SkyblockItems::ENCHANTED_ENDER_PEARL()->setCount(3)]);
		$this->registerSlayerLevel($item, 5, 28, [SkyblockItems::ENCHANTED_ENDER_PEARL()->setCount(6)]);
		$this->registerSlayerLevel($item, 6, 28, [SkyblockItems::ENCHANTED_ENDER_PEARL()->setCount(12)]);
		$this->registerSlayerLevel($item, 7, 25, [SkyblockItems::ENCHANTED_EYE_OF_ENDER()->setCount(1)]);
		$this->registerSlayerLevel($item, 8, 25, [SkyblockItems::ENCHANTED_EYE_OF_ENDER()->setCount(3)]);
		$this->registerSlayerLevel($item, 9, 22, [SkyblockItems::ENCHANTED_EYE_OF_ENDER()->setCount(6)]);
		$this->registerSlayerLevel($item, 10, 22, [SkyblockItems::ENCHANTED_EYE_OF_ENDER()->setCount(12)]);
		$this->registerSlayerLevel($item, 11, 18, [SkyblockItems::ENCHANTED_EYE_OF_ENDER()->setCount(24)]);
	}

	public function registerSkeleton(): void {
		$item = EntityIds::SKELETON;

		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::BONE()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::BONE()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::BONE()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::BONE()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_BONE()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_BONE()->setCount(2)]);
		$this->registerSlayerLevel($item, 7, 20, [SkyblockItems::ENCHANTED_BONE()->setCount(4)]);
		$this->registerSlayerLevel($item, 8, 20, [SkyblockItems::ENCHANTED_BONE()->setCount(8)]);
		$this->registerSlayerLevel($item, 9, 17, [SkyblockItems::ENCHANTED_BONE()->setCount(16)]);
		$this->registerSlayerLevel($item, 10, 17, [SkyblockItems::ENCHANTED_BONE()->setCount(32)]);
		$this->registerSlayerLevel($item, 11, 13, [SkyblockItems::ENCHANTED_BONE()->setCount(64)]);
	}

	public function registerSpider(): void {
		$item = EntityIds::SPIDER;

		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::STRING()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::STRING()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::STRING()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::STRING()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_STRING()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_STRING()->setCount(2)]);
		$this->registerSlayerLevel($item, 7, 20, [SkyblockItems::ENCHANTED_STRING()->setCount(4)]);
		$this->registerSlayerLevel($item, 8, 20, [SkyblockItems::ENCHANTED_STRING()->setCount(8)]);
		$this->registerSlayerLevel($item, 9, 17, [SkyblockItems::ENCHANTED_STRING()->setCount(16)]);
		$this->registerSlayerLevel($item, 10, 17, [SkyblockItems::ENCHANTED_STRING()->setCount(32)]);
		$this->registerSlayerLevel($item, 11, 13, [SkyblockItems::ENCHANTED_STRING()->setCount(64)]);
	}

	public function registerCaveSpider(): void {
		$item = EntityIds::CAVE_SPIDER;

		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::SPIDER_EYE()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::SPIDER_EYE()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::SPIDER_EYE()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::SPIDER_EYE()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_SPIDER_EYE()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_SPIDER_EYE()->setCount(3)]);
		$this->registerSlayerLevel($item, 7, 20, [SkyblockItems::ENCHANTED_SPIDER_EYE()->setCount(8)]);
		$this->registerSlayerLevel($item, 8, 20, [SkyblockItems::ENCHANTED_SPIDER_EYE()->setCount(16)]);
		$this->registerSlayerLevel($item, 9, 17, [SkyblockItems::ENCHANTED_SPIDER_EYE()->setCount(32)]);
		$this->registerSlayerLevel($item, 10, 17, [SkyblockItems::ENCHANTED_SPIDER_EYE()->setCount(64)]);
		$this->registerSlayerLevel($item, 11, 13, [SkyblockItems::ENCHANTED_FERMENTED_SPIDER_EYE()->setCount(2)]);
	}

	public function registerZombie(): void {
		$item = EntityIds::ZOMBIE;

		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::ROTTEN_FLESH()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::ROTTEN_FLESH()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::ROTTEN_FLESH()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::ROTTEN_FLESH()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(2)]);
		$this->registerSlayerLevel($item, 7, 20, [SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(4)]);
		$this->registerSlayerLevel($item, 8, 20, [SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(8)]);
		$this->registerSlayerLevel($item, 9, 17, [SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(16)]);
		$this->registerSlayerLevel($item, 10, 17, [SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(32)]);
		$this->registerSlayerLevel($item, 11, 13, [SkyblockItems::ENCHANTED_ROTTEN_FLESH()->setCount(64)]);
	}

	public function registerSlime(): void {
		$item = EntityIds::SLIME;

		$this->registerSlayerLevel($item, 1, 26, [VanillaItems::SLIMEBALL()->setCount(10)]);
		$this->registerSlayerLevel($item, 2, 26, [VanillaItems::SLIMEBALL()->setCount(20)]);
		$this->registerSlayerLevel($item, 3, 24, [VanillaItems::SLIMEBALL()->setCount(40)]);
		$this->registerSlayerLevel($item, 4, 24, [VanillaItems::SLIMEBALL()->setCount(64)]);
		$this->registerSlayerLevel($item, 5, 22, [SkyblockItems::ENCHANTED_SLIMEBALL()->setCount(1)]);
		$this->registerSlayerLevel($item, 6, 22, [SkyblockItems::ENCHANTED_SLIMEBALL()->setCount(3)]);
		$this->registerSlayerLevel($item, 7, 19, [SkyblockItems::ENCHANTED_SLIMEBALL()->setCount(8)]);
		$this->registerSlayerLevel($item, 8, 19, [SkyblockItems::ENCHANTED_SLIMEBALL()->setCount(16)]);
		$this->registerSlayerLevel($item, 9, 16, [SkyblockItems::ENCHANTED_SLIMEBALL()->setCount(32)]);
		$this->registerSlayerLevel($item, 10, 16, [SkyblockItems::ENCHANTED_SLIMEBALL()->setCount(64)]);
		$this->registerSlayerLevel($item, 11, 12, [SkyblockItems::ENCHANTED_SLIME_BLOCK()->setCount(1)]);
	}

	public function createNewMinion(int $level, SlayerType $type, string $owner, int $resources = 0): SlayerMinion {
		$nbt = new CompoundTag();
		$nbt->setInt("level", $level);
		$nbt->setInt("resources", $resources);
		$nbt->setString("owner", $owner);
		$nbt->setString("type", $type->getName());

		$pos = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
		$location = Location::fromObject($pos, $pos->getWorld());
		return new SlayerMinion($location, $nbt);
	}

	public function registerSlayerLevel(string $entityId, int $level, float $baseSpeed, array $itemAroundIt): void {
		$name = EntityUtils::getEntityNameFromID($entityId);

		$this->levels[strtolower(str_replace(" ", "_", $name))][$level] = new MinionLevel($level, $baseSpeed, $itemAroundIt);
	}

	public function getLevel(string $entity, int $level): ?MinionLevel {
		return $this->levels[strtolower(str_replace(" ", "_", $entity))][$level] ?? null;
	}

	/**
	 * @return array
	 */
	public function getAllLevels() : array{
		return $this->levels;
	}
}