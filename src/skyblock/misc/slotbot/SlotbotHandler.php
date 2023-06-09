<?php

declare(strict_types=1);

namespace skyblock\misc\slotbot;

use pocketmine\item\Item;
use skyblock\items\itemmods\ItemMod;
use skyblock\items\itemmods\types\MutatedBubbleRodItemMod;
use skyblock\items\lootbox\types\BundleOfJoyLootbox;
use skyblock\items\lootbox\types\HeroicKothLootbox;
use skyblock\items\lootbox\types\IntergalacticAgentLootbox;
use skyblock\items\lootbox\types\itemmod\BubbleItemModsLootbox;
use skyblock\items\lootbox\types\itemmod\SpaceItemModsLootbox;
use skyblock\items\lootbox\types\KothLootbox;
use skyblock\items\lootbox\types\MysteryRankGeneratorLootbox;
use skyblock\items\lootbox\types\rank\AetherPlusLootbox;
use skyblock\items\lootbox\types\rank\AstronomicalLootbox;
use skyblock\items\lootbox\types\slots\SlotBotTicketGenerator;
use skyblock\items\special\types\DestructionEssence;
use skyblock\items\special\types\HeroicUpgradeItem;
use skyblock\items\special\types\minion\MinerMinionSpawnEggItem;
use skyblock\items\special\types\minion\SlayerMinionSpawnEggItem;
use skyblock\items\tools\types\BalenciagaBladeWeapon;
use skyblock\items\tools\types\KrakensDesireBladeWeapon;
use skyblock\items\tools\types\ReadyToComplyTool;
use skyblock\traits\AetherSingletonTrait;
use skyblock\utils\Utils;

class SlotbotHandler {
	use AetherSingletonTrait;

	public Item $current;

	public int $totalBuys = 0;

	public function __construct(){
		self::setInstance($this);

		$arr = $this->getCreditShopItems();

		$this->current = $arr[array_rand($arr)];

		$msg = [
			"§r",
			"§r§l§6SLOT-CREDIT SHOP RESTOCKED!",
			"§r§l§6ITEM: " . $this->current->getName(),
			"§r§l§6COST: §r§a100 Slot-Bot Credits",
			"§r§l§6UPTIME: §r§a8 hours",
			"§r",
			"§r§7Click the Gold Block on the top right of the",
			"§r§7/slot bot menu to open the slot-credit shop.",
			"§r"
		];

		Utils::announce(implode("\n", $msg));
	}


	public function getCreditShopItems(): array {
		return [
			IntergalacticAgentLootbox::getItem(),
			AetherPlusLootbox::getItem(),
			AstronomicalLootbox::getItem(),
			MysteryRankGeneratorLootbox::getItem(),
			KothLootbox::getItem(),
			HeroicKothLootbox::getItem(),
			ReadyToComplyTool::getItem(),
			KrakensDesireBladeWeapon::getItem(),
			BalenciagaBladeWeapon::getItem(),
			BubbleItemModsLootbox::getItem(),
			SpaceItemModsLootbox::getItem(),
			ItemMod::getItem(MutatedBubbleRodItemMod::getUniqueID()),
			MinerMinionSpawnEggItem::getBasic(),
			SlayerMinionSpawnEggItem::getBasic(),
			HeroicUpgradeItem::getItem(100),
			BundleOfJoyLootbox::getItem(),
			SlotBotTicketGenerator::getItem(),
			DestructionEssence::getItem(DestructionEssence::TYPE_DESTRUCTIVE_ESSENCE)
		];
	}
}