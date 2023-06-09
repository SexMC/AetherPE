<?php

declare(strict_types=1);

namespace skyblock\misc\pve;

use muqsit\random\WeightedRandom;
use pathfinder\Pathfinder;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\Server;
use pocketmine\world\World;
use skyblock\items\lootbox\LootboxItem;
use skyblock\items\lootbox\LootboxItem as LB;
use skyblock\items\sets\SpecialSet;
use skyblock\items\sets\types\ArachnesArmorSet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\ArachneFragment;
use skyblock\items\special\types\ArachnesCalling;
use skyblock\items\special\types\crafting\EnchantedSpiderEye;
use skyblock\items\special\types\crafting\EnchantedString;
use skyblock\items\special\types\crafting\SoulString;
use skyblock\listeners\PveListener;
use skyblock\Main;
use skyblock\misc\launchpads\LaunchpadHandler;
use skyblock\misc\pve\ability\ArachneBossAbility;
use skyblock\misc\pve\ability\MobAbility;
use skyblock\misc\pve\ability\MobThrowAbility;
use skyblock\misc\pve\ability\PveMagicImmuneAbility;
use skyblock\misc\pve\ability\RandomJumpAbility;
use skyblock\misc\pve\ability\SkeletonBowShootAbility;
use skyblock\misc\pve\ability\SplitterSpiderAbility;
use skyblock\misc\pve\fishing\HotspotHandler;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\traits\AetherHandlerTrait;

class PveHandler {
	use AetherHandlerTrait;

	private array $entities = [];

	private World $pveWorld;

	private array $mobAbilities = [];

	private array $loottables = [];

	public function onEnable() : void{
		Server::getInstance()->getPluginManager()->registerEvents(new PveListener(), Main::getInstance());

		Server::getInstance()->getWorldManager()->loadWorld("hypixel_net");
		$this->pveWorld = Server::getInstance()->getWorldManager()->getWorldByName("hypixel_net");


		$this->registerMobAbility(new MobThrowAbility());
		$this->registerMobAbility(new RandomJumpAbility());
		$this->registerMobAbility(new SplitterSpiderAbility());
		$this->registerMobAbility(new ArachneBossAbility());
		$this->registerMobAbility(new PveMagicImmuneAbility());
		$this->registerMobAbility(new SkeletonBowShootAbility());
		$this->registerEntityTypes();

		Pathfinder::initialise();
		ZoneHandler::initialise();
		LaunchpadHandler::initialise();
		HotspotHandler::initialise();

		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new PveDataRegenerator(), 40);
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new PveTipUpdater(), 10);
	}

	public function registerMobAbility(MobAbility $ability): void {
		$this->mobAbilities[$ability::getId()] = $ability;
	}

	public function registerEntityTypes(): void {
		$this->addPveEntityType("wolf-non-hostile", "Wolf", 1, 15, EntityIds::WOLF, 250, true, 12, 15, false, 0.33, [new LB(VanillaItems::BONE(), 100, 1, 3)]);
		$this->addPveEntityType("wolf-hostile", "Old Wolf", 40, 50, EntityIds::WOLF, 15000, true, 48, 250, false, 0.37, [new LB(VanillaItems::BONE(), 100, 1, 20)]);


		$this->addPveEntityType("zombie-non-hostile", "Zombie", 1, 1, EntityIds::ZOMBIE, 100, true, 6, 10, false, 0.33, [new LB(VanillaItems::ROTTEN_FLESH(), 100, 1, 2), new LB(VanillaItems::POISONOUS_POTATO(), 2)]);
		$this->addPveEntityType("zombie-villager-non-hostile", "Zombie Villager", 1,1, EntityIds::ZOMBIE_VILLAGER, 120, true, 8, 10, false, 0.55, [new LB(VanillaItems::ROTTEN_FLESH(), 100, 1, 2), new LB(VanillaItems::POISONOUS_POTATO(), 2)]);



		$this->addPveEntityType("floor-one-zombie", "Zombie", 5,20, EntityIds::ZOMBIE, 15000, true, 100, 150, true, 0.42, []);
		$this->addPveEntityType("floor-one-spider", "Spider", 5,23, EntityIds::SPIDER, 25000, true, 200, 200, true, 0.65, []);
		$this->addPveEntityType("floor-one-witch", "Witch", 5,25, EntityIds::WITCH, 45000, true, 300, 300, true, 0.65, []);

		$this->addPveEntityType("floor-one-iron-golem", "Iron Golem", 10, 40, EntityIds::IRON_GOLEM, 25000, true, 700, 250, true, 0.65,[], [MobThrowAbility::getId()]);


		$this->addPveEntityType("floor-one-turtle-boss", "Floor One BOSS", 20, 50, EntityIds::TURTLE, 1750000, true, 1500, 500, true, 0.95, []);
	
		$this->addPveEntityType(
			"crypt-ghoul",
			"Crypt Ghoul",
			13,
			30,
			EntityIds::ZOMBIE,
			2000,
			true,
			32,
			350,
			true,
			0.65,
			[new LB(VanillaItems::ROTTEN_FLESH(), 100, 1, 2)],
			[],
			new PveEntityEquipment(VanillaItems::IRON_SWORD(), VanillaItems::CHAINMAIL_HELMET(), VanillaItems::CHAINMAIL_CHESTPLATE(), VanillaItems::CHAINMAIL_LEGGINGS(), VanillaItems::CHAINMAIL_BOOTS())
		);

		$this->addPveEntityType(
			"weaver-spider",
			"Weaver Spider",
			2,
			3,
			EntityIds::SPIDER,
			160,
			true,
			9,
			35,
			false,
			0.6,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 50)], //todo: coins
			[]
		);

		$this->addPveEntityType(
			"dasher-spider",
			"Dasher Spider",
			2,
			4,
			EntityIds::SPIDER,
			160,
			true,
			10,
			55,
			false,
			1,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 50)], //todo: coins
			[]
		);

		$this->addPveEntityType(
			"splitter-spider",
			"Splitter Spider",
			2,
			4,
			EntityIds::SPIDER,
			180,
			true,
			10,
			30,
			false,
			0.72,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 100)], //todo: coins
			[SplitterSpiderAbility::getId()]
		);

		$this->addPveEntityType(
			"voracious-spider",
			"Voracious Spider",
			4,
			10,
			EntityIds::SPIDER,
			300,
			true,
			12,
			30,
			false,
			0.72,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 10)], //todo: coins
			[],
		);

		$this->addPveEntityType(
			"splitter-silverfish",
			"Silverfish",
			1,
			2,
			EntityIds::SILVERFISH,
			50,
			true,
			12,
			25,
			true,
			0.72,
			[new LB(VanillaItems::STRING(), 80)],
		);

		$this->addPveEntityType(
			"splitter-spider-42",
			"Splitter Spider",
			10,
			42,
			EntityIds::SPIDER,
			4500,
			true,
			28,
			550,
			true,
			0.72,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 10)], //todo: coins
			[SplitterSpiderAbility::getId()],
		);

		$this->addPveEntityType(
			"splitter-spider-50",
			"Splitter Spider",
			15,
			50,
			EntityIds::SPIDER,
			9000,
			true,
			40,
			850,
			true,
			0.72,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 10)], //todo: coins
			[SplitterSpiderAbility::getId()],
		);


		$this->addPveEntityType(
			"dasher-spider-42",
			"Dasher Spider",
			10,
			42,
			EntityIds::SPIDER,
			3350,
			true,
			10,
			435,
			true,
			1,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 80), new LB(SkyblockItems::ARACHNES_CALLING(), 0.2)],
		);

		$this->addPveEntityType(
			"dasher-spider-50",
			"Dasher Spider",
			20,
			50,
			EntityIds::SPIDER,
			7150,
			true,
			32,
			980,
			true,
			1.1,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 80), new LB(SkyblockItems::ARACHNES_CALLING(), 0.2)],
		);


		$this->addPveEntityType(
			"weaver-spider-42",
			"Weaver Spider",
			10,
			42,
			EntityIds::SPIDER,
			4600,
			true,
			10,
			450,
			true,
			0.6,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 80), new LB(SkyblockItems::ARACHNES_CALLING(), 0.02)],
		);

		$this->addPveEntityType(
			"weaver-spider-50",
			"Weaver Spider",
			18,
			42,
			EntityIds::SPIDER,
			11600,
			true,
			32,
			1050,
			true,
			0.6,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 80), new LB(SkyblockItems::ARACHNES_CALLING(), 0.02)],
		);


		$this->addPveEntityType(
			"voracious-spider-42",
			"Voracious Spider",
			10,
			42,
			EntityIds::SPIDER,
			5600,
			true,
			10,
			550,
			true,
			0.6,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 80)],
		);

		$this->addPveEntityType(
			"voracious-spider-50",
			"Voracious Spider",
			10,
			50,
			EntityIds::SPIDER,
			15600,
			true,
			38,
			1235,
			true,
			0.6,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 10)],
		);


		$this->addPveEntityType(
			"arachnes-brood",
			"Arachne's Brood",
			800,
			100,
			EntityIds::SPIDER,
			5000,
			true,
			80,
			200,
			true,
			0.9,
			[new LB(VanillaItems::STRING(), 100), new LB(VanillaItems::SPIDER_EYE(), 50)],
			[PveMagicImmuneAbility::getId()],
		);

		$this->addPveEntityType(
			"arachne-boss-300",
			"Arachne Boss",
			2000,
			300,
			EntityIds::SPIDER,
			40000,
			true,
			200,
			300,
			true,
			0.6,
			[
				new LB(VanillaItems::SPIDER_EYE(), 100, 10, 20),
				new LB(SkyblockItems::ENCHANTED_SPIDER_EYE(), 100, 1, 2),
				new LB(VanillaItems::STRING(), 100, 10, 20),
				new LB(SkyblockItems::ENCHANTED_STRING(), 100, 1, 2),
				new LB(SkyblockItems::ARACHNE_HELMET(), 10),
				new LB(SkyblockItems::ARACHNE_CHESTPLATE(), 10),
				new LB(SkyblockItems::ARACHNE_LEGGINGS(), 10),
				new LB(SkyblockItems::ARACHNE_BOOTS(), 10),
				new LB(SkyblockItems::SOUL_STRING(), 100),
				new LB(SkyblockItems::ARACHNE_FRAGMENT(), 100),
			],
			[ArachneBossAbility::getId(), PveMagicImmuneAbility::getId()]
		);

		$this->addPveEntityType("skeleton-3", "Skeleton", 2, 3, EntityIds::SKELETON, 150, true, 3, 19, true, 0.1, [new LootboxItem(VanillaItems::BONE(), 100, 1, 3)], [SkeletonBowShootAbility::getId()], new PveEntityEquipment(VanillaItems::BOW()));
		$this->addPveEntityType("skeleton-8", "Skeleton", 4, 8, EntityIds::SKELETON, 325, true, 8, 39, true, 0.1, [new LootboxItem(VanillaItems::BONE(), 100, 1, 5)], [SkeletonBowShootAbility::getId()], new PveEntityEquipment(VanillaItems::BOW()));

	}

	public function addPveEntityType(string $name, string $displayName, int $coins, int $level, string $entityID, int $health, bool $hostile, float $combatXp, float $damage = 0, bool $targetsFirst = false, float $speed = 0.3, array $drops, array $abilities = [], ?PveEntityEquipment $equipment = null): void {
		$id = "";
		$totalDrops = 0;

		if(!empty($drops)){
			$this->loottables[$id = uniqid()] = $r = new WeightedRandom();
			/** @var LB $lootboxItem */
			foreach($drops as $lootboxItem){
				if((float) $lootboxItem->getChance() === (float) 100.0){
					$totalDrops++;
				}

				$r->add($lootboxItem, $lootboxItem->getChance());
			}

			$r->setup();
		}

		$compound = new CompoundTag();
		$compound->setString("custom_name", $name);
		$compound->setString("custom_entity_id", $entityID);
		$compound->setInt("custom_health", $health);
		$compound->setInt("custom_coins", $coins);
		$compound->setByte("custom_hostile", (int) $hostile);
		$compound->setFloat("custom_damage", $damage);
		$compound->setByte("custom_targetsFirst", (int) $targetsFirst);
		$compound->setFloat("custom_speed", $speed);
		$compound->setString("custom_displayName", $displayName);
		$compound->setInt("custom_level", $level);
		$compound->setInt("custom_totaldrops", max(1, $totalDrops));
		$compound->setFloat("custom_combat_xp", $combatXp);
		$compound->setString("custom_loottable", $id);
		$compound->setString("abilities", json_encode($abilities));
		if($equipment !== null){
			$compound->setString("equipment", json_encode($equipment));
		}

		$this->entities[$name] = ["networkID" => $entityID, "nbt" => $compound];
	}

	public function getAbility(string $id): ?MobAbility {
		return $this->mobAbilities[$id] ?? null;
	}

	/**
	 * @return array
	 */
	public function getEntities() : array{
		return $this->entities;
	}

	/**
	 * @return WeightedRandom[]
	 */
	public function getLoottables() : array{
		return $this->loottables;
	}

	/**
	 * @return World
	 */
	public function getPveWorld() : World{
		return $this->pveWorld;
	}


}