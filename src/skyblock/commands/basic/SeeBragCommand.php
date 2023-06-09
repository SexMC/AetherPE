<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\operations\mechanics\BragGetOperation;
use skyblock\communication\packets\types\mechanics\brag\BragResponsePacket;
use skyblock\communication\packets\types\mechanics\brag\GetBragPacket;
use skyblock\Main;
use skyblock\menus\commands\BragMenu;
use SOFe\AwaitGenerator\Await;

class SeeBragCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			Await::f2c(function() use($args, $sender){
				CommunicationLogicHandler::getInstance()->sendPacket(new GetBragPacket(
					strtolower($args["player"]),
					yield Await::RESOLVE
				));

				/** @var BragResponsePacket $data */
				$data = yield Await::ONCE;
				if($data->player === "not_found"){
					$sender->sendMessage(Main::PREFIX . "No brag found of a player named §c" . $args["player"]);
					return;
				}

				$menu = new BragMenu($data->player . "'s Inventory", $data->inventory, $data->armorInventory);
				$menu->send($sender);
			});
		}
	}
}