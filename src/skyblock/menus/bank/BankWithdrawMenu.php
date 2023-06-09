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

class BankWithdrawMenu extends AetherMenu {
	use AwaitStdTrait;


	public function __construct(private AetherPlayer $player, private Session $bankSession, private Session $withdrawToSession, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("Bank Withdrawal");


		$glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");

		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, $glass);
		}

		$menu->getInventory()->setItem(19, $this->getWithdrawAll());
		$menu->getInventory()->setItem(21, $this->getWithdrawHalf());
		$menu->getInventory()->setItem(23, $this->getWithdrawTwenty());
		$menu->getInventory()->setItem(25, $this->getWithdrawCustom());

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

		if($slot === 19) {
			$p->removeCurrentWindow();
			BankUtils::withdraw($p, $this->bankSession->getBank(), $this->bankSession, $this->withdrawToSession);
		}

		if($slot === 21) {
			$p->removeCurrentWindow();
			BankUtils::withdraw($p, (int) floor($this->bankSession->getBank() * 0.5), $this->bankSession, $this->withdrawToSession);
		}

		if($slot === 23) {
			$p->removeCurrentWindow();
			BankUtils::withdraw($p, (int) floor($this->bankSession->getBank() * 0.2), $this->bankSession, $this->withdrawToSession);
		}

		if($slot === 25) {
			Await::f2c(function() use($p) {
				$p->removeCurrentWindow();

				yield $this->getStd()->sleep(20);

				$p->sendForm(new CustomForm("Bank Withdrawal", [new Input("Enter amount", "", "1000")], yield Await::RESOLVE_MULTI));
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

				BankUtils::withdraw($p, (int) floor($input), $this->bankSession, $this->withdrawToSession);
			});
		}
	}

	public function getWithdrawAll(): Item {
		$item = BlockFactory::getInstance()->get(BlockLegacyIds::DISPENSER, 0)->asItem()->setCount(64);
		$item->setCustomName("§r§l§a» Withdraw 100% «");
		$item->setLore([
			"§r§8Bank Withdraw",
			"§r",
			"§r§7Bank Balance: " . number_format($this->bankSession->getBank()),
			"§r§7Amount to withdraw: " . number_format($this->bankSession->getBank()),
			"§r",
			"§r§eClick to withdraw coins"
		]);

		return $item;
	}

	public function getWithdrawHalf(): Item {
		$item = BlockFactory::getInstance()->get(BlockLegacyIds::DISPENSER, 0)->asItem()->setCount(32);
		$item->setCustomName("§r§l§a» Withdraw 50% «");
		$item->setLore([
			"§r§8Bank Withdrawal",
			"§r",
			"§r§7Bank Balance: " . number_format($this->bankSession->getBank()),
			"§r§7Amount to withdraw: " . number_format($this->bankSession->getBank() * 0.5),
			"§r",
			"§r§eClick to withdraw coins"
		]);

		return $item;
	}

	public function getWithdrawTwenty(): Item {
		$item = BlockFactory::getInstance()->get(BlockLegacyIds::DISPENSER, 0)->asItem()->setCount(1);
		$item->setCustomName("§r§l§a» Withdraw 20% «");
		$item->setLore([
			"§r§8Bank Withdrawal",
			"§r",
			"§r§7Bank Balance: " . number_format($this->bankSession->getBank()),
			"§r§7Amount to withdraw: " . number_format($this->bankSession->getBank() * 0.2),
			"§r",
			"§r§eClick to withdraw coins"
		]);

		return $item;
	}


	public function getWithdrawCustom(): Item {
		$item = VanillaBlocks::OAK_SIGN()->asItem()->setCount(1);
		$item->setCustomName("§r§l§a» Specific Amount «");
		$item->setLore([
			"§r§8Bank Withdrawal",
			"§r",
			"§r§7Bank Balance: " . number_format($this->bankSession->getBank()),
			"§r",
			"§r§eClick to withdraw coins"
		]);

		return $item;
	}
}