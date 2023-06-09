<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\server\ExecuteCommandPacket;
use skyblock\Main;
use skyblock\misc\shop\Shop;
use skyblock\utils\Utils;

class SlotbotAllSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("Sell the items in your inventory");
		$this->setPermission("skyblock.command.slotbotall");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			CommunicationLogicHandler::getInstance()->sendPacket(new ExecuteCommandPacket(
				array_map(fn(string $p) => "specialitem \"$p\" 1 slotbotticket", Utils::getOnlinePlayerUsernames())
			));
			Utils::announce(Main::PREFIX . "§c{$sender->getName()}§7 has done a §cslot bot ticket§7 all.");
		}
	}
}