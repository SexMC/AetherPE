<?php

declare(strict_types=1);

namespace skyblock\menus\minions;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\entity\minion\BaseMinion;
use skyblock\items\SkyblockItems;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\minion\AutoSmelter;
use skyblock\items\special\types\minion\Compactor;
use skyblock\items\special\types\minion\DiamondSpreading;
use skyblock\items\special\types\minion\EnchantedLavaBucket;
use skyblock\items\special\types\minion\SuperCompactor3000;
use skyblock\items\tools\SpecialWeapon;
use skyblock\items\tools\types\pve\FlintShovel;
use skyblock\menus\AetherMenu;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\Utils;

class MinionMenu extends AetherMenu {

	private array $inventorySlots = [
		21, 22, 23, 24, 25,
		30, 31, 32, 33, 34,
		39, 40, 41, 42, 43
	];

	public function __construct(private BaseMinion $minion, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$level = $this->minion->getInt("level");


		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($this->minion->getString("name") . "Minion " . CustomEnchantUtils::roman($level));
		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK())->asItem()->setCustomName(" "));
		}

		$menu->getInventory()->setItem(37, $this->minion->getUpgradeItem1() ?? $this->getUpgradeSlotItem());
		$menu->getInventory()->setItem(46, $this->minion->getUpgradeItem2() ?? $this->getUpgradeSlotItem());
		$menu->getInventory()->setItem(19, $this->getFuelSlotItem());
		$menu->getInventory()->setItem(53, $this->getPickUpSlotItem());
		$menu->getInventory()->setItem(48, VanillaBlocks::CHEST()->asItem()->setCustomName("§aCollect All")->setLore(["§r§eClick to collect all items!"]));


		$item = $this->minion->getEggItem();
		$menu->getInventory()->setItem(4, VanillaItems::PLAYER_HEAD()->setCustomName($item->getCustomName())->setLore($item->getLore()));

		$slotsByLevel = $this->minion->getStorageSizeByLevel($level);
		foreach($this->inventorySlots as $k => $slot){
			if($slotsByLevel >= $k +1){
				$menu->getInventory()->setItem($slot, $this->minion->getMinionInventory()->getItem($k));
				continue;
			}

			$menu->getInventory()->setItem($slot, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::WHITE())->asItem()->setCustomName("§r§eLocked"));
		}


		return $menu;
	}

	public function isValidMinionUpgrade(Item $item): bool {
		$array = [
			SkyblockItems::AUTO_SMELTER(), SkyblockItems::COMPACTOR(), SkyblockItems::DIAMOND_SPREADING(), SkyblockItems::ENCHANTED_LAVA_BUCKET(), SkyblockItems::SUPER_COMPACTOR_3000(), SkyblockItems::FLINT_SHOVEL()
		];

		return
			in_array($item->getNamedTag()->getString(SpecialItem::TAG_SPECIAL_ITEM, ""), $array) ||
			in_array($item->getNamedTag()->getString(SpecialWeapon::TAG_SPECIAL_TOOL, ""), $array);
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		if($this->minion->isFlaggedForDespawn()) return;

		$slot = $transaction->getAction()->getSlot();
		$player = $transaction->getPlayer();
		$in = $transaction->getIn();
		$out = $transaction->getOut();

		if($slot === 19){
			(new MinionFuelInventoryMenu($this->minion, $this))->send($player);
			return;
		}

		if($slot === 37){
			if($this->minion->getUpgradeItem1()){
				Utils::addItem($player, $this->minion->getUpgradeItem1());
				$this->menu->getInventory()->setItem(37, $this->getUpgradeSlotItem());
				$this->minion->setUpgradeItem1(null);
				return;
			}

			if($this->isValidMinionUpgrade($in)){
				$in->setCount(1);
				$player->getCursorInventory()->removeItem($in);
				$this->menu->getInventory()->setItem(37, $in);
				$this->minion->setUpgradeItem1($in);
			}
			//upgrade slot 1
		}


		if($slot === 46){
			if($this->minion->getUpgradeItem2()){
				Utils::addItem($player, $this->minion->getUpgradeItem2());
				$this->menu->getInventory()->setItem(46, $this->getUpgradeSlotItem());
				$this->minion->setUpgradeItem2(null);
				return;
			}
			if($this->isValidMinionUpgrade($in)){
				$in->setCount(1);
				$player->getCursorInventory()->removeItem($in);
				$this->menu->getInventory()->setItem(46, $in);
				$this->minion->setUpgradeItem2($in);
			}


			//upgrade slot 2
		}

		if($slot === 53){
			$item = $this->minion->getEggItem();
			$this->minion->flagForDespawn();

			if($this->minion->getUpgradeItem1()){
				$player->getInventory()->addItem($this->minion->getUpgradeItem1());
			}

			if($this->minion->getUpgradeItem2()){
				$player->getInventory()->addItem($this->minion->getUpgradeItem2());
			}

			Utils::addItem($player, $item);
			$player->removeCurrentWindow();
		}
		
		if($slot === 48){
			foreach($this->inventorySlots as $s){
				$item = $this->getMenu()->getInventory()->getItem($s);
				
				if($item->getId() === ItemIds::STAINED_GLASS_PANE || $item->isNull()){
					continue;
				}
				$item->getNamedTag()->removeTag("menuItem");

				if(!$this->minion->getMinionInventory()->contains($item)) continue;

				$this->minion->getMinionInventory()->removeItem($item);
				$this->menu->getInventory()->setItem($s, VanillaItems::AIR());

				Utils::addItem($player, $item, true, true);
			}
		}
		
		if(in_array($slot, $this->inventorySlots)){
			if($in->isNull() && !$out->isNull()){
				$out->getNamedTag()->removeTag("menuItem");

				if(!$this->minion->getMinionInventory()->contains($out)) return;


				$this->minion->getMinionInventory()->removeItem($out);
				$this->getMenu()->getInventory()->setItem($slot, VanillaItems::AIR());

				Utils::addItem($player, $out);
			}
		}
	}

	public function getUpgradeSlotItem(): Item {
		$item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::YELLOW())->asItem();
		$item->setCustomName("§r§aUpgrade Slot");
		$item->setLore([
			"§r§7You can improve your minion by",
			"§r§7adding a minion upgrade item",
			"§r§7here."
		]);

		return $item;
	}

	public function getPickUpSlotItem(): Item {
		$item = VanillaItems::NETHER_STAR();
		$item->setCustomName("§r§aPickup Minion");
		$item->setLore([
			"§r§7Click to despawn the minion",
			"§r§7and retrieve the minion egg.",
		]);

		return $item;
	}

	public function getFuelSlotItem(): Item {
		$item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::ORANGE())->asItem();
		$item->setCustomName("§r§aFuel §7(Right-Click)");
		$item->setLore([
			"§r§7Add fuel to your minion",
			"§r§7to make them start working.",
			"§r§o§7Enslave em!",
		]);

		return $item;
	}
}