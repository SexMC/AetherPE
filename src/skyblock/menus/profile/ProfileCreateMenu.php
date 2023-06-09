<?php

declare(strict_types=1);

namespace skyblock\menus\profile;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\ProfileUtils;
use skyblock\utils\TimeUtils;

class ProfileCreateMenu extends AetherMenu {
	use AwaitStdTrait;


	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("Profile Create");

		$glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");
		for($i = 0; $i <= 53; $i++){

			$menu->getInventory()->setItem($i, $glass);
		}
		

		$this->getMenu()->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));
		$this->getMenu()->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock Menu"]));

		$this->getMenu()->getInventory()->setItem(22, $this->getCreateItem());



		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();

		assert($player instanceof AetherPlayer);

		if($slot === 22){
			$player->removeCurrentWindow();

			$t = (new Session($player))->getLastProfileSwitchUnix();
			if(time() - $t <= 120){
				$player->sendMessage(Main::PREFIX . "You must wait §c" . TimeUtils::getFormattedTime(120 - (time() - $t)) . " §7before doing this action again!");
				return;
			}
			
			$profile = ProfileUtils::createNewProfile($player->getName(), []);
			$player->addProfile($profile);
			(new Session($player))->setLastProfileSwitchUnix(time());

			$player->sendMessage(Main::PREFIX . "§r§7Creating your new profile...");

			ProfileUtils::switchProfile($player, $profile);
		}

		if($slot === 49){
			$player->removeCurrentWindow();
		}

		if($slot === 48){
			(new SkyblockMenu($player, $this))->send($player);
		}
	}

	public function getCreateItem(): Item {
		$item = VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GREEN())->asItem();
		$item->setCustomName("§r§l§a» Create new profile «");
		$item->setLore([
			"§r§7You are creating a new SkyBlock",
			"§r§7profile.",
			"§r§7",
			"§r§7You won't lose any progress.",
			"§r§7You can switch between profiles.",
			"§r§7",
			"§r§aUse /coop to play with friends",
			"§r§eClick to confirm new profile!"
		]);

		return $item;
	}
}