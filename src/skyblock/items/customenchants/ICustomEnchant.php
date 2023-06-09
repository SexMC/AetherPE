<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

interface ICustomEnchant {

	const RARITY_UNCOMMON = 1;
	const RARITY_ELITE = 4;
	const RARITY_RARE = 7;
	const RARITY_LEGENDARY = 10;
	const RARITY_SPECIAL = 12;
	const RARITY_MASTERY = 16;

	const ITEM_TOOLS = 0;
	const ITEM_ARMOUR = 1;
	const ITEM_WEAPONS = 2;
	const ITEM_ALL = 3;
	const ITEM_SWORD = 4;
	const ITEM_PICKAXE = 5;
	const ITEM_AXE = 6;
	const ITEM_HELMET = 7;
	const ITEM_CHESTPLATE = 8;
	const ITEM_LEGGINGS = 9;
	const ITEM_BOOTS = 10;
	const ITEM_DURABLE = 11;
	const ITEM_BOW = 12;
	const ITEM_FARMING = 13; //hoe or axe
	const ITEM_HOE = 14;
	const ITEM_FISHING_ROD = 15;
	const ITEM_BELT = 16;
	const ITEM_AMULET = 17;
	const ITEM_BACKPACK = 18;
	const ITEM_BOOTS_AND_HELMET = 19;
	const ITEM_SHEAR = 20;
}