<?php

declare(strict_types=1);

namespace skyblock\utils;

use pocketmine\item\Armor;
use pocketmine\item\Axe;
use pocketmine\item\Bow;
use pocketmine\item\Durable;
use pocketmine\item\FishingRod;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use skyblock\customenchants\heroic\ExtremeOverload;
use skyblock\customenchants\legendary\Overload;
use skyblock\items\customenchants\BaseCustomEnchant;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\itemmods\types\CrystalAmuletItemMod;
use skyblock\items\itemmods\types\MutatedCrystalAmuletItemMod;
use skyblock\items\masks\types\AstronautMask;
use skyblock\items\sets\types\KrakenSpecialSet;

class CustomEnchantUtils {

	public static function getMaxHealth(Player $player): int {
		$max = 20;

		//TODO: addapt this to pve
		return $max;
	}

	public static function isHelmet(Item $item): bool
	{
		return in_array($item->getId(), [ItemIds::LEATHER_CAP, ItemIds::CHAIN_HELMET, ItemIds::IRON_HELMET, ItemIds::GOLD_HELMET, ItemIds::DIAMOND_HELMET]);
	}

	public static function isChestplate(Item $item): bool
	{
		return in_array($item->getId(), [ItemIds::LEATHER_CHESTPLATE, ItemIds::CHAIN_CHESTPLATE, ItemIds::IRON_CHESTPLATE, ItemIds::GOLD_CHESTPLATE, ItemIds::DIAMOND_CHESTPLATE]);
	}

	public static function isLeggings(Item $item): bool
	{
		return in_array($item->getId(), [ItemIds::LEATHER_PANTS, ItemIds::CHAIN_LEGGINGS, ItemIds::IRON_LEGGINGS, ItemIds::GOLD_LEGGINGS, ItemIds::DIAMOND_LEGGINGS]);
	}

	public static function isBoots(Item $item): bool
	{
		return in_array($item->getId(), [ItemIds::LEATHER_BOOTS, ItemIds::CHAIN_BOOTS, ItemIds::IRON_BOOTS, ItemIds::GOLD_BOOTS, ItemIds::DIAMOND_BOOTS]);
	}

	public static function isArmor(Item $item): bool {
		return self::isBoots($item) || self::isLeggings($item) || self::isChestplate($item) || self::isHelmet($item);
	}

	public static function getItemType(Item $item): int {
		switch ($item){
			case self::isLeggings($item):
				return BaseCustomEnchant::ITEM_LEGGINGS;
			case self::isChestplate($item):
				return BaseCustomEnchant::ITEM_CHESTPLATE;
			case self::isBoots($item):
				return BaseCustomEnchant::ITEM_BOOTS;
			case self::isHelmet($item):
				return BaseCustomEnchant::ITEM_HELMET;
			case $item instanceof Sword:
				return BaseCustomEnchant::ITEM_SWORD;
			case $item instanceof Axe:
				return BaseCustomEnchant::ITEM_AXE;
			case $item instanceof Pickaxe:
				return BaseCustomEnchant::ITEM_PICKAXE;
			case $item instanceof FishingRod:
				return BaseCustomEnchant::ITEM_FISHING_ROD;
		}

		return -38;
	}

	public static function itemMatchesItemType(Item $item, int $itemType): bool
	{
		if ($item->getId() === ItemIds::ENCHANTED_BOOK || $item->getId() === ItemIds::ENCHANTED_BOOK) return true;

		switch ($itemType) {
			case BaseCustomEnchant::ITEM_ALL:
				return true;
			case BaseCustomEnchant::ITEM_DURABLE:
				return $item instanceof Durable;
			case BaseCustomEnchant::ITEM_WEAPONS:
				return $item instanceof Sword || $item instanceof Axe || $item instanceof Bow;
			case BaseCustomEnchant::ITEM_SWORD:
				return $item instanceof Sword;
			case BaseCustomEnchant::ITEM_BOW:
				return $item instanceof Bow;
			case BaseCustomEnchant::ITEM_TOOLS:
				return $item instanceof Pickaxe || $item instanceof Axe || $item instanceof Shovel || $item instanceof Hoe || $item instanceof Shears;
			case BaseCustomEnchant::ITEM_PICKAXE:
				return $item instanceof Pickaxe;
			case BaseCustomEnchant::ITEM_AXE:
				return $item instanceof Axe;
			case BaseCustomEnchant::ITEM_ARMOUR:
				return $item instanceof Armor || $item->getId() === ItemIds::ELYTRA;
			case BaseCustomEnchant::ITEM_HELMET:
				return self::isHelmet($item);
			case BaseCustomEnchant::ITEM_BOOTS:
				return self::isBoots($item);
			case BaseCustomEnchant::ITEM_CHESTPLATE:
			case BaseCustomEnchant::ITEM_AMULET:
			case BaseCustomEnchant::ITEM_BACKPACK:
				return self::isChestplate($item);
			case BaseCustomEnchant::ITEM_LEGGINGS:
			case BaseCustomEnchant::ITEM_BELT:
				return self::isLeggings($item);
			case BaseCustomEnchant::ITEM_FARMING:
				return $item instanceof Axe || $item instanceof Hoe;
			case BaseCustomEnchant::ITEM_HOE:
				return $item instanceof Hoe;
			case BaseCustomEnchant::ITEM_FISHING_ROD:
				return $item instanceof FishingRod;
			case BaseCustomEnchant::ITEM_BOOTS_AND_HELMET:
				return self::isHelmet($item) || self::isBoots($item);

		}


		return false;
	}

	public static function sortEnchantmentsByLevel(array $enchantments): array
	{
		usort($enchantments, function (CustomEnchantInstance $enchantmentInstance, CustomEnchantInstance $enchantmentInstanceB) {
			return $enchantmentInstanceB->getLevel() - $enchantmentInstance->getLevel();
		});
		return $enchantments;
	}

	public static function itemTypeIntToString(int $type): string {
		switch($type) {
			case BaseCustomEnchant::ITEM_BELT:
				return "Belt";
			case BaseCustomEnchant::ITEM_BACKPACK:
				return "Backpack";
			case BaseCustomEnchant::ITEM_AMULET:
				return "Amulet";
			case BaseCustomEnchant::ITEM_FISHING_ROD:
				return "Fishing Rod";
			case BaseCustomEnchant::ITEM_HELMET:
				return "Helmet";
			case BaseCustomEnchant::ITEM_CHESTPLATE:
				return "Chestplate";
			case BaseCustomEnchant::ITEM_LEGGINGS:
				return "Leggings";
			case BaseCustomEnchant::ITEM_BOOTS:
				return "Boots";
			case BaseCustomEnchant::ITEM_SWORD:
				return "Sword";
			case BaseCustomEnchant::ITEM_WEAPONS:
				return "Weapons";
			case BaseCustomEnchant::ITEM_AXE:
				return "Axe";
			case BaseCustomEnchant::ITEM_PICKAXE:
				return "Pickaxe";
			case BaseCustomEnchant::ITEM_TOOLS:
				return "Tools";
			case BaseCustomEnchant::ITEM_ARMOUR:
				return "Armour";
			case BaseCustomEnchant::ITEM_BOW:
				return "Bow";
			case BaseCustomEnchant::ITEM_DURABLE:
				return "Durable";
			case BaseCustomEnchant::ITEM_ALL:
				return "All";
			case BaseCustomEnchant::ITEM_FARMING:
				return "Axe & Hoe";
			case BaseCustomEnchant::ITEM_BOOTS_AND_HELMET:
				return "Boots & Helmet";
			case BaseCustomEnchant::ITEM_HOE:
				return "Hoe";
		}
		return "";
	}

	public static function canBeHolied(Item $item): bool {
		return
			self::isHelmet($item) ||
			self::isBoots($item) ||
			self::isChestplate($item) ||
			self::isLeggings($item) ||
			$item instanceof Tool ||
			$item instanceof Sword ||
			$item instanceof Axe ||
			$item instanceof FishingRod;
	}

	/**
	 * @param int $num
	 * @return string|null
	 */
	public static function roman(int $num): ?string
	{
		$n = intval($num);
		$res = '';

		//array of roman numbers
		$romanNumber_Array = array(
			'M' => 1000,
			'CM' => 900,
			'D' => 500,
			'CD' => 400,
			'C' => 100,
			'XC' => 90,
			'L' => 50,
			'XL' => 40,
			'X' => 10,
			'IX' => 9,
			'V' => 5,
			'IV' => 4,
			'I' => 1);

		foreach ($romanNumber_Array as $roman => $number) {
			//divide to get  matches
			$matches = intval($n / $number);

			//assign the roman char * $matches
			$res .= str_repeat($roman, $matches);

			//substract from the number
			$n = $n % $number;
		}
		// return the result
		return $res;
	}
}