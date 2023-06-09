<?php

declare(strict_types=1);

namespace skyblock\items;

use customiesdevs\customies\item\CustomiesItemFactory;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\RegistryTrait;
use skyblock\items\accessory\AccessoryItem;
use skyblock\items\crafting\SkyBlockEnchantedItem;
use skyblock\items\masks\MaskItem;
use skyblock\items\misc\EnchantmentBook;
use skyblock\items\misc\minion\AutoSmelter;
use skyblock\items\misc\minion\StorageChest;
use skyblock\items\misc\PersonalCompactor4000;
use skyblock\items\misc\PolishedPumpkin;
use skyblock\items\misc\SoulString;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\items\pets\MysteryPetItem;
use skyblock\items\pets\PetItem;
use skyblock\items\tools\RodOfChampions;
use skyblock\items\tools\RodOfLegends;
use skyblock\items\tools\SkyBlockAxe;
use skyblock\items\tools\SkyBlockFishingRod;
use skyblock\items\tools\SkyBlockPickaxe;
use skyblock\items\tools\SkyBlockShovel;
use skyblock\items\weapons\ExplosiveBow;
use skyblock\items\weapons\SkyBlockBow;

/**
 * @method static SkyblockTool DIAMOND_SWORD()
 * @method static SkyblockTool DIAMOND_PICKAXE()
 * @method static SkyblockArmor DIAMOND_HELMET()
 * @method static SkyblockArmor DIAMOND_CHESTPLATE()
 * @method static SkyblockArmor DIAMOND_LEGGINGS()
 * @method static SkyblockArmor DIAMOND_BOOTS()
 *
 *
 * @method static SkyblockArmor LAPIS_LAZULI_BOOTS
 * @method static SkyblockArmor LAPIS_LAZULI_LEGGINGS
 * @method static SkyblockArmor LAPIS_LAZULI_CHESTPLATE
 * @method static SkyblockArmor LAPIS_LAZULI_HELMET
 * @method static SkyblockArmor ZOMBIE_BOOTS
 * @method static SkyblockArmor ZOMBIE_LEGGINGS
 * @method static SkyblockArmor ZOMBIE_CHESTPLATE
 * @method static SkyblockArmor ZOMBIE_HELMET
 * @method static SkyblockArmor SPEEDSTER_BOOTS
 * @method static SkyblockArmor SPEEDSTER_LEGGINGS
 * @method static SkyblockArmor SPEEDSTER_CHESTPLATE
 * @method static SkyblockArmor SPEEDSTER_HELMET
 * @method static SkyblockArmor PUMPKIN_BOOTS
 * @method static SkyblockArmor PUMPKIN_LEGGINGS
 * @method static SkyblockArmor PUMPKIN_CHESTPLATE
 * @method static SkyblockArmor PUMPKIN_HELMET
 * @method static SkyblockArmor MINER_BOOTS
 * @method static SkyblockArmor MINER_LEGGINGS
 * @method static SkyblockArmor MINER_CHESTPLATE
 * @method static SkyblockArmor MINER_HELMET
 * @method static SkyblockArmor LEAFLET_BOOTS
 * @method static SkyblockArmor LEAFLET_LEGGINGS
 * @method static SkyblockArmor LEAFLET_CHESTPLATE
 * @method static SkyblockArmor LEAFLET_HELMET
 * @method static SkyblockArmor HARDENED_DIAMOND_BOOTS
 * @method static SkyblockArmor HARDENED_DIAMOND_LEGGINGS
 * @method static SkyblockArmor HARDENED_DIAMOND_CHESTPLATE
 * @method static SkyblockArmor HARDENED_DIAMOND_HELMET
 * @method static SkyblockArmor GROWTH_BOOTS
 * @method static SkyblockArmor GROWTH_LEGGINGS
 * @method static SkyblockArmor GROWTH_CHESTPLATE
 * @method static SkyblockArmor GROWTH_HELMET
 * @method static SkyblockArmor GOLEM_BOOTS
 * @method static SkyblockArmor GOLEM_LEGGINGS
 * @method static SkyblockArmor GOLEM_CHESTPLATE
 * @method static SkyblockArmor GOLEM_HELMET
 * @method static SkyblockArmor FARM_SUIT_BOOTS
 * @method static SkyblockArmor FARM_SUIT_LEGGINGS
 * @method static SkyblockArmor FARM_SUIT_CHESTPLATE
 * @method static SkyblockArmor FARM_SUIT_HELMET
 * @method static SkyblockArmor FARM_BOOTS
 * @method static SkyblockArmor FARM_LEGGINGS
 * @method static SkyblockArmor FARM_CHESTPLATE
 * @method static SkyblockArmor FARM_HELMET
 * @method static SkyblockArmor ANGLER_BOOTS
 * @method static SkyblockArmor ANGLER_LEGGINGS
 * @method static SkyblockArmor ANGLER_CHESTPLATE
 * @method static SkyblockArmor ANGLER_HELMET
 * @method static SkyblockArmor ARACHNE_BOOTS
 * @method static SkyblockArmor ARACHNE_LEGGINGS
 * @method static SkyblockArmor ARACHNE_CHESTPLATE
 * @method static SkyblockArmor ARACHNE_HELMET
 * @method static SkyblockArmor EMERALD_BOOTS
 * @method static SkyblockArmor EMERALD_LEGGINGS
 * @method static SkyblockArmor EMERALD_CHESTPLATE
 * @method static SkyblockArmor EMERALD_HELMET
 *
 * @method static PolishedPumpkin POLISHED_PUMPKIN()
 * @method static SoulString SOUL_STRING()
 * @method static EnchantmentBook ENCHANTMENT_BOOK()
 *
 * @method static SkyBlockWeapon CLEAVER_SWORD()
 * @method static SkyBlockWeapon GOLEM_SWORD()
 * @method static SkyBlockWeapon ZOMBIE_SWORD()
 * @method static SkyBlockWeapon ROGUE_SWORD()
 * @method static SkyBlockWeapon YETI_SWORD()
 * @method static SkyBlockWeapon PIGMAN_SWORD()
 * @method static SkyBlockWeapon SPIDER_SWORD()
 * @method static SkyBlockWeapon ASPECT_OF_THE_END_SWORD()
 * @method static SkyBlockWeapon INK_WAND()
 * @method static ExplosiveBow EXPLOSIVE_BOW()
 * @method static SkyBlockBow SAVANNA_BOW()
 * @method static SkyBlockBow ENDER_BOW()
 * @method static SkyBlockBow RUNAANS_BOW()
 * @method static SkyBlockBow HURRICANE_BOW()
 * @method static SkyBlockFishingRod FARMERS_ROD()
 * @method static RodOfLegends ROD_OF_LEGENDS()
 * @method static RodOfChampions ROD_OF_CHAMPIONS()
 * @method static SkyBlockShovel FLINT_SHOVEL()
 * @method static SkyBlockAxe TREECAPACITOR()
 * @method static SkyBlockAxe JUNGLE_AXE()
 * @method static SkyBlockPickaxe ZOMBIE_PICKAXE()
 * @method static SkyblockItem GRAPLING_HOOK()
 * @method static SkyblockItem ARACHNE_FRAGMENT()
 * @method static SkyblockItem HOT_POTATO_BOOK()
 * @method static SkyblockItem EMERALD_SWORD()
 *
 * @method static AccessoryItem EXPERIENCE_ARTIFACT()
 * @method static AccessoryItem FARMING_TALISMAN()
 * @method static AccessoryItem HEALING_RING()
 * @method static AccessoryItem HEALING_TALISMAN()
 * @method static AccessoryItem SPEED_ARTIFACT()
 * @method static AccessoryItem SPEED_RING()
 * @method static AccessoryItem SPEED_TALISMAN()
 * @method static AccessoryItem VACCINE_TALISMAN()
 * @method static AccessoryItem WOOD_AFFINITY_TALISMAN()
 * @method static AccessoryItem EMERALD_RING()
 *
 * @method static SkyblockItem SIMPLE_CARROT_CANDY()
 * @method static SkyblockItem GREAT_CARROT_CANDY()
 * @method static SkyblockItem SUPERB_CARROT_CANDY()
 * @method static SkyblockItem ULTIMATE_CARROT_CANDY()
 * @method static MaskItem MASK_ITEM()
 *
 *
 * @method static StorageSack HUSBANDRY_SACK()
 * @method static StorageSack COMBAT_SACK()
 * @method static StorageSack AGRONOMY_SACK()
 * @method static StorageSack MINING_SACK()
 * @method static StorageSack FORAGING_SACK()
 * @method static StorageSack FISHING_SACK()
 *
 * @method static SkyblockItem SKYBLOCK_MENU_ITEM()
 * @method static PersonalCompactor4000 PERSONAL_COMPACTOR_4000()
 *
 * @method static SkyblockItem COMPACTOR()
 * @method static SkyblockItem SUPER_COMPACTOR_3000()
 * @method static StorageChest STORAGE_CHEST()
 * @method static AutoSmelter  AUTO_SMELTER()
 * @method static SkyblockItem ENCHANTED_HOPPER()
 * @method static SkyblockItem ENCHANTED_LAVA_BUCKET()
 * @method static SkyblockItem BUDGET_HOPPER()
 * @method static SkyblockItem DIAMOND_SPREADING()
 * @method static SkyblockItem SUPER_ENCHANTED_EGG()
 *
 * @method static SkyblockItem HYPER_FURNACE()
 * @method static SkyblockItem ULTIMATE_CARROT_CANDY_UPGRADE()
 * @method static SkyblockItem ARACHNES_CALLING()
 * @method static PetItem PET_ITEM()
 * @method static MysteryPetItem MYSTERY_PET_ITEM()
 *
 *
 * @method static SkyBlockEnchantedItem ENCHANTED_ACACIA_WOOD
 * @method static SkyBlockEnchantedItem ENCHANTED_EGG
 * @method static SkyBlockEnchantedItem ENCHANTED_JUNGLE_WOOD
 * @method static SkyBlockEnchantedItem ENCHANTED_BIRCH_WOOD
 * @method static SkyBlockEnchantedItem ENCHANTED_OAK_WOOD
 * @method static SkyBlockEnchantedItem ENCHANTED_DARK_OAK_WOOD
 * @method static SkyBlockEnchantedItem ENCHANTED_SPRUCE_WOOD
 * @method static SkyBlockEnchantedItem ENCHANTED_BAKED_POTATO
 * @method static SkyBlockEnchantedItem ENCHANTED_BEEF
 * @method static SkyBlockEnchantedItem ENCHANTED_BLAZE_ROD
 * @method static SkyBlockEnchantedItem ENCHANTED_BONE
 * @method static SkyBlockEnchantedItem ENCHANTED_BREAD
 * @method static SkyBlockEnchantedItem ENCHANTED_CACTUS
 * @method static SkyBlockEnchantedItem ENCHANTED_CACTUS_GREEN
 * @method static SkyBlockEnchantedItem ENCHANTED_CARROT
 * @method static SkyBlockEnchantedItem ENCHANTED_CHICKEN
 * @method static SkyBlockEnchantedItem ENCHANTED_CLAY
 * @method static SkyBlockEnchantedItem ENCHANTED_COAL
 * @method static SkyBlockEnchantedItem ENCHANTED_COAL_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_COBBLESTONE
 * @method static SkyBlockEnchantedItem ENCHANTED_COOKED_FISH
 * @method static SkyBlockEnchantedItem ENCHANTED_COOKED_MUTTON
 * @method static SkyBlockEnchantedItem ENCHANTED_DIAMOND
 * @method static SkyBlockEnchantedItem ENCHANTED_DIAMOND_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_EMERALD
 * @method static SkyBlockEnchantedItem ENCHANTED_EMERALD_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_ENDER_PEARL
 * @method static SkyBlockEnchantedItem ENCHANTED_EYE_OF_ENDER
 * @method static SkyBlockEnchantedItem ENCHANTED_FEATHER
 * @method static SkyBlockEnchantedItem ENCHANTED_FERMENTED_SPIDER_EYE
 * @method static SkyBlockEnchantedItem ENCHANTED_FLINT
 * @method static SkyBlockEnchantedItem ENCHANTED_GLISTERING_MELON
 * @method static SkyBlockEnchantedItem ENCHANTED_GLOWSTONE
 * @method static SkyBlockEnchantedItem ENCHANTED_GLOWSTONE_DUST
 * @method static SkyBlockEnchantedItem ENCHANTED_GOLD
 * @method static SkyBlockEnchantedItem ENCHANTED_GOLD_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_GOLDEN_CARROT
 * @method static SkyBlockEnchantedItem ENCHANTED_GRILLED_PORK
 * @method static SkyBlockEnchantedItem ENCHANTED_GUNPOWDER
 * @method static SkyBlockEnchantedItem ENCHANTED_HAY_BALE
 * @method static SkyBlockEnchantedItem ENCHANTED_INK_SAC
 * @method static SkyBlockEnchantedItem ENCHANTED_IRON
 * @method static SkyBlockEnchantedItem ENCHANTED_IRON_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_LAPIS_LAZULI
 * @method static SkyBlockEnchantedItem ENCHANTED_LAPIS_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_LEATHER
 * @method static SkyBlockEnchantedItem ENCHANTED_LILY_PAD
 * @method static SkyBlockEnchantedItem ENCHANTED_MAGMA_CREAM
 * @method static SkyBlockEnchantedItem ENCHANTED_MELON_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_MELON
 * @method static SkyBlockEnchantedItem ENCHANTED_MUTTON
 * @method static SkyBlockEnchantedItem ENCHANTED_OBSIDIAN
 * @method static SkyBlockEnchantedItem ENCHANTED_PAPER
 * @method static SkyBlockEnchantedItem ENCHANTED_PORKCHOP
 * @method static SkyBlockEnchantedItem ENCHANTED_POTATO
 * @method static SkyBlockEnchantedItem ENCHANTED_PUFFERFISH
 * @method static SkyBlockEnchantedItem ENCHANTED_PUMPKIN
 * @method static SkyBlockEnchantedItem ENCHANTED_QUARTZ
 * @method static SkyBlockEnchantedItem ENCHANTED_RABBIT
 * @method static SkyBlockEnchantedItem ENCHANTED_RABBIT_FOOT
 * @method static SkyBlockEnchantedItem ENCHANTED_RABBIT_HIDE
 * @method static SkyBlockEnchantedItem ENCHANTED_RAW_FISH
 * @method static SkyBlockEnchantedItem ENCHANTED_SAND
 * @method static SkyBlockEnchantedItem ENCHANTED_RED_SAND
 * @method static SkyBlockEnchantedItem ENCHANTED_REDSTONE_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_REDSTONE
 * @method static SkyBlockEnchantedItem ENCHANTED_ROTTEN_FLESH
 * @method static SkyBlockEnchantedItem ENCHANTED_SLIMEBALL
 * @method static SkyBlockEnchantedItem ENCHANTED_SLIME_BLOCK
 * @method static SkyBlockEnchantedItem ENCHANTED_SPIDER_EYE
 * @method static SkyBlockEnchantedItem ENCHANTED_STRING
 * @method static SkyBlockEnchantedItem ENCHANTED_SUGAR
 * @method static SkyBlockEnchantedItem ENCHANTED_SUGARCANE
 */
final class SkyblockItems{
	use RegistryTrait;

	protected static function setup() : void{
		$factory = SkyblockItemFactory::getInstance();


		//self::register("cleaver_sword", CustomiesItemFactory::getInstance()->get("customies:custom_redsand"));

		self::register("ultimate_carrot_candy_upgrade", $factory->get(ItemIds::BOOK, 2));


		self::register("diamond_sword", $factory->get(ItemIds::DIAMOND_SWORD));
		self::register("diamond_pickaxe", $factory->get(ItemIds::DIAMOND_PICKAXE));

		self::register("diamond_helmet", $factory->get(ItemIds::DIAMOND_HELMET));
		self::register("diamond_chestplate", $factory->get(ItemIds::DIAMOND_CHESTPLATE));
		self::register("diamond_leggings", $factory->get(ItemIds::DIAMOND_LEGGINGS));
		self::register("diamond_boots", $factory->get(ItemIds::DIAMOND_BOOTS));

		self::register("arachnes_calling", $factory->get(ItemIds::CHEMICAL_HEAT, 2));

		//self::register("cleaver_sword", $factory->get(ItemIds::IRON_SWORD, 1));
		//self::register("spider_sword", $factory->get(ItemIds::IRON_SWORD, 2));
		//self::register("yeti_sword", $factory->get(ItemIds::IRON_SWORD, 3));
		//self::register("pigman_sword", $factory->get(ItemIds::IRON_SWORD, 4));
		//self::register("zombie_sword", $factory->get(ItemIds::IRON_SWORD, 5));
		//self::register("Golem_Sword", $factory->get(ItemIds::IRON_SWORD, 6));
		//self::register("rogue_sword", $factory->get(ItemIds::GOLD_SWORD, 1));

		self::register("explosive_bow", $factory->get(ItemIds::BOW, 2));
		self::register("runaans_bow", $factory->get(ItemIds::BOW, 5));
		self::register("hurricane_bow", $factory->get(ItemIds::BOW, 4));
		self::register("ender_bow", $factory->get(ItemIds::BOW, 3));
		self::register("rod_of_champions", $factory->get(ItemIds::FISHING_ROD, 1));
		self::register("rod_of_legends", $factory->get(ItemIds::FISHING_ROD, 2));
		self::register("farmers_rod", $factory->get(ItemIds::FISHING_ROD, 3));
		self::register("flint_shovel", $factory->get(ItemIds::IRON_SHOVEL, 1));
		self::register("Savanna_Bow", $factory->get(ItemIds::BOW, 1));
		self::register("Ink_Wand", $factory->get(ItemIds::STICK, 1));

		self::register("Treecapacitor", $factory->get(ItemIds::GOLDEN_AXE, 1));
		self::register("Jungle_Axe", $factory->get(ItemIds::WOODEN_AXE, 1));
		self::register("Zombie_Pickaxe", $factory->get(ItemIds::IRON_PICKAXE, 1));

		self::register("Husbandry_Sack", $factory->get(ItemIds::BARREL, 1));
		self::register("Fishing_Sack", $factory->get(ItemIds::BARREL, 2));
		self::register("Agronomy_Sack", $factory->get(ItemIds::BARREL, 3));
		self::register("Foraging_Sack", $factory->get(ItemIds::BARREL, 4));
		self::register("Mining_Sack", $factory->get(ItemIds::BARREL, 5));
		self::register("Combat_Sack", $factory->get(ItemIds::BARREL, 6));

		self::register("enchantment_book", $factory->get(ItemIds::WRITABLE_BOOK, 2));
		self::register("mask_item", $factory->get(ItemIds::NETHER_STAR, 2));




		self::register("polished_pumpkin", $factory->get(ItemIds::PUMPKIN, 1));
		self::register("soul_string", $factory->get(ItemIds::STRING, 2));
		self::register("skyblock_menu_item", $factory->get(ItemIds::NETHER_STAR, 1));
		self::register("personal_compactor_4000", $factory->get(ItemIds::DROPPER, 1));

		self::register("super_compactor_3000", $factory->get(ItemIds::DROPPER, 4));
		self::register("storage_chest", $factory->get(ItemIds::CHEST, 3));
		self::register("compactor", $factory->get(ItemIds::DROPPER, 2));
		self::register("auto_smelter", $factory->get(ItemIds::FURNACE, 1));

		self::register("hyper_furnace", $factory->get(ItemIds::FURNACE, 2));


		self::register("enchanted_sand", $factory->get(939, 1));
		self::register("enchanted_red_sand", $factory->get(940, 1));
		self::register("enchanted_ink_sac", $factory->get(941, 1));
		self::register("enchanted_cactus_green", $factory->get(942, 1));
		self::register("enchanted_lapis_lazuli", $factory->get(938, 1));


		self::register("Enchanted_Acacia_Wood", $factory->get(ItemIds::STRIPPED_ACACIA_LOG, 1));
		self::register("Enchanted_jungle_Wood", $factory->get(ItemIds::STRIPPED_JUNGLE_LOG, 1));
		self::register("Enchanted_Birch_Wood", $factory->get(ItemIds::STRIPPED_BIRCH_LOG, 1));
		self::register("Enchanted_Oak_Wood", $factory->get(ItemIds::STRIPPED_OAK_LOG, 1));
		self::register("Enchanted_Dark_Oak_Wood", $factory->get(ItemIds::STRIPPED_DARK_OAK_LOG, 1));
		self::register("Enchanted_Spruce_Wood", $factory->get(ItemIds::STRIPPED_SPRUCE_LOG, 1));
		self::register("Enchanted_Baked_Potato", $factory->get(ItemIds::BAKED_POTATO, 1));
		self::register("Enchanted_Beef", $factory->get(ItemIds::BEEF, 1));
		self::register("Enchanted_Blaze_Rod", $factory->get(ItemIds::BLAZE_ROD, 1));
		self::register("Enchanted_Bone", $factory->get(ItemIds::BONE, 1));
		self::register("Enchanted_Bread", $factory->get(ItemIds::BREAD, 1));
		self::register("Enchanted_Cactus", $factory->get(ItemIds::CACTUS, 1));
		self::register("Enchanted_Carrot", $factory->get(ItemIds::CARROT, 1));
		self::register("Enchanted_Chicken", $factory->get(ItemIds::CHICKEN, 1));
		self::register("Enchanted_Clay", $factory->get(ItemIds::CLAY, 1));
		self::register("Enchanted_Coal", $factory->get(ItemIds::COAL, 1));
		self::register("Enchanted_Coal_Block", $factory->get(ItemIds::COAL_BLOCK, 1));
		self::register("Enchanted_Cobblestone", $factory->get(ItemIds::COBBLESTONE, 1));
		self::register("Enchanted_Cooked_Fish", $factory->get(ItemIds::COOKED_FISH, 1));
		self::register("Enchanted_Cooked_Mutton", $factory->get(ItemIds::COOKED_MUTTON, 1));
		self::register("Enchanted_Diamond", $factory->get(ItemIds::DIAMOND, 1));
		self::register("Enchanted_Diamond_Block", $factory->get(ItemIds::DIAMOND_BLOCK, 1));
		self::register("Enchanted_Emerald", $factory->get(ItemIds::EMERALD, 1));
		self::register("Enchanted_Emerald_Block", $factory->get(ItemIds::EMERALD_BLOCK, 1));
		self::register("Enchanted_Ender_Pearl", $factory->get(ItemIds::ENDER_PEARL, 1));
		self::register("Enchanted_Eye_Of_Ender", $factory->get(ItemIds::ENDER_EYE, 1));
		self::register("Enchanted_Feather", $factory->get(ItemIds::FEATHER, 1));
		self::register("Enchanted_Fermented_Spider_Eye", $factory->get(ItemIds::FERMENTED_SPIDER_EYE, 1));
		self::register("Enchanted_Flint", $factory->get(ItemIds::FLINT, 1));
		self::register("Enchanted_Glistering_Melon", $factory->get(ItemIds::GLISTERING_MELON, 1));
		self::register("Enchanted_Glowstone", $factory->get(ItemIds::GLOWSTONE, 1));
		self::register("Enchanted_Glowstone_Dust", $factory->get(ItemIds::GLOWSTONE_DUST, 1));
		self::register("Enchanted_Gold", $factory->get(ItemIds::GOLD_INGOT, 1));
		self::register("Enchanted_Gold_Block", $factory->get(ItemIds::GOLD_BLOCK, 1));
		self::register("Enchanted_Golden_Carrot", $factory->get(ItemIds::GOLDEN_CARROT, 1));
		self::register("Enchanted_Grilled_Pork", $factory->get(ItemIds::COOKED_PORKCHOP, 1));
		self::register("Enchanted_Gunpowder", $factory->get(ItemIds::GUNPOWDER, 1));
		self::register("Enchanted_Hay_Bale", $factory->get(ItemIds::HAY_BALE, 1));
		self::register("Enchanted_Iron", $factory->get(ItemIds::IRON_INGOT, 1));
		self::register("Enchanted_Iron_Block", $factory->get(ItemIds::IRON_BLOCK, 1));
		self::register("Enchanted_Lapis_Block", $factory->get(ItemIds::LAPIS_BLOCK, 1));
		self::register("Enchanted_Leather", $factory->get(ItemIds::LEATHER, 1));
		self::register("Enchanted_Lily_Pad", $factory->get(ItemIds::LILY_PAD, 1));
		self::register("Enchanted_Magma_Cream", $factory->get(ItemIds::MAGMA_CREAM, 1));
		self::register("Enchanted_Melon_Block", $factory->get(ItemIds::MELON_BLOCK, 1));
		self::register("Enchanted_Melon", $factory->get(ItemIds::MELON, 1));
		self::register("Enchanted_Mutton", $factory->get(ItemIds::MUTTON, 1));
		self::register("Enchanted_Obsidian", $factory->get(ItemIds::OBSIDIAN, 1));
		self::register("Enchanted_Paper", $factory->get(ItemIds::PAPER, 1));
		self::register("Enchanted_Porkchop", $factory->get(ItemIds::PORKCHOP, 1));
		self::register("Enchanted_Potato", $factory->get(ItemIds::POTATO, 1));
		self::register("Enchanted_Pufferfish", $factory->get(ItemIds::PUFFERFISH, 1));
		self::register("Enchanted_Pumpkin", $factory->get(ItemIds::PUMPKIN, 1));
		self::register("Enchanted_Quartz", $factory->get(ItemIds::NETHER_QUARTZ, 1));
		self::register("Enchanted_Rabbit", $factory->get(ItemIds::RABBIT, 1));
		self::register("Enchanted_Rabbit_Foot", $factory->get(ItemIds::RABBIT_FOOT, 1));
		self::register("Enchanted_Rabbit_Hide", $factory->get(ItemIds::RABBIT_HIDE, 1));
		self::register("Enchanted_Raw_Fish", $factory->get(ItemIds::FISH, 1));
		self::register("Enchanted_Redstone_Block", $factory->get(ItemIds::REDSTONE_BLOCK, 1));
		self::register("Enchanted_Redstone", $factory->get(ItemIds::REDSTONE, 1));
		self::register("Enchanted_Rotten_flesh", $factory->get(ItemIds::ROTTEN_FLESH, 1));
		self::register("Enchanted_Slimeball", $factory->get(ItemIds::SLIMEBALL, 1));
		self::register("Enchanted_Slime_Block", $factory->get(ItemIds::SLIME, 1));
		self::register("Enchanted_Spider_Eye", $factory->get(ItemIds::SPIDER_EYE, 1));
		self::register("Enchanted_String", $factory->get(ItemIds::STRING, 1));
		self::register("Enchanted_Sugar", $factory->get(ItemIds::SUGAR, 1));
		self::register("Enchanted_Sugarcane", $factory->get(ItemIds::REEDS, 1));
		self::register("Enchanted_egg", $factory->get(ItemIds::EGG, 1));

	}

	public static function registerPublic(string $name, Item $item): void {
		self::register($name, $item);
	}

	protected static function register(string $name, Item $item) : void{
		self::_registryRegister($name, $item);
	}

	public static function __callStatic($name, $arguments){
		if(count($arguments) > 0){
			throw new \ArgumentCountError("Expected exactly 0 arguments, " . count($arguments) . " passed");
		}
		try{
			return clone self::_registryFromString($name);
		}catch(\InvalidArgumentException $e){
			throw new \Error($e->getMessage(), 0, $e);
		}
	}
}