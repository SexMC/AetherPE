<?php

declare(strict_types=1);

namespace skyblock\menus\slotbot;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\world\sound\XpLevelUpSound;
use skyblock\items\ItemEditor;
use skyblock\items\itemmods\types\AetherPeHatItemMod;
use skyblock\items\lootbox\LootboxHandler;
use skyblock\items\lootbox\types\slots\SlotsLootbox;
use skyblock\items\special\types\SlotBotTicket;
use skyblock\logs\LogHandler;
use skyblock\logs\types\SlotsLog;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\menus\common\ViewPagedItemsMenu;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class SlotBotMenu extends AetherMenu{
	use AwaitStdTrait;


	private array $slot1 = [2, 11, 20, 29, 38, 47];
	private array $slot2 = [3, 12, 21, 30, 39, 48];
	private array $slot3 = [4, 13, 22, 31, 40, 49];
	private array $slot4 = [5, 14, 23, 32, 41, 50];
	private array $slot5 = [6, 15, 24, 33, 42, 51];

	private array $insertSlots = [47, 48, 49, 50, 51];

	private array $unused = [9, 36, 45, 46, 52, 45, 35, 17, 27]; //todo: remove 27 as it is used in flash sale

	private array $middleSlots = [20, 21, 22, 23, 24];

	private array $sideRolls = [
		1, 7,
		10, 16,
		19, 25,
		28, 34,
		37, 43
	];

	private array $metaRollingSlots = [
		0 => [1, 10, 19, 28, 37],
		1 => [7, 16, 25, 34, 43]
	];

	private int $spinButton = 26;
	private int $metaItemSlot = 13;
	private int $basicLoottableSlot = 0;
	private int $basicLoottableSecondSlot = 9;

	private bool $isSpinning = false;

	private int $awaiting = 0;
	private int $total = 0;

	private array $won = [];

	public function __construct(private Session $session, private int $openedLootboxes){
		$this->openedLootboxes = min(5, $this->openedLootboxes);
		parent::__construct();
	}

	public function onClose(Player $player, Inventory $inventory) : void{
			Utils::executeLater(function() use ($player) : void{
				if($player->isOnline() && $this->isSpinning){

					$this->send($player);
				}
			}, 20);
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$out = $transaction->getOut();
		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();
		$ticketItem = SlotBotTicket::getItem();

		if($this->isSpinning) return;


		if(in_array($slot, $this->insertSlots)){
			if($out->getId() === ItemIds::STAINED_GLASS_PANE){
				//if(!$this->isPreviousSlotFilled($slot)){
				//	$player->sendMessage(Main::PREFIX . "Please fill the other slots first");
				//	return;
				//}


				if($player->getInventory()->contains($ticketItem)){
					$player->getInventory()->removeItem($ticketItem);
					$this->getMenu()->getInventory()->setItem($slot, $ticketItem->setCount($this->getSlotCountByInvSlot($slot)));
					$this->onTicketChange();
				}else{
					$player->sendMessage(Main::PREFIX . "You don't have any slot bot tickets in your inventory");
				}
			}elseif($out->getId() === $ticketItem->getId()){

				$this->getMenu()->getInventory()->setItem($slot, $this->getLockedButton($slot));
				Utils::addItem($player, $ticketItem);

				$this->onTicketChange();
			}

			return;
		}

		if($slot === 8){
			$player->removeCurrentWindow();

			Utils::executeLater(function() use ($player) : void{
				$this->giveInserted($player);
				(new CreditShopMenu())->send($player);
			}, 15);
		}

		if($slot === $this->metaItemSlot){
			$player->removeCurrentWindow();

			$items = [];
			foreach(LootboxHandler::getInstance()->getLootbox(SlotsLootbox::getName())->getNonDuplicatedJackpot() as $reward){
				$s = $reward->getItem();
				$s->getNamedTag()->setString("unique", uniqid());

				if($reward->getMinCount() !== $reward->getMaxCount()){
					$s->setCount(mt_rand($reward->getMinCount(), $reward->getMaxCount()));
				}
				$items[] = $s;
			}

			Utils::executeLater(function() use($player, $items) : void{
				$this->giveInserted($player);
				(new ViewPagedItemsMenu("Meta Loottable - Aether Slot Bot", $items))->send($player);
			}, 15);

			return;
		}

		if($slot === $this->basicLoottableSlot){
			$player->removeCurrentWindow();
			$items = [];
			foreach(LootboxHandler::getInstance()->getLootbox(SlotsLootbox::getName())->getNonDuplicatedBasics() as $reward){
				$s = $reward->getItem();
				$s->getNamedTag()->setString("unique", uniqid());
				if($reward->getMinCount() !== $reward->getMaxCount()){
					$s->setCount(mt_rand($reward->getMinCount(), $reward->getMaxCount()));
				}
				$items[] = $s;
			}

			Utils::executeLater(function() use($player, $items) : void{
				$this->giveInserted($player);
				(new ViewPagedItemsMenu("Basic Loottable - Aether Slot Bot", $items))->send($player);
			}, 15);

			return;
		}

		if($slot === $this->spinButton){
			if($out->getId() === ItemIds::MAGMA_CREAM){
				$player->sendMessage(Main::PREFIX . "You cannot spin if you have no tickets inserted");
				return;
			}

			$this->startSpinning();

			return;
		}
	}




	public function startSpinning() : void{
		$this->isSpinning = true;

		foreach($this->insertSlots as $k => $v){
			if($this->getMenu()->getInventory()->getItem($v)->getId() !== ItemIds::STAINED_GLASS_PANE){
				$this->startRegular($k + 1);
				$this->awaiting++;
				$this->total++;
			}
		}

		$this->startMeta();
	}

	public function giveInserted(Player $player): void {
		if(!$player->isOnline()) return;

		foreach($this->insertSlots as $slot){
			if($this->getMenu()->getInventory()->getItem($slot)->getId() !== ItemIds::STAINED_GLASS_PANE){
				Utils::addItem($player, $this->getMenu()->getInventory()->getItem($slot)->setCount(1));
				$this->getMenu()->getInventory()->setItem($slot, $this->getLockedButton($slot));
			}
		}
	}


	public function startMeta(){

		Await::f2c(function(){

			$placed = 0;
			foreach($this->insertSlots as $slot){
				if($this->getMenu()->getInventory()->getItem($slot)->getId() !== ItemIds::STAINED_GLASS_PANE){
					$placed++;
				}
			}

			$chance = 2 * $this->openedLootboxes + $placed;

			$populate = [
				$this->generateMeta($chance),
				$this->generateMeta($chance),
				$this->generateMeta($chance),
				$this->generateMeta($chance),
				$this->generateMeta($chance),
			];
			$maxSpins = 25;
			$sleepFor = 3;

			while($maxSpins > 0){
				if($maxSpins <= 6){
					$sleepFor = 8;
				}elseif($maxSpins <= 3){
					$sleepFor = 15;
				}

				array_pop($populate);

				array_unshift($populate, $this->generateMeta($chance));

				foreach($populate as $k => $v){
					$this->getMenu()->getInventory()->setItem($this->metaRollingSlots[0][$k], $v);
					$this->getMenu()->getInventory()->setItem($this->metaRollingSlots[1][$k], $v);
				}

				if($p = $this->session->getPlayer()){
					$p->broadcastSound(new XpCollectSound(), [$p]);
				}


				$maxSpins--;
				yield $this->getStd()->sleep($sleepFor);
			}

			if($this->getMenu()->getInventory()->getItem(19)->getId() === ItemIds::DYE){
				yield $this->getStd()->sleep(20);

				$populate = [
					$this->getRandomMetaReward(),
					$this->getRandomMetaReward(),
					$this->getRandomMetaReward(),
					$this->getRandomMetaReward(),
					$this->getRandomMetaReward(),
				];
				$maxSpins = 25;
				$sleepFor = 3;

				while($maxSpins > 0){
					if($maxSpins <= 6){
						$sleepFor = 8;
					}elseif($maxSpins <= 3){
						$sleepFor = 15;
					}

					array_pop($populate);

					array_unshift($populate, $this->getRandomMetaReward());

					foreach($populate as $k => $v){
						$this->getMenu()->getInventory()->setItem($this->middleSlots[$k], $v);
					}

					$maxSpins--;
					yield $this->getStd()->sleep($sleepFor);
				}

				yield $this->getStd()->sleep(8);

				$middle = 22;
				$this->getMenu()->getInventory()->clear($middle - 1);
				$this->getMenu()->getInventory()->clear($middle - 2);
				$this->getMenu()->getInventory()->clear($middle + 2);
				$this->getMenu()->getInventory()->clear($middle + 1);

				$reward = $this->getMenu()->getInventory()->getItem($middle);
				if($p = $this->session->getPlayer()){
					$p->broadcastSound(new XpLevelUpSound(30), [$p]);
				}

				$this->won[] = $reward;
				$this->session->addCollectItem($reward);

				$this->end();
			} else {
				yield $this->getStd()->sleep(5);
				$this->end();
			}
		});

	}

	public function end(): void {
		if(($p = $this->session->getPlayer())){
			$this->isSpinning = false;
			$p->removeCurrentWindow();
			$p->sendMessage(Main::PREFIX . "rewards has been added to your /collect. (Take them out ASAP to avoid issues)");

			if(ItemEditor::hasItemMod($p->getArmorInventory()->getHelmet(), AetherPeHatItemMod::getUniqueID()) && mt_rand(1, 80) === 1){
				$p->sendMessage(Main::PREFIX . AetherPeHatItemMod::getUniqueID() . " has given you +1 slot bot ticket");
				Utils::addItem($p, SlotBotTicket::getItem());
			}
		}

		$msg = [
			"§r",
			"              §r§l§bAether§7-§5Slot Bot",
			"§r",
			"     §r§7{$this->session->getUsername()} rolled the slot /bot with §f{$this->total} tickets §7and won:",
		];

		foreach($this->won as $reward){
			$msg[] = str_repeat(" ", mt_rand(4, 9)) . " §r§l§b* §r§f{$reward->getCount()}x §r§l§b{$reward->getName()}";
		}

		$msg[] = "§r";
		Utils::announce(implode("\n", $msg));
		LogHandler::getInstance()->log(new SlotsLog($this->session->getUsername(), implode("\n", $msg), $this->won));
	}

	public function generateMeta(int $chance) : Item{
		if(mt_rand(1, 1000) <= $chance){
			return VanillaItems::CYAN_DYE()->setCustomName("§b§lMETA")->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
		}

		$rand = mt_rand(1, 3);
		if($rand === 1){
			$item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLUE());
		}elseif($rand === 2){
			$item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIGHT_BLUE());
		}else $item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::CYAN());

		return $item->asItem()->setCustomName("§c:(");
	}

	public function startRegular(int $slot){
		Await::f2c(function() use ($slot){

			$slots = match ($slot) {
				1 => $this->slot1,
				2 => $this->slot2,
				3 => $this->slot3,
				4 => $this->slot4,
				5 => $this->slot5,
			};

			$maxSpins = 25;
			$sleepFor = 3;
			$populate = [
				$this->getRandomRegularReward(),
				$this->getRandomRegularReward(),
				$this->getRandomRegularReward(),
				$this->getRandomRegularReward(),
				$this->getRandomRegularReward()
			];

			while($maxSpins > 0){
				if($maxSpins <= 6){
					$sleepFor = 8;
				}elseif($maxSpins <= 3){
					$sleepFor = 15;
				}

				array_pop($populate);

				array_unshift($populate, $this->getRandomRegularReward());

				foreach($populate as $k => $v){
					$this->getMenu()->getInventory()->setItem($slots[$k], $v);
				}

				$maxSpins--;
				yield $this->getStd()->sleep($sleepFor);
			}

			$this->session->addCollectItem($i = $this->getMenu()->getInventory()->getItem($s = $slots[2]));
			$this->session->increaseSlotCredits(1);
			if(($p = $this->session->getPlayer())){
				$p->broadcastSound(new XpLevelUpSound(30), [$p]);
			}

			$this->getMenu()->getInventory()->clear($s + 9);
			$this->getMenu()->getInventory()->clear($s + 18);
			$this->getMenu()->getInventory()->clear($s - 18);
			$this->getMenu()->getInventory()->clear($s - 9);
			$this->awaiting--;
			$this->won[] = $i;
		});
	}

	public function getRandomRegularReward() : Item{
		$reward = LootboxHandler::getInstance()->getLootbox(SlotsLootbox::getName())->getRandomReward();
		$i = $reward->getItem();

		if($reward->getMinCount() !== $reward->getMaxCount()){
			$i->setCount(mt_rand($reward->getMinCount(), $reward->getMaxCount()));
		}

		return $i;
	}

	public function getRandomMetaReward() : Item{
		$reward = LootboxHandler::getInstance()->getLootbox(SlotsLootbox::getName())->getJackpotReward();
		$i = $reward->getItem();

		if($reward->getMinCount() !== $reward->getMaxCount()){
			$i->setCount(mt_rand($reward->getMinCount(), $reward->getMaxCount()));
		}

		return $i;
	}

	public function onTicketChange() : void{
		$placed = 0;
		foreach($this->insertSlots as $slot){
			if($this->getMenu()->getInventory()->getItem($slot)->getId() !== ItemIds::STAINED_GLASS_PANE){
				$placed++;
			}
		}


		if($placed <= 0){
			$item = $this->getEmptySlot();
		}else $item = $this->getFullSlot($placed);


		$this->getMenu()->getInventory()->setItem($this->spinButton, $item);
		$this->getMenu()->getInventory()->setItem($this->metaItemSlot, $this->getMetaSpinItem($placed));
	}

	public function isPreviousSlotFilled(int $slot) : bool{
		$search = array_search($slot, $this->insertSlots);

		while($search > 0){
			$search--;

			if($this->menu->getInventory()->getItem($this->insertSlots[$search])->getId() === ItemIds::STAINED_GLASS_PANE){
				return false;
			}
		}

		return true;
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("AetherPE - Slot Bot");
		$menu->getInventory()->setItem(53, $this->getHowToPlayItem());

		foreach($this->unused as $unused){
			$menu->getInventory()->setItem($unused, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
			$menu->getInventory()->setItem($this->basicLoottableSecondSlot, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
		}

		foreach($this->sideRolls as $sideRoll){
			$menu->getInventory()->setItem($sideRoll, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK())->asItem()->setCustomName(" "));
		}

		foreach($this->insertSlots as $slot){
			$menu->getInventory()->setItem($slot, $this->getLockedButton($slot));
		}

		foreach($this->middleSlots as $slot){
			$menu->getInventory()->setItem($slot, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIME())->asItem()->setCustomName("§r§l§aReward Slot"));
		}

		$menu->getInventory()->setItem(8, $this->getSlotCreditShop());
		$menu->getInventory()->setItem($this->spinButton, $this->getEmptySlot());
		$menu->getInventory()->setItem($this->metaItemSlot, $this->getMetaSpinItem());
		$menu->getInventory()->setItem($this->basicLoottableSlot, $this->getLoottableItem());
		//$menu->getInventory()->setItem($this->basicLoottableSecondSlot, $this->getSecondLoottableItem());
		$menu->getInventory()->setItem(18, $this->getFlashSaleItem());


		return $menu;
	}

	public function getLockedButton(int $slot) : Item{
		$item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem();
		$item->setCustomName("§r§l§cLOCKED§r§7 (Right-Click to insert)");
		$item->setLore([
			"§r§7Requires a slot bot ticket to roll",
			"",
			"§r§7Purchase slot bot tickets",
			"at store.aetherpe.net"
		]);

		return $item->setCount($this->getSlotCountByInvSlot($slot));
	}

	public function getEmptySlot(int $tickets = 0) : Item{
		$lb = $this->openedLootboxes;
		$meta = $tickets * 0.1 + $lb * 0.2;

		$item = VanillaItems::MAGMA_CREAM();

		$item->setCustomName("§r§c§lMissing Ticket (§r§7Missing Tickets§l§c)");
		$item->setLore([
			"§r§7Insert a Slot Bot Ticket",
			"§r§7to spin the bot.",
			"§r",
			"§r§l§3Meta Spin",
			"§r§bMeta Chance: §f$meta% §7(+0.1% per slot placed)",
			"§r§bWeekly /lootbox: §f{$lb}§7/ 5",
			"§r",
			"§r§7Increase your chance of rolling",
			"§r§7the §bmeta spin §7by opening",
			"§r§7more of this week's /lootbox: §r§l§6*§c*§6* §bAETHER CRATE: §6E§cA§6R§cL§6Y §cS§6U§cM§6M§cE§6R §c2§60§c2§62 §r§l§6*§c*§6*",
			"§r§l§4Yet? §r§7(Right-Click)",
			"§r§7(+0.2% per lootbox opened)",
			"§r",
			"§r§7Purchase Slot Bot Tickets and",
			"§r§7Lootboxes at §fstore.aetherpe.net",
		]);

		$item->setCount(max(1, $tickets));

		return $item;
	}

	public function getMetaSpinItem(int $tickets = 0) : Item{
		$lb = $this->openedLootboxes;
		$meta = $tickets * 0.1 + $lb * 0.2;

		$item = VanillaItems::CYAN_DYE();

		$item->setCustomName("§r§l§3Meta Spin");
		$item->setLore([
			"§r§bMeta Chance: §f{$meta}% §7(+0.1% per slot placed)",
			"§r§bWeekly /lootbox: §f{$lb}§7/5",
			"§r",
			"§r§7Increase your chance of rolling",
			"§r§7the §bmeta spin §7by opening",
			"§r§7more of this week's /lootbox: §r§l§6*§c*§6* §bAETHER CRATE: §6E§cA§6R§cL§6Y §cS§6U§cM§6M§cE§6R §c2§60§c2§62 §r§l§6*§c*§6*",
			"§r§l§4Yet? §r§7(Right-Click)",
			"§r§7(+0.2% per lootbox opened)",
			"§r",
			"§r§7This \"meta\" roll offers some of",
			"§r§7the best loot in the Galaxy!",
			"§r",
			"§r§7Click to view loot table",
		]);

		$item->setCount(max(1, $this->openedLootboxes));

		return $item;
	}

	public function getFullSlot(int $tickets = 0) : Item{
		$lb = $this->openedLootboxes;
		$meta = $tickets * 0.1 + $lb * 0.2;

		$item = VanillaItems::SLIMEBALL();

		$item->setCustomName("§r§a§lRoll Ticket (§r§7$tickets Ticket Inserted§l§a)");
		$item->setLore([
			"§r§7Click to roll your Slot Bot Ticket(s)",
			"§r§7inserted.",
			"§r",
			"§r§l§3Meta Spin",
			"§r§bMeta Chance: §f$meta% §7(+0.1% per slot placed)",
			"§r§bWeekly /lootbox: §f{$lb}§7/ 5",
			"§r",
			"§r§7Increase your chance of rolling",
			"§r§7the §bmeta spin §7by opening",
			"§r§7more of this week's /lootbox: §r§l§6*§c*§6* §bAETHER CRATE: §6E§cA§6R§cL§6Y §cS§6U§cM§6M§cE§6R §c2§60§c2§62 §r§l§6*§c*§6*",
			"§r§l§4Yet? §r§7(Right-Click)",
			"§r§7(+0.2% per lootbox opened)",
			"§r",
			"§r§7Purchase Slot Bot Tickets and",
			"§r§7Lootboxes at §fstore.aetherpe.net",
		]);

		$item->setCount(max(1, $tickets));

		return $item;
	}


	public function getSlotCountByInvSlot(int $slot) : int{
		return match ($slot) {
			48 => 2,
			49 => 3,
			50 => 4,
			51 => 5,
			default => 1
		};
	}

	public function getHowToPlayItem() : Item{
		$item = VanillaItems::BOOK();
		$item->setCustomName("§r§b§lHow to Play");
		$item->setLore([
			"§r§l§d1. §r§7Insert up to 5 Slot Bot Tickets",
			"§r§7into the bot from your inventory",
			"",
			"§r§l§d2. §r§7Click \"Spin\" (on the right) to roll",
			"§r§7the bot or, click \"Return\" (on the left)",
			"§r§7to withdraw all inserted tickets.",
			"",
			"§r§l§d3. §r§7When the bot finishes spinning",
			"§r§7the item which lands on the Reward Slot",
			"§r§7will be the item you receive.",
			"",
			"§r§l§fTIP: §r§7Closing the bot while spinning will",
			"§r§7give the wards you would have",
			"§r§7received, otherwise your inserted",
			"§r§7tickets will be refunded.",
		]);

		return $item;
	}

	public function getSlotCreditShop(): Item {
		$item = VanillaBlocks::MOSSY_COBBLESTONE()->asItem();
		$item->setCustomName("§r§6§lSlot Credit Shop");
		$item->setLore([
			"§r§eAether-Slot Credits: §f" . $this->session->getSlotCredits(),
			"§r",
			"§r§7The Slot Credit Shop is a §ereward store",
			"§r§7where players can spend their credits",
			"§r§7gained from rolling the Aether-Slot Bot.",
			"§r",
			"§r§7For each ticket you roll in the Slot Bot,",
			"§r§7you will receive §e1 Aether-Slot Credit",
			"§r§7However, §eyou cannot have more than 250",
			"§r§ecredits at a time.",
			"§r",
			"§r§7The items in the Credit Shop are",
			"§r§7only available for §e6 hours §7before",
			"§r§7being replaced with new items.",
			"§r",
			"§r§7Click to open the Slot Credit Shop",
		]);

		return $item;
	}

	public function getLoottableItem(): Item {
		$item = VanillaBlocks::BEACON()->asItem();
		$item->setCustomName("§r§f§lLoot Table");
		$item->setLore([
			"§r§7The Slot Bot contains the items",
			"§r§7from the following Lootboxes,",
			"§r§7which will randomize every week",
			"§r",
			"§r§l§f* §r§l§fLootbox: §l§6Astronaut Package",
			"§r§l§f* §r§l§fLootbox: §l§cArmor or Armour?",
			"§r§l§f* §r§l§fLootbox: §l§5Intergalactic Chocolate Box",
			"§r§l§f* §r§l§fLootbox: §bAether§3+",
			"§r§l§f* §r§l§fLootbox: §5Astronomical",
			"§r§l§f* §r§l§fLootbox: §r§l§bPlanet §3Neptune",
			"§r§l§f* §r§f§lLootbox: §4Jealous Yet?",
			"§r§l§f* §r§l§fLootbox: §r§l§fEnhancement Attributes",
			"§r§l§f* §r§l§fLootbox: §l§4Warden's Vault",
			"§r§l§f* §r§l§6** §r§6§lTurkey Item Mod Generator §r§l§6**",
			"§r§l§f* §r§l§4** §r§4§lWarden Item Mod Generator §r§l§4**",
			"§r§l§f* §r§l§4*§c§4* §cMystery Rank Generator §r§l§4*§c§4* §r§7(Right-Click)",
			"§r§l§f* §r§l§d*§5* §r§l§5Dark Age §r§l§d*§5* §r§7(Right-Click)",
			"§r§l§f* §r§l§6M§ee§em§eo§gr§gy §6L§ea§gn§ge §eB§go§gx §fGenerator",
			"§r§l§f* §r§l§fItem Mod (§4Warden's Cape§f)",
			"§r§l§f* §r§l§fItem Mod (§fCrystal Amulet§f)",
			"§r§l§f* §r§l§e*§d*§e* §bAETHER CRATE: §l§dAP§eRI§dL 2§e02§d2 §r§l§e*§d*§e*",
			"§r§l§f* §r§l§d*§6*§d* §bAETHER CRATE: §dM§6A§dY §62§d0§62§d2 §r§l§d*§6*§d*",
			"§r§l§f* §r§l§6Rank Voucher \"§r§l§bAether§3+§l§6\" §r§7(Right-Click)",
			"§r§l§f* §r§l§6Rank Voucher \"§r§l§5+ Astronomical +§l§6\" §r§7(Right-Click)",
			"§r",
			"§r§7Click to View Loot Table",
		]);

		return $item;
	}

	public function getSecondLoottableItem(): Item {
		$item = VanillaBlocks::BEACON()->asItem();
		$item->setCustomName("§r§f§lLoot Table");
		$item->setCustomName("§r§l§f* §r§l§f4x §r§l§bAether§7-§5Slot Bot §fTicket §r§l§cII");
		$item->setCustomName("§r§l§f* §r§l§f4x §r§l§bAether§7-§5Slot Bot §fTicket §r§l§cII");
		$item->setLore([
			"§r§l§f* §r§l§f6x §r§l§bAether§7-§5Slot Bot §fTicket §r§l§cII",
			"§r§l§f* §r§l§f12x §r§l§bAether§7-§5Slot Bot §fTicket §r§l§cII",
			"§r§l§f* §r§l§e*§g** §eBundle of' Joy §r§l§g**§e* §r§7(Right-Click) §r§l§cII",
			"§r§l§f* §r§l§d*§5*§d* §r§l§dCosmonaut Papers §r§l§d*§5*§d* §r§7(Right-Click) §r§l§cII",
			"§r§l§f* §r§l§fItem Mod (§2Mutated §bBubble §3Rod§f) §r§l§cII",
			"§r§l§f* §r§l§6M§ee§em§eo§gr§gy §6L§ea§gn§ge §eB§go§gx §r§7(Right-Click) §r§l§cII",
			"§r§l§f* §r§l§fLootbox: §r§l§4Jealous Yet? §r§7(Right-Click) §r§l§cII",
			"§r§l§f* §r§l§4Ready to Comply §r§l§cII",
			"§r§l§f* §r§l§4§r§l§dGuar§5dian §dFex§5's §dFarm§5ing §dToo§5l §r§l§cII",
			"§r§l§f* §r§l§fItem Mod (§aLucky Jackhammer§f) §r§l§cII",
			"§r§l§f* §r§l§fItem Mod (§8Broken Heart§f) §r§l§cII",
			"§r§l§f* §r§l§f*§d* §r§l§bAether§7-§5Slot Bot §fTicket §bGenerator §r§l§f*§d* §r§7(Right-Click) §r§l§cII",
			"§r§l§f* §r§l§cBalenciaga §eBlade §r§l§cII",
			"§r§l§f* §r§l§cKr§4ak§cen'§4s §cD§4e§cs§4i§cr§4e §cBl§4ad§ce §r§l§cII",
			"§r§l§f* §r§l§fLootbox: §r§l§4Jealous Yet? §r§7(Right-Click) §r§l§cII",
			"§r§l§f* §r§l§3*§9*§b* §bAETHER CRATE: §l§3N§bE§3P§bT§3U§bN§3E §cRESET #1 §r§l§3*§9*§b* §r§7(Right-Click) §r§l§cII",
			"§r§l§f* §r§l§e*§d*§e* §bAETHER CRATE: §l§dAP§eRI§dL 2§e02§d2 §r§l§e*§d*§e* §r§7(Right-Click) §r§l§cII",
			"§r§l§f* §r§l§6Rank Voucher \"§r§l§5Astronomical§l§6\" §r§7(Right-Click) §r§l§cII",
			"§r",
			"§r§l§cII §r§crefers to \"Item Itself\"",
			"§r§cwhich means, you get the item itself",
			"§r§cinstead of the random loot of the lootbox.",
			"§r",
			"§r§7Click to View Loot Table",
		]);

		return $item;
	}

	public function getFlashSaleItem(): Item {
		$item = VanillaBlocks::IRON_BARS()->asItem();
		$item->setCustomName("§r§4§lAether-Slot Flash Sale");
		$item->setLore([
			"§r§7The /slot bot Flash sale is a §cjackpot/event",
			"§r§7that will reward one lucky player with the current",
			"§r§7OP item on offer on top of their /bot roll",
			"§r",
			"§r§l§4OUT OF ORDER",
		]);

		return $item;
	}

	public function getRandomRollingGlass() : Item{
		return VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::getAll()[array_rand(DyeColor::getAll())])->asItem()->setCustomName(" ");
	}
}