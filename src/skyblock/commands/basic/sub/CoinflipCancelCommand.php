<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipGetOperation;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipGetRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipRemoveOperation;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipRemoveRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipRemoveResponsePacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipStartOperation;
use skyblock\Main;
use skyblock\sessions\Session;
use SOFe\AwaitGenerator\Await;

class CoinflipCancelCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("Cancel your coinflips");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			Await::f2c(function() use($sender){
				CommunicationLogicHandler::getInstance()->sendPacket(new CoinflipRemoveRequestPacket($sender->getName(), yield Await::RESOLVE));
				/** @var CoinflipRemoveResponsePacket $data */
				$data = yield Await::ONCE;

				if($data->success === true){
					(new Session($sender))->increasePurse($data->amount);
					$sender->sendMessage(Main::PREFIX . "Successfully cancelled your coinflip");
					return;
				}

				$sender->sendMessage(Main::PREFIX . "No cancellable coin flip found");
			});
		}
	}
}