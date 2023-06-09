<?php

declare(strict_types=1);

namespace skyblock\menus\bank;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\BankUtils;
use skyblock\utils\TimeUtils;
use SOFe\AwaitGenerator\Await;

class BankDepositMenu extends AetherMenu {
	use AwaitStdTrait;

	public function __construct(private AetherPlayer $player, private Session $depositTo, private Session $takeMoneyFrom, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("Bank Deposit");


		$glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");

		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, $glass);
		}

		$menu->getInventory()->setItem(20, $this->getDepositAll());
		$menu->getInventory()->setItem(22, $this->getDepositHalf());
		$menu->getInventory()->setItem(24, $this->getDepositCustom());

		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));



		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$p = $transaction->getPlayer();
		assert($p instanceof AetherPlayer);

		if($slot === 49){
			$p->removeCurrentWindow();
		}

		if($slot === 20) {
			$p->removeCurrentWindow();
			BankUtils::deposit($p, $this->takeMoneyFrom->getPurse(), $this->takeMoneyFrom, $this->depositTo);
		}

		if($slot === 22) {
			$p->removeCurrentWindow();
			BankUtils::deposit($p, (int) floor($this->takeMoneyFrom->getPurse() * 0.5), $this->takeMoneyFrom, $this->depositTo);
		}

		if($slot === 24) {
			Await::f2c(function() use($p) {
				$p->removeCurrentWindow();

				yield $this->getStd()->sleep(20);

				$p->sendForm(new CustomForm("Bank Deposit", [new Input("Enter amount", "", "1000")], yield Await::RESOLVE_MULTI));
				/**
				 * @var AetherPlayer $player
				 * @var CustomFormResponse $response
				 */
				$response = (yield Await::ONCE)[1];

				$input = abs(intval($response->getString("Enter amount")));

				if($input <= 0){
					$p->sendMessage(Main::PREFIX . "Amount must be greater than 0");
					return;
				}

				if(!$p->isOnline()) return;

				BankUtils::deposit($p, (int) floor($input), $this->takeMoneyFrom, $this->depositTo);
			});
		}
	}

	public function getDepositAll(): Item {
		$item = VanillaBlocks::CHEST()->asItem()->setCount(64);
		$item->setCustomName("§r§l§a» Deposit whole purse «");
		$item->setLore([
			"§r§8Bank Deposit",
			"§r",
			"§r§7Bank Balance: " . number_format($this->depositTo->getBank()),
			"§r§7Amount to deposit: " . number_format($this->takeMoneyFrom->getPurse()),
			"§r",
			"§r§eClick to deposit coins"
		]);

		return $item;
	}

	public function getDepositHalf(): Item {
		$item = VanillaBlocks::CHEST()->asItem()->setCount(32);
		$item->setCustomName("§r§l§a» Deposit half purse «");
		$item->setLore([
			"§r§8Bank Deposit",
			"§r",
			"§r§7Bank Balance: " . number_format($this->depositTo->getBank()),
			"§r§7Amount to deposit: " . number_format($this->takeMoneyFrom->getPurse() * 0.5),
			"§r",
			"§r§eClick to deposit coins"
		]);

		return $item;
	}

	public function getDepositCustom(): Item {
		$item = VanillaBlocks::OAK_SIGN()->asItem()->setCount(1);
		$item->setCustomName("§r§l§a» Specific Amount «");
		$item->setLore([
			"§r§8Bank Deposit",
			"§r",
			"§r§7Bank Balance: " . number_format($this->depositTo->getBank()),
			"§r",
			"§r§eClick to deposit coins"
		]);

		return $item;
	}
}