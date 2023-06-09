<?php

declare(strict_types=1);

namespace skyblock\items;

use customiesdevs\customies\item\CustomiesItemFactory;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use skyblock\items\accessory\EmeraldRingAccessory;
use skyblock\items\accessory\ExperienceArtifact;
use skyblock\items\accessory\FarmingTalisman;
use skyblock\items\accessory\HealingRingAccessory;
use skyblock\items\accessory\HealingTalismanAccessory;
use skyblock\items\accessory\SpeedArtifactAccessory;
use skyblock\items\accessory\SpeedRingAccessory;
use skyblock\items\accessory\SpeedTalismanAccessory;
use skyblock\items\accessory\types\FarmingTalismanAccessory;
use skyblock\items\accessory\VaccineTalismanAccessory;
use skyblock\items\accessory\WoodAffinityTalismanAccessory;
use skyblock\items\armor\angler\AnglerBoots;
use skyblock\items\armor\angler\AnglerChestplate;
use skyblock\items\armor\angler\AnglerHelmet;
use skyblock\items\armor\angler\AnglerLeggings;
use skyblock\items\armor\arachne\ArachneBoots;
use skyblock\items\armor\arachne\ArachneChestplate;
use skyblock\items\armor\arachne\ArachneHelmet;
use skyblock\items\armor\arachne\ArachneLeggings;
use skyblock\items\armor\emerald\EmeraldBoots;
use skyblock\items\armor\emerald\EmeraldChestplate;
use skyblock\items\armor\emerald\EmeraldHelmet;
use skyblock\items\armor\emerald\EmeraldLeggings;
use skyblock\items\armor\farm\FarmBoots;
use skyblock\items\armor\farm\FarmChestplate;
use skyblock\items\armor\farm\FarmHelmet;
use skyblock\items\armor\farm\FarmLeggings;
use skyblock\items\armor\farm_suit\FarmSuitBoots;
use skyblock\items\armor\farm_suit\FarmSuitChestplate;
use skyblock\items\armor\farm_suit\FarmSuitHelmet;
use skyblock\items\armor\farm_suit\FarmSuitLeggings;
use skyblock\items\armor\golem\GolemBoots;
use skyblock\items\armor\golem\GolemChestplate;
use skyblock\items\armor\golem\GolemHelmet;
use skyblock\items\armor\golem\GolemLeggings;
use skyblock\items\armor\growth\GrowthBoots;
use skyblock\items\armor\growth\GrowthChestplate;
use skyblock\items\armor\growth\GrowthHelmet;
use skyblock\items\armor\growth\GrowthLeggings;
use skyblock\items\armor\hardened_diamond\HardenedDiamondBoots;
use skyblock\items\armor\hardened_diamond\HardenedDiamondChestplate;
use skyblock\items\armor\hardened_diamond\HardenedDiamondHelmet;
use skyblock\items\armor\hardened_diamond\HardenedDiamondLeggings;
use skyblock\items\armor\lapis\LapisBoots;
use skyblock\items\armor\lapis\LapisChestplate;
use skyblock\items\armor\lapis\LapisHelmet;
use skyblock\items\armor\lapis\LapisLeggings;
use skyblock\items\armor\leaflet\LeafletBoots;
use skyblock\items\armor\leaflet\LeafletChestplate;
use skyblock\items\armor\leaflet\LeafletHelmet;
use skyblock\items\armor\leaflet\LeafletLeggings;
use skyblock\items\armor\miner\MinerBoots;
use skyblock\items\armor\miner\MinerChestplate;
use skyblock\items\armor\miner\MinerHelmet;
use skyblock\items\armor\miner\MinerLeggings;
use skyblock\items\armor\pumpkin\PumpkinBoots;
use skyblock\items\armor\pumpkin\PumpkinChestplate;
use skyblock\items\armor\pumpkin\PumpkinHelmet;
use skyblock\items\armor\pumpkin\PumpkinLeggings;
use skyblock\items\armor\speedster\SpeedsterBoots;
use skyblock\items\armor\speedster\SpeedsterChestplate;
use skyblock\items\armor\speedster\SpeedsterHelmet;
use skyblock\items\armor\speedster\SpeedsterLeggings;
use skyblock\items\armor\zombie\ZombieBoots;
use skyblock\items\armor\zombie\ZombieChestplate;
use skyblock\items\armor\zombie\ZombieHelmet;
use skyblock\items\armor\zombie\ZombieLeggings;
use skyblock\items\crafting\SkyBlockEnchantedItem;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\masks\MaskItem;
use skyblock\items\misc\ArachneFragment;
use skyblock\items\misc\ArachnesCalling;
use skyblock\items\misc\carrot_candy\GreatCarrotCandy;
use skyblock\items\misc\carrot_candy\SimpleCarrotCandy;
use skyblock\items\misc\carrot_candy\SuperbCarrotCandy;
use skyblock\items\misc\carrot_candy\UltimateCarrotCandy;
use skyblock\items\misc\carrot_candy\UltimateCarrotCandyUpgrade;
use skyblock\items\misc\EnchantmentBook;
use skyblock\items\misc\HotPotatoBook;
use skyblock\items\misc\HyperFurnace;
use skyblock\items\misc\minion\AutoSmelter;
use skyblock\items\misc\minion\BudgetHopper;
use skyblock\items\misc\minion\Compactor;
use skyblock\items\misc\minion\DiamondSpreading;
use skyblock\items\misc\minion\EnchantedEgg;
use skyblock\items\misc\minion\EnchantedHopper;
use skyblock\items\misc\minion\EnchantedLavaBucket;
use skyblock\items\misc\minion\StorageChest;
use skyblock\items\misc\minion\SuperCompactor3000;
use skyblock\items\misc\PersonalCompactor4000;
use skyblock\items\misc\PolishedPumpkin;
use skyblock\items\misc\SkyblockMenuItem;
use skyblock\items\misc\SoulString;
use skyblock\items\misc\storagesack\AgronomyStorageSack;
use skyblock\items\misc\storagesack\CombatStorageSack;
use skyblock\items\misc\storagesack\FishingStorageSack;
use skyblock\items\misc\storagesack\ForagingStorageSack;
use skyblock\items\misc\storagesack\HusbandryStorageSack;
use skyblock\items\misc\storagesack\MiningStorageSack;
use skyblock\items\misc\SuperEchantedEgg;
use skyblock\items\pets\MysteryPetItem;
use skyblock\items\pets\PetItem;
use skyblock\items\rarity\Rarity;
use skyblock\items\special\types\crafting\EnchantedCarrot;
use skyblock\items\special\types\CustomEnchantmentBook;
use skyblock\items\tools\FarmersRod;
use skyblock\items\tools\FlintShovel;
use skyblock\items\tools\GraplingHook;
use skyblock\items\tools\JungleAxe;
use skyblock\items\tools\RodOfChampions;
use skyblock\items\tools\RodOfLegends;
use skyblock\items\tools\SkyBlockAxe;
use skyblock\items\tools\SkyBlockFishingRod;
use skyblock\items\tools\SkyBlockHoe;
use skyblock\items\tools\SkyBlockPickaxe;
use skyblock\items\tools\SkyBlockShovel;
use skyblock\items\tools\Treecapacitor;
use skyblock\items\tools\ZombiePickaxe;
use skyblock\items\weapons\AspectOfTheEndSword;
use skyblock\items\weapons\CleaverSword;
use skyblock\items\weapons\DreadlordSword;
use skyblock\items\weapons\EmeraldBlade;
use skyblock\items\weapons\EnderBow;
use skyblock\items\weapons\ExplosiveBow;
use skyblock\items\weapons\GolemSword;
use skyblock\items\weapons\HurricaneBow;
use skyblock\items\weapons\InkWand;
use skyblock\items\weapons\PigmanSword;
use skyblock\items\weapons\RogueSword;
use skyblock\items\weapons\RunaansBow;
use skyblock\items\weapons\SavannaBow;
use skyblock\items\weapons\SpiderSword;
use skyblock\items\weapons\YetiSword;
use skyblock\items\weapons\ZombieSword;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

class SkyblockItemFactory{
	use AetherHandlerTrait;
	use AwaitStdTrait;

	public function onEnable() : void{
		$this->registerItems();
		$this->registerEquipment();
		$this->registerVanillaArmor();

		$this->registerEnchantedItems();

		$this->registerMisc();
		$this->registerMinion();
		$this->registerAccessory();

		$this->registerCustom(LapisBoots::class, "lapis_lazuli_boots");
		$this->registerCustom(LapisHelmet::class, "lapis_lazuli_helmet");
		$this->registerCustom(LapisChestplate::class, "lapis_lazuli_chestplate");
		$this->registerCustom(LapisLeggings::class, "lapis_lazuli_leggings");

		$this->registerCustom(GrowthBoots::class, "growth_boots");
		$this->registerCustom(GrowthLeggings::class, "growth_leggings");
		$this->registerCustom(GrowthChestplate::class, "growth_chestplate");
		$this->registerCustom(GrowthHelmet::class, "growth_helmet");

		$this->registerCustom(HardenedDiamondBoots::class, "hardened_diamond_boots");
		$this->registerCustom(HardenedDiamondLeggings::class, "hardened_diamond_leggings");
		$this->registerCustom(HardenedDiamondChestplate::class, "hardened_diamond_chestplate");
		$this->registerCustom(HardenedDiamondHelmet::class, "hardened_diamond_helmet");

		$this->registerCustom(AnglerBoots::class, "angler_boots");
		$this->registerCustom(AnglerLeggings::class, "angler_leggings");
		$this->registerCustom(AnglerChestplate::class, "angler_chestplate");
		$this->registerCustom(AnglerHelmet::class, "angler_helmet");

		$this->registerCustom(MinerBoots::class, "miner_boots");
		$this->registerCustom(MinerLeggings::class, "miner_leggings");
		$this->registerCustom(MinerChestplate::class, "miner_chestplate");
		$this->registerCustom(MinerHelmet::class, "miner_helmet");

		$this->registerCustom(LeafletBoots::class, "leaflet_boots");
		$this->registerCustom(LeafletLeggings::class, "leaflet_leggings");
		$this->registerCustom(LeafletChestplate::class, "leaflet_chestplate");
		$this->registerCustom(LeafletHelmet::class, "leaflet_helmet");

		$this->registerCustom(SpeedsterBoots::class, "speedster_boots");
		$this->registerCustom(SpeedsterLeggings::class, "speedster_leggings");
		$this->registerCustom(SpeedsterChestplate::class, "speedster_chestplate");
		$this->registerCustom(SpeedsterHelmet::class, "speedster_helmet");


		$this->registerCustom(PumpkinBoots::class, "pumpkin_boots");
		$this->registerCustom(PumpkinLeggings::class, "pumpkin_leggings");
		$this->registerCustom(PumpkinChestplate::class, "pumpkin_chestplate");
		$this->registerCustom(PumpkinHelmet::class, "pumpkin_helmet");

		$this->registerCustom(FarmBoots::class, "farm_boots");
		$this->registerCustom(FarmLeggings::class, "farm_leggings");
		$this->registerCustom(FarmChestplate::class, "farm_chestplate");
		$this->registerCustom(FarmHelmet::class, "farm_helmet");

		$this->registerCustom(FarmSuitBoots::class, "farm_suit_boots");
		$this->registerCustom(FarmSuitLeggings::class, "farm_suit_leggings");
		$this->registerCustom(FarmSuitChestplate::class, "farm_suit_chestplate");
		$this->registerCustom(FarmSuitHelmet::class, "farm_suit_helmet");

		$this->registerCustom(GolemBoots::class, "golem_boots");
		$this->registerCustom(GolemLeggings::class, "golem_leggings");
		$this->registerCustom(GolemChestplate::class, "golem_chestplate");
		$this->registerCustom(GolemHelmet::class, "golem_helmet");

		$this->registerCustom(ZombieBoots::class, "zombie_boots");
		$this->registerCustom(ZombieLeggings::class, "zombie_leggings");
		$this->registerCustom(ZombieChestplate::class, "zombie_chestplate");
		$this->registerCustom(ZombieHelmet::class, "zombie_helmet");

		$this->registerCustom(ArachneBoots::class, "arachne_boots");
		$this->registerCustom(ArachneLeggings::class, "arachne_leggings");
		$this->registerCustom(ArachneChestplate::class, "arachne_chestplate");
		$this->registerCustom(ArachneHelmet::class, "arachne_helmet");

		$this->registerCustom(EmeraldBoots::class, "emerald_boots");
		$this->registerCustom(EmeraldLeggings::class, "emerald_leggings");
		$this->registerCustom(EmeraldChestplate::class, "emerald_chestplate");
		$this->registerCustom(EmeraldHelmet::class, "emerald_helmet");
	}

	public function registerMinion(): void {
		$this->register(new AutoSmelter(new ItemIdentifier(ItemIds::FURNACE, 1)));
		$this->register(new Compactor(new ItemIdentifier(ItemIds::DROPPER, 2)));
		$this->register(new StorageChest(new ItemIdentifier(ItemIds::CHEST, 3)));
		$this->register(new SuperCompactor3000(new ItemIdentifier(ItemIds::DROPPER, 4)));

		$this->registerCustom(BudgetHopper::class, "budget_hopper");
		$this->registerCustom(DiamondSpreading::class, "diamond_spreading");
		$this->registerCustom(EnchantedHopper::class, "enchanted_hopper");
		$this->registerCustom(EnchantedLavaBucket::class, "enchanted_lava_bucket");
	}

	public function registerMisc(): void {
		$this->register(new PolishedPumpkin(new ItemIdentifier(ItemIds::PUMPKIN, 1)));
		$this->register(new SoulString(new ItemIdentifier(ItemIds::STRING, 2)));
		$this->register(new SkyblockMenuItem(new ItemIdentifier(ItemIds::NETHER_STAR, 1)));
		$this->register(new PersonalCompactor4000(new ItemIdentifier(ItemIds::DROPPER, 1)));

		$this->register(new MaskItem(new ItemIdentifier(ItemIds::NETHER_STAR, 2)));



		$this->register(new HusbandryStorageSack(new ItemIdentifier(ItemIds::BARREL, 1)));
		$this->register(new FishingStorageSack(new ItemIdentifier(ItemIds::BARREL, 2)));
		$this->register(new AgronomyStorageSack(new ItemIdentifier(ItemIds::BARREL, 3)));
		$this->register(new ForagingStorageSack(new ItemIdentifier(ItemIds::BARREL, 4)));
		$this->register(new MiningStorageSack(new ItemIdentifier(ItemIds::BARREL, 5)));
		$this->register(new CombatStorageSack(new ItemIdentifier(ItemIds::BARREL, 6)));

		$this->register(new HyperFurnace(new ItemIdentifier(ItemIds::FURNACE, 2)));

		$this->registerCustom(ArachneFragment::class, "arachne_fragment");

		$this->registerCustom(HotPotatoBook::class, "hot_potato_book");


		$this->registerCustom(SimpleCarrotCandy::class, "simple_carrot_candy");
		$this->registerCustom(GreatCarrotCandy::class, "great_carrot_candy");
		$this->registerCustom(SuperbCarrotCandy::class, "superb_carrot_candy");
		$this->registerCustom(UltimateCarrotCandy::class, "ultimate_carrot_candy");

		$this->register(new UltimateCarrotCandyUpgrade(new ItemIdentifier(ItemIds::BOOK, 2)));

		$this->register(new ArachnesCalling(new ItemIdentifier(ItemIds::CHEMICAL_HEAT, 2)));
		
		$this->register(new EnchantmentBook(new ItemIdentifier(ItemIds::WRITABLE_BOOK, 2)));

		$this->registerCustom(SuperEchantedEgg::class, "super_enchanted_egg");
		$this->registerCustom(PetItem::class, "pet_item");
		$this->registerCustom(MysteryPetItem::class, "mystery_pet_item");
	}

	public function registerEnchantedItems(): void {
		$this->register(new EnchantedEgg(new ItemIdentifier(ItemIds::EGG, 1)));

		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::STRIPPED_ACACIA_LOG, 1), "Enchanted Acacia Wood"))->setRecipeItem(fn() => VanillaBlocks::ACACIA_LOG()->asItem()->setCount(32)));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::STRIPPED_BIRCH_LOG, 1), "Enchanted Birch Wood"))->setRecipeItem(fn() => VanillaBlocks::BIRCH_LOG()->asItem()->setCount(32)));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::STRIPPED_OAK_LOG, 1), "Enchanted Oak Wood"))->setRecipeItem(fn() => VanillaBlocks::OAK_LOG()->asItem()->setCount(32)));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::STRIPPED_DARK_OAK_LOG, 1), "Enchanted Dark Oak Wood"))->setRecipeItem(fn() => VanillaBlocks::DARK_OAK_LOG()->asItem()->setCount(32)));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::STRIPPED_SPRUCE_LOG, 1), "Enchanted Spruce Wood"))->setRecipeItem(fn() => VanillaBlocks::SPRUCE_LOG()->asItem()->setCount(32)));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::STRIPPED_JUNGLE_LOG, 1), "Enchanted Jungle Wood"))->setRecipeItem(fn() => VanillaBlocks::JUNGLE_LOG()->asItem()->setCount(32)));


		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::BAKED_POTATO, 1), "Enchanted Baked Potato"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::BEEF, 1), "Enchanted Beef"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::BLAZE_ROD, 1), "Enchanted Blaze Rod"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::BONE, 1), "Enchanted Bone"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::BREAD, 1), "Enchanted Bread"))
			->setRecipeItem(fn() => VanillaItems::WHEAT()->setCount(10))
		);

		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::CACTUS, 1), "Enchanted Cactus"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(942, 1), "Enchanted Cactus Green"));

		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::CARROT, 1), "Enchanted Carrot"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::CHICKEN, 1), "Enchanted Chicken"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::CLAY, 1), "Enchanted Clay"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::COAL, 1), "Enchanted Coal"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::COAL_BLOCK, 1), "Enchanted Coal Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_COAL()->setCount(32)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::COBBLESTONE, 1), "Enchanted Cobblestone"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::COOKED_FISH, 1), "Enchanted Cooked Fish"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_RAW_FISH()->setCount(32)));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::COOKED_MUTTON, 1), "Enchanted Cooked Mutton"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_COOKED_MUTTON()));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::DIAMOND, 1), "Enchanted Diamond"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::DIAMOND_BLOCK, 1), "Enchanted Diamond Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_DIAMOND()->setCount(32)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::EMERALD, 1), "Enchanted Emerald"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::EMERALD_BLOCK, 1), "Enchanted Emerald Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_EMERALD()->setCount(32)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::ENDER_PEARL, 1), "Enchanted Ender Pearl"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::ENDER_EYE, 1), "Enchanted Eye Of Ender"))
			->setMiddleItem(fn() => SkyblockItems::ENCHANTED_ENDER_PEARL()->setCount(16))
			->setRecipeItem(fn() => VanillaItems::BLAZE_POWDER())->setCount(16)
		);
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::FEATHER, 1), "Enchanted Feather"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::FERMENTED_SPIDER_EYE, 1), "Enchanted Fermented Spider Eye"))
			->setRecipeItem(fn() => SkyblockItems::ENCHANTED_SPIDER_EYE()->setCount(16))
			->setMiddleItem(fn() => VanillaBlocks::BROWN_MUSHROOM()->asItem()->setCount(64)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::FLINT, 1), "Enchanted Flint"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::GLISTERING_MELON, 1), "Enchanted Glistering Melon"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::GLOWSTONE, 1), "Enchanted Glowstone"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::GLOWSTONE_DUST, 1), "Enchanted Glowstone Dust"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::GOLD_INGOT, 1), "Enchanted Gold"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::GOLD_BLOCK, 1), "Enchanted Gold Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_GOLD()->setCount(32)));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::GOLDEN_CARROT, 1), "Enchanted Golden Carrot"))->setMiddleItem(fn() => VanillaItems::GOLDEN_CARROT()->setCount(32))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_CARROT()->setCount(32)));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::COOKED_PORKCHOP, 1), "Enchanted Grilled Pork"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_PORKCHOP()->setCount(32)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::GUNPOWDER, 1), "Enchanted Gunpowder"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::HAY_BALE, 1), "Enchanted Hay Bale")));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(941, 1), "Enchanted Ink Sac"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::IRON_INGOT, 1), "Enchanted Iron"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::IRON_BLOCK, 1), "Enchanted Iron Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_IRON()->setCount(32)));

		//custom id, customitemloader plugin
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(938, 1), "Enchanted Lapis Lazuli"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::LAPIS_BLOCK, 1), "Enchanted Lapis Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_LAPIS_LAZULI()->setCount(32)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::LEATHER, 1), "Enchanted Leather"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::LILY_PAD, 1), "Enchanted Lily Pad"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::MAGMA_CREAM, 1), "Enchanted Magma Cream"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::MELON_BLOCK, 1), "Enchanted Melon Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_MELON()->setCount(32)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::MELON, 1), "Enchanted Melon"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::MUTTON, 1), "Enchanted Mutton"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::OBSIDIAN, 1), "Enchanted Obsidian"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::PAPER, 1), "Enchanted Paper"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::PORKCHOP, 1), "Enchanted Porkchop"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::POTATO, 1), "Enchanted Potato"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::PUFFERFISH, 1), "Enchanted Pufferfish"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::PUMPKIN, 1), "Enchanted Pumpkin"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::QUARTZ, 1), "Enchanted Quartz"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::RABBIT, 1), "Enchanted Rabbit"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::RABBIT_FOOT, 1), "Enchanted Rabbit Foot"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::RABBIT_HIDE, 1), "Enchanted Rabbit Hide"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::RAW_FISH, 1), "Enchanted Raw Fish"));
		//TOOD: customitemloader sand and red sand  $this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::REDSA, 1), "Enchanted Raw Fish"));

		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(939, 1), "Enchanted Sand"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(940, 1), "Enchanted Red Sand"));


		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::REDSTONE_BLOCK, 1), "Enchanted Redstone Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_REDSTONE()->setCount(32)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::REDSTONE, 1), "Enchanted Redstone"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::ROTTEN_FLESH, 1), "Enchanted Rottenflesh"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::SLIMEBALL, 1), "Enchanted Slimeball"));
		$this->register((new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::SLIME_BLOCK, 1), "Enchanted Slime Block"))->setRecipeItem(fn() => SkyblockItems::ENCHANTED_SLIMEBALL()->setCount(32)));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::SPIDER_EYE, 1), "Enchanted Spider Eye"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::STRING, 1), "Enchanted String"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::SUGAR, 1), "Enchanted Sugar"));
		$this->register(new SkyBlockEnchantedItem(new ItemIdentifier(ItemIds::SUGARCANE, 1), "Enchanted Sugarcane"));
	}

	public function registerItems() : void{
		$this->register((new SkyBlockPickaxe(new ItemIdentifier(ItemIds::WOODEN_PICKAXE, 0), "Wooden Pickaxe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::MINING_SPEED(), 70))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 15))
		);

		$this->register((new SkyBlockPickaxe(new ItemIdentifier(ItemIds::IRON_PICKAXE, 0), "Iron Pickaxe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::MINING_SPEED(), 160))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 25))
		);

		$this->register((new SkyBlockPickaxe(new ItemIdentifier(ItemIds::STONE_PICKAXE, 0), "Stone Pickaxe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::MINING_SPEED(), 110))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 20))
		);

		$this->register((new SkyBlockPickaxe(new ItemIdentifier(ItemIds::GOLDEN_PICKAXE, 0), "Golden Pickaxe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::MINING_SPEED(), 250))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 15))
		);

		$this->register((new SkyBlockPickaxe(new ItemIdentifier(ItemIds::DIAMOND_PICKAXE, 0), "Diamond Pickaxe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::MINING_SPEED(), 230))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 30))
		);


		$this->register((new SkyBlockWeapon(new ItemIdentifier(ItemIds::WOODEN_SWORD, 0), "Wooden Sword"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 20))
		);

		$this->register((new SkyBlockWeapon(new ItemIdentifier(ItemIds::IRON_SWORD, 0), "Iron Sword"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 30))
		);

		$this->register((new SkyBlockWeapon(new ItemIdentifier(ItemIds::STONE_SWORD, 0), "Stone Sword"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 25))
		);

		$this->register((new SkyBlockWeapon(new ItemIdentifier(ItemIds::GOLDEN_SWORD, 0), "Golden Sword"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 20))
		);

		$this->register((new SkyBlockWeapon(new ItemIdentifier(ItemIds::DIAMOND_SWORD, 0), "Diamond Sword"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 35))
		);


		$this->register((new SkyBlockAxe(new ItemIdentifier(ItemIds::WOODEN_AXE, 0), "Wooden Axe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 10))
		);

		$this->register((new SkyBlockAxe(new ItemIdentifier(ItemIds::IRON_AXE, 0), "Iron Axe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 25))
		);

		$this->register((new SkyBlockAxe(new ItemIdentifier(ItemIds::STONE_AXE, 0), "Stone Axe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 15))
		);

		$this->register((new SkyBlockAxe(new ItemIdentifier(ItemIds::GOLDEN_AXE, 0), "Golden Axe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 20))
		);

		$this->register((new SkyBlockAxe(new ItemIdentifier(ItemIds::DIAMOND_AXE, 0), "Diamond Axe"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 30))
		);


		$this->register((new SkyBlockShovel(new ItemIdentifier(ItemIds::WOODEN_SHOVEL, 0), "Wooden Shovel"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 15))
		);

		$this->register((new SkyBlockShovel(new ItemIdentifier(ItemIds::IRON_SHOVEL, 0), "Iron Shovel"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 25))
		);

		$this->register((new SkyBlockShovel(new ItemIdentifier(ItemIds::STONE_SHOVEL, 0), "Stone Shovel"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 20))
		);

		$this->register((new SkyBlockShovel(new ItemIdentifier(ItemIds::GOLDEN_SHOVEL, 0), "Golden Shovel"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 15))
		);

		$this->register((new SkyBlockShovel(new ItemIdentifier(ItemIds::DIAMOND_SHOVEL, 0), "Diamond Shovel"))
			->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 30))
		);


		$this->register((new SkyBlockHoe(new ItemIdentifier(ItemIds::WOODEN_HOE, 0), "Wooden Hoe")));

		$this->register((new SkyBlockHoe(new ItemIdentifier(ItemIds::IRON_HOE, 0), "Iron Hoe")));

		$this->register((new SkyBlockHoe(new ItemIdentifier(ItemIds::STONE_HOE, 0), "Stone Hoe")));

		$this->register((new SkyBlockHoe(new ItemIdentifier(ItemIds::GOLDEN_HOE, 0), "Golden Hoe")));

		$this->register((new SkyBlockHoe(new ItemIdentifier(ItemIds::DIAMOND_HOE, 0), "Diamond Hoe")));

	}

	public function registerVanillaArmor(): void {
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::DIAMOND_HELMET, 0), "Diamond Helmet", new SkyBlockArmorInfo(15, ArmorInventory::SLOT_HEAD, Rarity::uncommon())));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::DIAMOND_CHESTPLATE, 0), "Diamond Chestplate", new SkyBlockArmorInfo(40, ArmorInventory::SLOT_CHEST, Rarity::uncommon())));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::DIAMOND_LEGGINGS, 0), "Diamond Leggings",new SkyBlockArmorInfo(30, ArmorInventory::SLOT_LEGS, Rarity::uncommon())));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::DIAMOND_BOOTS, 0), "Diamond Boots", new SkyBlockArmorInfo(15, ArmorInventory::SLOT_FEET, Rarity::uncommon())));


		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::IRON_HELMET, 0), "Iron Helmet", new SkyBlockArmorInfo(10, ArmorInventory::SLOT_HEAD)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::IRON_CHESTPLATE, 0), "Iron Chestplate", new SkyBlockArmorInfo(30, ArmorInventory::SLOT_CHEST)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::IRON_LEGGINGS, 0), "Iron Leggings",new SkyBlockArmorInfo(25, ArmorInventory::SLOT_LEGS)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::IRON_BOOTS, 0), "Iron Boots", new SkyBlockArmorInfo(10, ArmorInventory::SLOT_FEET)));


		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::GOLD_HELMET, 0), "Golden Helmet", new SkyBlockArmorInfo(10, ArmorInventory::SLOT_HEAD)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::GOLD_CHESTPLATE, 0), "Golden Chestplate", new SkyBlockArmorInfo(25, ArmorInventory::SLOT_CHEST)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::GOLD_LEGGINGS, 0), "Golden Leggings",new SkyBlockArmorInfo(15, ArmorInventory::SLOT_LEGS)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::GOLD_BOOTS, 0), "Golden Boots", new SkyBlockArmorInfo(5, ArmorInventory::SLOT_FEET)));


		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::LEATHER_HELMET, 0), "Leather Helmet", new SkyBlockArmorInfo(5, ArmorInventory::SLOT_HEAD)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::LEATHER_CHESTPLATE, 0), "Leather Chestplate", new SkyBlockArmorInfo(15, ArmorInventory::SLOT_CHEST)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::LEATHER_LEGGINGS, 0), "Leather Leggings",new SkyBlockArmorInfo(10, ArmorInventory::SLOT_LEGS)));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::LEATHER_BOOTS, 0), "Leather Boots", new SkyBlockArmorInfo(5, ArmorInventory::SLOT_FEET)));


		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::CHAINMAIL_HELMET, 0), "Chainmail Helmet", new SkyBlockArmorInfo(12, ArmorInventory::SLOT_HEAD, Rarity::uncommon())));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::CHAINMAIL_CHESTPLATE, 0), "Chainmail Chestplate", new SkyBlockArmorInfo(30, ArmorInventory::SLOT_CHEST, Rarity::uncommon())));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::CHAINMAIL_LEGGINGS, 0), "Chainmail Leggings",new SkyBlockArmorInfo(20, ArmorInventory::SLOT_LEGS, Rarity::uncommon())));
		$this->register(new SkyblockArmor(new ItemIdentifier(ItemIds::CHAINMAIL_BOOTS, 0), "Chainmail Boots", new SkyBlockArmorInfo(7, ArmorInventory::SLOT_FEET, Rarity::uncommon())));
	}
	
	public function registerAccessory(): void {
		$this->registerCustom(SpeedArtifactAccessory::class, "speed_artifact");
		$this->registerCustom(SpeedRingAccessory::class, "speed_ring");
		$this->registerCustom(SpeedTalismanAccessory::class, "speed_talisman");
		$this->registerCustom(WoodAffinityTalismanAccessory::class, "wood_affinity_talisman");
		$this->registerCustom(HealingRingAccessory::class, "healing_ring");
		$this->registerCustom(HealingTalismanAccessory::class, "healing_talisman");
		$this->registerCustom(ExperienceArtifact::class, "experience_artifact");
		$this->registerCustom(VaccineTalismanAccessory::class, "vaccine_talisman");
		$this->registerCustom(FarmingTalisman::class, "farming_talisman");
		$this->registerCustom(EmeraldRingAccessory::class, "emerald_ring");
	}

	public function registerEquipment() : void{


		$this->registerCustom(RogueSword::class, "rogue_sword", "§r§g§lRogue Sword §r§7(right-click)");

		//CustomiesItemFactory::getInstance()->registerItem(CleaverSword::class, "customies:custom_redsand", "Cleaver Sword");
		$this->registerCustom(CleaverSword::class, "cleaver_sword", "§r§a§lCleaver Sword");
		$this->registerCustom(SpiderSword::class, "spider_sword", "§r§f§lSpider Sword");
		$this->registerCustom(YetiSword::class, "yeti_sword", "§r§6§lYeti Sword §r§7(Right-Click)");
		$this->registerCustom(PigmanSword::class, "pigman_sword", "§r§6§lPigman Sword §r§7(Right-Click)");
		$this->registerCustom(ZombieSword::class, "zombie_sword", "§r§3§lZombie Sword §r§7(Right-Click)");
		$this->registerCustom(GolemSword::class, "golem_sword", "§r§3§lGolem Sword §r§7(Right-Click)");
		$this->registerCustom(DreadlordSword::class, "dreadlord_sword", "§r§3§lDreadlord Sword §r§7(Right-Click)");
		$this->registerCustom(AspectOfTheEndSword::class, "aspect_of_the_end_sword", "§r§3§lAspect Of The End Sword §r§7(Right-Click)");

		$this->registerCustom(GraplingHook::class, "grapling_hook", "§r§aGrapling Hook");

		$this->registerCustom(EmeraldBlade::class, "emerald_sword");


		$this->register(new Treecapacitor(new ItemIdentifier(ItemIds::GOLDEN_AXE, 1),"§r§5§lTreecapacitor"));
		$this->register(new JungleAxe(new ItemIdentifier(ItemIds::WOODEN_AXE, 1),"§r§a§lJungle Axe"));

		$this->register(new FlintShovel(new ItemIdentifier(ItemIds::IRON_SHOVEL, 1), "§r§f§lFlint Shovel"));

		$this->register(new ZombiePickaxe(new ItemIdentifier(ItemIds::IRON_PICKAXE, 1), "§r§fZombie Pickaxe"));

		$this->register(new SavannaBow(new ItemIdentifier(ItemIds::BOW, 1), "§r§aSavanna Bow"));
		$this->register(new ExplosiveBow(new ItemIdentifier(ItemIds::BOW, 2), "§r§5Explosive Bow"));
		$this->register(new EnderBow(new ItemIdentifier(ItemIds::BOW, 3), "§r§3Ender Bow"));
		$this->register(new HurricaneBow(new ItemIdentifier(ItemIds::BOW, 4)));
		$this->register(new RunaansBow(new ItemIdentifier(ItemIds::BOW, 5)));

		$this->register(new InkWand(new ItemIdentifier(ItemIds::STICK, 1), "§r§5§lInk Wand §r§7(Right-Click)"));
		
		$this->register(new SkyBlockFishingRod(new ItemIdentifier(ItemIds::FISHING_ROD, 0), "§r§fFishing Rod"));
		$this->register(new RodOfChampions(new ItemIdentifier(ItemIds::FISHING_ROD, 1), "§r§3§lRod Of Champions"));
		$this->register(new RodOfLegends(new ItemIdentifier(ItemIds::FISHING_ROD, 2), "§r§5§lRod Of Legends"));
		$this->register(new FarmersRod(new ItemIdentifier(ItemIds::FISHING_ROD, 3), "§r§a§lFarmer's Rod"));

	}

	public function get(int $id, int $meta = 0, int $count = 1) : SkyblockItem{
		$item = clone ItemFactory::getInstance()->get($id, $meta, $count);
		if(!$item instanceof SkyblockItem){
			throw new \InvalidArgumentException($item::class . " is not a SkyblockItem.");
		}

		return $item;
	}

	public function getOrNull(int $id, int $meta = 0, int $count = 0) : ?SkyblockItem{
		$item = ItemFactory::getInstance()->get($id, $meta, $count);
		if(!$item instanceof SkyblockItem){
			return null;
		}

		return $item;
	}

	public function registerCustom(string $class, string $id, ?string $name = null, ?string $exceptionId = null): void {
		$identifier = "aetherpe:" . $id;

		CustomiesItemFactory::getInstance()->registerItem($class, $identifier, $name ?? $id);
		SkyblockItems::registerPublic($exceptionId ?? $id, CustomiesItemFactory::getInstance()->get($identifier));
	}

	public function register(SkyblockItem $item) : void{
		ItemFactory::getInstance()->register($item, true);
		if(!CreativeInventory::getInstance()->contains($item)){
			CreativeInventory::getInstance()->add($item);
		}

		if($item instanceof SkyBlockEnchantedItem){
			/** @var SkyBlockEnchantedItem $item */
			Await::f2c(function() use($item) {
				yield $this->getStd()->sleep(2);
				RecipesHandler::getInstance()->registerRecipe($item->getName(), [
					1 => $item->getRecipeItem(),
					3 => $item->getRecipeItem(),
					4 => $item->getMiddleItem() ?? $item->getRecipeItem(),
					5 => $item->getRecipeItem(),
					7 => $item->getRecipeItem()
				], clone $item, 0);
			});
		}

		/*$data = file_get_contents(Main::getInstance()->getDataFolder() . "test.txt"); //GENERATE CONSTANTS FOR SKYBLOCKITEMS::
		if($data === false) $data = "";

		$constant = new ReflectionClass(ItemIds::class);
		foreach($constant->getReflectionConstants() as $con){
			if($con->getValue() === $item->getId()){
				$name = $con->getName();
				$s = str_replace(' ', '_', TextFormat::clean($item->getCustomName()));
				$data .= 'self::register("' . $s . '", $factory->get(ItemIds::' . $name . ', ' . $item->getMeta() . '))' . "\n";

				break;
			}
		}

		file_put_contents(Main::getInstance()->getDataFolder() . "test.txt", $data);*/
	}
}