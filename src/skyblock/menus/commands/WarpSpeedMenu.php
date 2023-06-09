<?php

declare(strict_types=1);

namespace skyblock\menus\commands;


use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use skyblock\Database;
use skyblock\menus\AetherMenu;

class WarpSpeedMenu extends AetherMenu {
	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setName("Warp Speed");

		$age = (int) (time() -  (Database::getInstance()->getRedis()->get("server.age") ?? 0));
		$days = (int) floor($age / 86400);

		$menu->getInventory()->addItem($this->getInfoItem());
		$menu->getInventory()->addItem($this->getWelcoming($age, $days));
		$menu->getInventory()->addItem($this->getDayOne($age, $days));
		$menu->getInventory()->addItem($this->getDayTwo($age, $days));
		$menu->getInventory()->addItem($this->getDayThree($age, $days));
		$menu->getInventory()->addItem($this->getDayFour($age, $days));
		$menu->getInventory()->addItem($this->getDayFive($age, $days));
		$menu->getInventory()->addItem($this->getDaySix($age, $days));
		$menu->getInventory()->addItem($this->getDaySeven($age, $days));

		return $menu;
	}

	public function getInfoItem(): Item {
		$item = ItemFactory::getInstance()->get(386);
		$item->setCustomName("§r§d§l*§5*§d* §r§l§dWA§5RP §dSP§5EED §r§d§l*§5*§d*");
		$item->setLore([
			"§r§7Warp Speed is an Event that occurs",
			"§r§7during a new season of a planet. Each",
			"§r§7day, A feature gets enabled allowing players",
			"§r§7to be able to use that certain feature.",
			"§r",
			"§r§7Once 7 days has passed, /fund begins.",
			"§r§7It's no longer days, but global economy",
			"§r§7mixed together in-order to unlock a feature.",
			"§r",
			"§r§7Still don't get it? You can join",
			"§r§7the Discord Wiki for more help.",
			"§r§7https://discord.gg/aetherpe",
		]);

		return $item;
	}

	public function getWelcoming(int $age, int $days): Item {
		$item = ItemFactory::getInstance()->get(171, 5);
		$item->setCustomName("§r§a§lWelcoming Day [§r§7100%§l§a]");
		$item->setLore([
			"§r§a§l✓ §r§a0 ➜ 20 Level Cap",
			"§r§a§l✓ §r§a0 ➜ 3 Custom Enchantment Limit",
			"§r§7(Not including Pickaxes)",
			"§r§c§l✗ §r§c/Coinflips §r§7(/cf)",
			"§r§c§l✗ §r§cRank Lootboxes §r§7(/kit lootbox)",
			"§r§c§l✗ §r§cMasks §r§7(Able to be used)",
			"§r§c§l✗ §r§cAmulet, Backpack & Belt Sockets §r§7(Able to be used)",
			"§r§c§l✗ §r§cMinions §r§7(Able to be used)",
			"§r§c§l✗ §r§cHeroic Upgrades §r§7(Able to be used)",
			"§r§c§l✗ §r§cSpecial Sets & Weapons §r§7(Able to be used)",
			"§r§c§l✗ §r§cMaterial Planet §r§7(Able to warp to)",
			"§r§c§l✗ §r§cKing of the Hill Event §r§7(Able to warp to)",
			"§r§c§l✗ §r§c/skit §r§7(Able to be used)",
			"§r§c§l✗ §r§cHeroic Enchantments §r§7(Able to be used)",
			"§r§c§l✗ §r§cHoly Essence §r§7(Able to be used)",
			"§r§4§l✗ §r§c/fund",
			"§r§7§oIf a feature is not in the list of features",
			"§r§7§oabove, it's going to be in /fund instead.",
			"§r",
			"§r§a:::::::::::::::::::::::::::::::::::::::: §r§7(100%)",
			"§r§7($days / 0 Days Passed)",
		]);

		return $item;
	}

	public function getDayOne(int $age, int $days): Item {
		$percent = min(100, (int) ceil($age / (86400) * 100));

		$item = ItemFactory::getInstance()->get(171, ($days >= 1 ? 5 : 14));
		$item->setCustomName("§r§c§lDay 1 [§r§7{$percent}%§l§c]");
		$item->setLore([
			"§r§a§l✓ §r§a20 Level Cap",
			"§r§a§l✓ §r§a3 Custom Enchantment Limit",
			"§r§7(Not including Pickaxes)",
			"§r§a§l✓ §r§a/Coinflips §r§7(/cf)",
			"§r§c§l✗ §r§cRank Lootboxes §r§7(/kit lootbox)",
			"§r§c§l✗ §r§cMasks §r§7(Able to be used)",
			"§r§c§l✗ §r§cAmulet, Backpack & Belt Sockets §r§7(Able to be used)",
			"§r§c§l✗ §r§cMinions §r§7(Able to be used)",
			"§r§c§l✗ §r§cHeroic Upgrades §r§7(Able to be used)",
			"§r§c§l✗ §r§cSpecial Sets & Weapons §r§7(Able to be used)",
			"§r§c§l✗ §r§cMaterial Planet §r§7(Able to warp to)",
			"§r§c§l✗ §r§cKing of the Hill Event §r§7(Able to warp to)",
			"§r§c§l✗ §r§c/skit §r§7(Able to be used)",
			"§r§c§l✗ §r§cHeroic Enchantments §r§7(Able to be used)",
			"§r§c§l✗ §r§cHoly Essence §r§7(Able to be used)",
			"§r§c§l✗ §r§cIsland Quests §r§7(Able to be used)",
			"§r§4§l✗ §r§c/fund",
			"§r§7§oIf a feature is not in the list of features",
			"§r§7§oabove, it's going to be in /fund instead.",
			"§r",
			"§r§7($days / 1 Days Passed)",
		]);

		return $item;
	}

	public function getDayTwo(int $age, int $days): Item {
		$percent = min(100, (int) ceil($age / (86400 * 2) * 100));

		$item = ItemFactory::getInstance()->get(171, ($days >= 2 ? 5 : 14));
		$item->setCustomName("§r§c§lDay 2 [§r§7{$percent}%§l§c]");
		$item->setLore([
			"§r§a§l✓ §r§a20 ➜ 30 Level Cap",
			"§r§a§l✓ §r§a3 ➜ 4 Custom Enchantment Limit",
			"§r§7(Not including Pickaxes)",
			"§r§a§l✓ §r§a/Coinflips §r§7(/cf)",
			"§r§a§l✓ §r§aMasks §r§7(Able to be used)",
			"§r§c§l✗ §r§cRank Lootboxes §r§7(/kit lootbox)",
			"§r§c§l✗ §r§cAmulet, Backpack & Belt Sockets §r§7(Able to be used)",
			"§r§c§l✗ §r§cMinions §r§7(Able to be used)",
			"§r§c§l✗ §r§cHeroic Upgrades §r§7(Able to be used)",
			"§r§c§l✗ §r§cSpecial Sets & Weapons §r§7(Able to be used)",
			"§r§c§l✗ §r§cMaterial Planet §r§7(Able to warp to)",
			"§r§c§l✗ §r§cKing of the Hill Event §r§7(Able to warp to)",
			"§r§c§l✗ §r§c/skit §r§7(Able to be used)",
			"§r§c§l✗ §r§cHeroic Enchantments §r§7(Able to be used)",
			"§r§c§l✗ §r§cHoly Essence §r§7(Able to be used)",
			"§r§c§l✗ §r§cIsland Quests §r§7(Able to be used)",
			"§r§4§l✗ §r§c/fund",
			"§r§7§oIf a feature is not in the list of features",
			"§r§7§oabove, it's going to be in /fund instead.",
			"§r",
			"§r§7($days / 2 Days Passed)",
		]);

		return $item;
	}

	public function getDayThree(int $age, int $days): Item {
		$percent = min(100, (int) ceil(($age / (86400 * 3)) * 100));

		$item = ItemFactory::getInstance()->get(171, ($days >= 3 ? 5 : 14));
		$item->setCustomName("§r§c§lDay 3 [§r§7{$percent}%§l§c]");
		$item->setLore([
			"§r§a§l✓ §r§a30 Level Cap",
			"§r§a§l✓ §r§a4 ➜ 5 Custom Enchantment Limit",
			"§r§7(Not including Pickaxes)",
			"§r§a§l✓ §r§a/Coinflips §r§7(/cf)",
			"§r§a§l✓ §r§aMasks §r§7(Able to be used)",
			"§r§a§l✓ §r§a/skit §r§7(Able to be used)",
			"§r§c§l✗ §r§cRank Lootboxes §r§7(/kit lootbox)",
			"§r§c§l✗ §r§cAmulet, Backpack & Belt Sockets §r§7(Able to be used)",
			"§r§c§l✗ §r§cMinions §r§7(Able to be used)",
			"§r§c§l✗ §r§cHeroic Upgrades §r§7(Able to be used)",
			"§r§c§l✗ §r§cSpecial Sets & Weapons §r§7(Able to be used)",
			"§r§c§l✗ §r§cMaterial Planet §r§7(Able to warp to)",

			"§r§c§l✗ §r§cKing of the Hill Event §r§7(Able to warp to)",
			"§r§c§l✗ §r§cHeroic Enchantments §r§7(Able to be used)",
			"§r§c§l✗ §r§cHoly Essence §r§7(Able to be used)",
			"§r§c§l✗ §r§cIsland Quests §r§7(Able to be used)",
			"§r§4§l✗ §r§c/fund",
			"§r§7§oIf a feature is not in the list of features",
			"§r§7§oabove, it's going to be in /fund instead.",
			"§r",
			"§r§7($days / 3 Days Passed)",
		]);

		return $item;
	}

	public function getDayFour(int $age, int $days): Item {
		$percent = min(100, (int) ceil($age / (86400 * 4) * 100));

		$item = ItemFactory::getInstance()->get(171, ($days >= 4 ? 5 : 14));
		$item->setCustomName("§r§c§lDay 4 [§r§7{$percent}%§l§c]");
		$item->setLore([
			"§r§a§l✓ §r§a30 ➜ 40 Level Cap",
			"§r§a§l✓ §r§a5 ➜ 6 Custom Enchantment Limit",
			"§r§7(Not including Pickaxes)",
			"§r§a§l✓ §r§a/Coinflips §r§7(/cf)",
			"§r§a§l✓ §r§aMasks §r§7(Able to be used)",
			"§r§a§l✓ §r§a/skit §r§7(Able to be used)",
			"§r§a§l✓ §r§aMaterial Planet §r§7(Able to warp to)",
			"§r§a§l✓ §r§aMinions §r§7(Able to be used)",
			"§r§c§l✗ §r§cRank Lootboxes §r§7(/kit lootbox)",
			"§r§c§l✗ §r§cAmulet, Backpack & Belt Sockets §r§7(Able to be used)",
			"§r§c§l✗ §r§cHeroic Upgrades §r§7(Able to be used)",
			"§r§c§l✗ §r§cSpecial Sets & Weapons §r§7(Able to be used)",

			"§r§c§l✗ §r§cKing of the Hill Event §r§7(Able to warp to)",
			"§r§c§l✗ §r§cHeroic Enchantments §r§7(Able to be used)",
			"§r§c§l✗ §r§cHoly Essence §r§7(Able to be used)",
			"§r§c§l✗ §r§cIsland Quests §r§7(Able to be used)",
			"§r§4§l✗ §r§c/fund",
			"§r§7§oIf a feature is not in the list of features",
			"§r§7§oabove, it's going to be in /fund instead.",
			"§r",
			"§r§7($days / 4 Days Passed)",
		]);

		return $item;
	}

	public function getDayFive(int $age, int $days): Item {
		$percent = min(100, (int) ceil($age / (86400 * 5) * 100));

		$item = ItemFactory::getInstance()->get(171, ($days >= 5 ? 5 : 14));
		$item->setCustomName("§r§c§lDay 5 [§r§7{$percent}%§l§c]");
		$item->setLore([
			"§r§a§l✓ §r§a40 Level Cap",
			"§r§a§l✓ §r§a6 Custom Enchantment Limit",
			"§r§7(Not including Pickaxes)",
			"§r§a§l✓ §r§a/Coinflips §r§7(/cf)",
			"§r§a§l✓ §r§aMasks §r§7(Able to be used)",
			"§r§a§l✓ §r§a/skit §r§7(Able to be used)",
			"§r§a§l✓ §r§aMaterial Planet §r§7(Able to warp to)",
			"§r§a§l✓ §r§aMinions §r§7(Able to be used)",
			"§r§a§l✓ §r§aRank Lootboxes §r§7(/kit lootbox)",
			"§r§a§l✓ §r§aHeroic Upgrades §r§7(Able to be used)",
			"§r§c§l✗ §r§cAmulet, Backpack & Belt Sockets §r§7(Able to be used)",
			"§r§c§l✗ §r§cSpecial Sets & Weapons §r§7(Able to be used)",

			"§r§c§l✗ §r§cKing of the Hill Event §r§7(Able to warp to)",
			"§r§c§l✗ §r§cHeroic Enchantments §r§7(Able to be used)",
			"§r§c§l✗ §r§cHoly Essence §r§7(Able to be used)",
			"§r§c§l✗ §r§cIsland Quests §r§7(Able to be used)",
			"§r§4§l✗ §r§c/fund",
			"§r§7§oIf a feature is not in the list of features",
			"§r§7§oabove, it's going to be in /fund instead.",
			"§r",
			"§r§7($days / 5 Days Passed)",
		]);

		return $item;
	}

	public function getDaySix(int $age, int $days): Item {
		$percent = min(100, (int) ceil($age / (86400 * 6) * 100));

		$item = ItemFactory::getInstance()->get(171, ($days >= 6 ? 5 : 14));
		$item->setCustomName("§r§c§lDay 6 [§r§7{$percent}%§l§c]");
		$item->setLore([
			"§r§a§l✓ §r§a40 ➜ 50 Level Cap",
			"§r§a§l✓ §r§a6 ➜ 7 Custom Enchantment Limit",
			"§r§7(Not including Pickaxes)",
			"§r§a§l✓ §r§a/Coinflips §r§7(/cf)",
			"§r§a§l✓ §r§aMasks §r§7(Able to be used)",
			"§r§a§l✓ §r§a/skit §r§7(Able to be used)",
			"§r§a§l✓ §r§aMaterial Planet §r§7(Able to warp to)",
			"§r§a§l✓ §r§aMinions §r§7(Able to be used)",
			"§r§a§l✓ §r§aRank Lootboxes §r§7(/kit lootbox)",
			"§r§a§l✓ §r§aHeroic Upgrades §r§7(Able to be used)",
			"§r§a§l✓ §r§aAmulet, Backpack & Belt Sockets §r§7(Able to be used)",
			"§r§a§l✓ §r§aHoly Essence §r§7(Able to be used)",

			"§r§c§l✗ §r§cSpecial Sets & Weapons §r§7(Able to be used)",
			"§r§c§l✗ §r§cKing of the Hill Event §r§7(Able to warp to)",
			"§r§c§l✗ §r§cHeroic Enchantments §r§7(Able to be used)",
			"§r§c§l✗ §r§cIsland Quests §r§7(Able to be used)",
			"§r§4§l✗ §r§c/fund",
			"§r§7§oIf a feature is not in the list of features",
			"§r§7§oabove, it's going to be in /fund instead.",
			"§r",
			"§r§7($days / 6 Days Passed)",
		]);

		return $item;
	}

	public function getDaySeven(int $age, int $days): Item {
		$percent = min(100, (int) ceil($age / (86400 * 7) * 100));

		$item = ItemFactory::getInstance()->get(171, ($days >= 7 ? 5 : 14));
		$item->setCustomName("§r§c§lDay 7 [§r§7{$percent}%§l§c]");
		$item->setLore([
			"§r§a§l✓ §r§a50 ➜ 60 Level Cap",
			"§r§a§l✓ §r§a7 ➜ 9 Custom Enchantment Limit",
			"§r§7(Not including Pickaxes)",
			"§r§a§l✓ §r§a/Coinflips §r§7(/cf)",
			"§r§a§l✓ §r§aMasks §r§7(Able to be used)",
			"§r§a§l✓ §r§a/skit §r§7(Able to be used)",
			"§r§a§l✓ §r§aMaterial Planet §r§7(Able to warp to)",
			"§r§a§l✓ §r§aMinions §r§7(Able to be used)",
			"§r§a§l✓ §r§aRank Lootboxes §r§7(/kit lootbox)",
			"§r§a§l✓ §r§aHeroic Upgrades §r§7(Able to be used)",
			"§r§a§l✓ §r§aAmulet, Backpack & Belt Sockets §r§7(Able to be used)",
			"§r§a§l✓ §r§aHoly Essence §r§7(Able to be used)",
			"§r§a§l✓ §r§aNether Dimension §r§7(Able to warp to)",
			"§r§a§l✓ §r§aSpecial Sets & Weapons §r§7(Able to be used)",
			"§r§a§l✓ §r§aKing of the Hill Event §r§7(Able to warp to)",
			"§r§a§l✓ §r§aHeroic Enchantments §r§7(Able to be used)",
			"§r§c§l✓ §r§aIsland Quests §r§7(Able to be used)",
			"§r§a§l✓ §r§a/fund §r§7(Requesting Payment...)",
			"§r§7§oGood bye Warp Speed. The government",
			"§r§7§owill now want everyone to prove the",
			"§r§7§oplanet is worthy for such features",
			"§r§7§oto be unlocked. Surprise Me.",
			"§r",
			"§r§7§oIf a feature is not in the list of features",
			"§r§7§oabove, it's going to be in /fund instead.",
			"§r",
			"§r§7($days / 7 Days Passed)",
		]);

		return $item;
	}
}