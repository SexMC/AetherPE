<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\operations\mechanics\BragGetOperation;
use skyblock\communication\operations\mechanics\SeeItemGetOperation;
use skyblock\communication\packets\types\mechanics\item\GetItemPacket;
use skyblock\communication\packets\types\mechanics\item\ItemResponsePacket;
use skyblock\Main;
use skyblock\menus\commands\SeeItemMenu;
use SOFe\AwaitGenerator\Await;

class SeeItemCommand extends AetherCommand {

	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			Await::f2c(function() use($args, $sender){
				CommunicationLogicHandler::getInstance()->sendPacket(new GetItemPacket(strtolower($args["player"]), yield Await::RESOLVE));
				/** @var ItemResponsePacket $data */
				$data = yield Await::ONCE;

				if($data->player === "not_found"){
					$sender->sendMessage(Main::PREFIX . "No item found of a player named §c{$args["player"]}");
					return;
				}

				$menu = new SeeItemMenu($args["player"] . "'s Item", $data->item);
				$menu->send($sender);

			});
		}
	}
}