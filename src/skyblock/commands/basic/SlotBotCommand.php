<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\SlotbotAllSubCommand;
use skyblock\items\lootbox\types\JealousYetLootbox;
use skyblock\items\lootbox\types\store\April2022AetherCrate;
use skyblock\items\lootbox\types\store\May2022AetherCrate;
use skyblock\Main;
use skyblock\menus\slotbot\SlotBotMenu;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class SlotBotCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Slot bot");
		
		$this->registerSubCommand(new SlotbotAllSubCommand("all"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;

		if(!Utils::isHubServer()){
			$sender->sendMessage(Main::PREFIX . "This command can only be used at spawn");
			return;
		}

		$session = new Session($sender);
		$lb = $session->getWeeklyLootbox(May2022AetherCrate::getName());
		(new SlotBotMenu($session, $lb))->send($sender);
	}
}