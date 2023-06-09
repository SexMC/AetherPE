<?php

declare(strict_types=1);

namespace skyblock\menus\bank;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\commands\economy\GiveMoneyCommand;
use skyblock\commands\economy\SetMoneyCommand;
use skyblock\items\crates\Crate;
use skyblock\items\crates\CrateItem;
use skyblock\menus\AetherMenu;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\BankUtils;
use skyblock\utils\TimeUtils;

class BankMenu extends AetherMenu {


	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("Bank");


		$glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");

		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, $glass);
		}

		$menu->getInventory()->setItem(20, $this->getDepositItem());
		$menu->getInventory()->setItem(22, $this->getWithdrawItem());
		$menu->getInventory()->setItem(24, $this->getHistory());
		$menu->getInventory()->setItem(53, $this->getInfoItem());

		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));



		return $menu;
	}

	public function getHistory(): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::MAP);
		$item->setCustomName("§r§l§a» Recent Transactions «");
		$lore = ["§r"];
		foreach($this->getDepositSession()->getTransactionHistory() as $data){
			[$type, $amount, $unix, $username] = $data;

			$prefix = $type === BankUtils::DEPOSIT ? "§a+" : "§c-";
			$lore[] = "§r" . $prefix . "§6 " . number_format($amount) . "§7, §b{$username}§7, §e" . TimeUtils::getFormattedTime(time() - $unix) . " ago";
		}

		$item->setLore($lore);

		return $item;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$p = $transaction->getPlayer();
		assert($p instanceof AetherPlayer);

		if($slot === 49){
			$p->removeCurrentWindow();
		}

		if($slot === 20){
			(new BankDepositMenu($p, $this->getDepositSession(), $p->getCurrentProfilePlayerSession(), $this))->send($p);
		}

		if($slot === 22){
			(new BankWithdrawMenu($p, $this->getDepositSession(), $p->getCurrentProfilePlayerSession(), $this))->send($p);
		}
	}

	public function getInfoItem(): Item {
		$item = VanillaBlocks::REDSTONE_TORCH()->asItem();
		$item->setCustomName("§r§l§a» Information «");

		$s = $this->getDepositSession();

		$item->setLore([
			"§r§7Keep your coins safe in the bank!",
			"§r§7You lose half of the coins in your",
			"§r§7purse when dying in combat.",
			"§r",
			"§r§7Bank Limit: §6" . number_format(BankUtils::maxLimit($s->getBankLevel())),
			"§r",
			"§r§7The banker rewards you every 31",
			"§r§7hours with §binterest§7 for the",
			"§r§7coins in your bank balance.",
			"§r",
			"§r§7Interest in: §b" . TimeUtils::getFormattedTime(BankUtils::INTEREST_TIME - (time() - $this->player->getCurrentProfile()->getProfileSession()->getLastInterestUnix())),
			"§r§7Last Interest: §6" . number_format($s->getLastInterest()) . " coins",
			"§r§7Projected: §6" . number_format(BankUtils::getInterestAmount($s)) . " coins §b(" . number_format(BankUtils::getInterestPercent($s), 3) . "%)",
		]);

		return $item;
	}


	public function getWithdrawItem(): Item {
		$item = BlockFactory::getInstance()->get(BlockLegacyIds::DISPENSER, 0)->asItem();
		$item->setCustomName("§r§l§a» Withdraw Coins «");

		$s = $this->getDepositSession();
		$coins = $s->getBank();


		$item->setLore([
			"§r§7Bank Balance: §6" . number_format($coins),
			"§r",
			"§r§7Take your coins out of the bank",
			"§r§7in order to spend them",
			"§r",
			"§r§eClick to withdraw coins!"
		]);

		return $item;
	}

	public function getDepositItem(): Item {
		$item = VanillaBlocks::CHEST()->asItem();
		$item->setCustomName("§r§l§a» Deposit Coins «");

		$s = $this->getDepositSession();
		$coins = $s->getBank();


		$item->setLore([
			"§r§7Bank Balance: §6" . number_format($coins),
			"§r",
			"§r§7Store coins in the bank to keep",
			"§r§7them safe while you go on",
			"§r§7adventures!",
			"§r",
			"§r§7Until interest: §b" . TimeUtils::getFormattedTime(BankUtils::INTEREST_TIME - (time() - $this->player->getCurrentProfile()->getProfileSession()->getLastInterestUnix())),
			"§r",
			"§r§eClick to make a deposit!"
		]);

		return $item;
	}

	public function getDepositSession(): Session {
		$profile = $this->player->getCurrentProfile();

		return $profile->getProfileSession();
	}
}