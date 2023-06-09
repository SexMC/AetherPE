<?php

declare(strict_types=1);

namespace skyblock\menus\profile;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\player\AetherPlayer;
use skyblock\profile\Profile;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\ProfileUtils;
use skyblock\utils\TimeUtils;
use SOFe\AwaitGenerator\Await;

class ProfileSwitchMenu extends AetherMenu {
	use AwaitStdTrait;


	public function __construct(private AetherPlayer $player, private Profile $profile, private ?AetherMenu $aetherMenu){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("Profile Manage");

		$glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");
		for($i = 0; $i <= 53; $i++){

			$menu->getInventory()->setItem($i, $glass);
		}


		$this->getMenu()->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));
		$this->getMenu()->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock Menu"]));

		$this->getMenu()->getInventory()->setItem(20, $this->getSwitchItem());
		$this->getMenu()->getInventory()->setItem(24, $this->getLeaveItem());



		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();

		assert($player instanceof AetherPlayer);

		if($slot === 20){
			$t = (new Session($player))->getLastProfileSwitchUnix();
			if(time() - $t <= 120){
				$player->sendMessage(Main::PREFIX . "You must wait §c" . TimeUtils::getFormattedTime(120 - (time() - $t)) . " §7before doing this action again!");
				$player->removeCurrentWindow();
				return;
			}
			$player->removeCurrentWindow();
			$player->sendMessage(Main::PREFIX . "Switching profile..");
			ProfileUtils::switchProfile($player, $this->profile);
		}

		if($slot === 24){
			$player->removeCurrentWindow();


			$t = (new Session($player))->getLastProfileSwitchUnix();
			if(time() - $t <= 120){
				$player->sendMessage(Main::PREFIX . "You must wait §c" . TimeUtils::getFormattedTime(120 - (time() - $t)) . " §7before doing this action again!");
				return;
			}


			if(count($player->getProfileIds()) === 1){
				$player->sendMessage(Main::PREFIX . "You cannot delete this profile while you only have 1 profile");
				return;
			}

			$player->sendMessage(Main::PREFIX . "To confirm the profile leaving type: §c§lDELETE PROFILE CONFIRM");
			Await::f2c(function() use($player) {
				/** @var PlayerChatEvent $event */
				$event = yield $this->getStd()->awaitEvent(PlayerChatEvent::class, fn(PlayerChatEvent $event) => $event->getPlayer()->getId() === $player->getId(), true, EventPriority::LOW, true);

				if(str_contains(TextFormat::clean($event->getMessage()), "DELETE PROFILE CONFIRM")){
					$event->cancel();;
					$player->removeProfile($this->profile);
					$this->profile->removeCoop($player->getName());
					$player->sendMessage(Main::PREFIX . "Left your profile: §e" . $this->profile->getName());
					(new Session($player))->setLastProfileSwitchUnix(time());

				}
			});
		}

		if($slot === 49){
			$player->removeCurrentWindow();
		}

		if($slot === 48){
			(new SkyblockMenu($player, $this))->send($player);
		}
	}

	public function getSwitchItem(): Item {
		$item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN())->asItem();
		$item->setCustomName("§r§l§a» Switch to profile «");
		$item->setLore([
			"§r§7Teleports you to your island on",
			"§r§7another profile and loads your",
			"§r§7inventory, skills, collections",
			"§r§7and more...",
			"§r",
			"§r§7Current: §e" . ($this->player->getCurrentProfile()->getName()),
			"§r§7Switching to: §e" . ($this->profile->getName()),
			"§r",
			"§r§eClick to switch!",
		]);


		return $item;
	}

	public function getLeaveItem(): Item {
		$item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem();
		$item->setCustomName("§r§l§a» Leave profile «");
		$item->setLore([
			"§r§7Clear this profile slot by",
			"§r§7leaving this profile.",
			"§r§7",
			"§r§4§lWARNING: §r§cthis action",
			"§r§ccannot be reverted!",
			"§r§7",
			"§r§eClick to continue!"
		]);

		return $item;
	}
}