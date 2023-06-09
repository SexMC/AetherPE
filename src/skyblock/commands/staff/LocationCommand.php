<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\communication\packets\types\player\FindPlayerLocationRequestPacket;
use skyblock\communication\packets\types\player\FindPlayerLocationResponsePacket;
use skyblock\Main;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class LocationCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.location");
		$this->setDescription("Find a players location");

		$this->registerArgument(0, new RawStringArgument("player"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$username = $args["player"];

		if(Utils::isOnline($username)){
			Await::f2c(function() use($sender, $username){
				$start = microtime(true);
				Main::getInstance()->getCommunicationLogicHandler()->sendPacket(new FindPlayerLocationRequestPacket($username, yield Await::RESOLVE));
				$data = yield Await::ONCE;
				$end = microtime(true);
				$s = $end - $start;
				var_dump("took $s ms");


				if($data instanceof FindPlayerLocationResponsePacket){
					$sender->sendMessage(Main::PREFIX . "§c$username §7is on §c" . $data->server);
				}
			});
		} else $sender->sendMessage(Main::PREFIX . "No online player named $username was found");
	}
}