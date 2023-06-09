<?php

namespace skyblock\misc\quests;

interface IQuest{

	public const NORMAL_PREFIX = "§7[§a§lQuests§r§7] ";
	public const ISLAND_PREFIX = "§7[§a§lIsland Quests§r§7] ";
	public const DAILY_PREFIX = "§7[§a§lDaily Quests§r§7] ";

	public const QUEST_TYPE_NORMAL = 1;
	public const QUEST_TYPE_DAILY = 2;
	public const QUEST_TYPE_ISLAND = 3;

	public const DONE = "done";

	public const KILL_ENTITY = 1;
	public const MINE_BLOCK = 2;
	public const USE_CRATE = 3;
	public const PLACE_BLOCK = 4;
	public const JUMP = 5;
	public const KIT_CLAIM = 6;
	public const CHAT = 7;
	public const ISLAND_CREATE_JOIN = 8;
	public const ISLAND_BOSS = 9;
	public const COMMAND = 10;
	public const SELL = 11;
	public const CRAFT = 12;
	public const EAT = 13;
	public const COINFLIP_WIN = 14;
	public const FISH = 15;
	public const ENCHANT = 16;
	public const ALCHEMIST = 17;
	public const PAY = 18;
	public const MINION_PLACE = 19;
	public const PAY_ESSENCE = 20;
	public const SNEAK = 21;
	public const ENVOY_CLAIM = 22;
	public const XP_SPEND = 23;
	public const BAH = 24;
	public const VOTE = 25;
	public const OPEN_LOOTBOX = 26;
	public const BANK_DEPOSIT = 27;
}