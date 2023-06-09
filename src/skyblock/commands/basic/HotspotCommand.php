<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\forms\commands\HotspotForm;
use skyblock\Main;
use skyblock\misc\pve\fishing\HotspotHandler;
use skyblock\player\AetherPlayer;
use skyblock\utils\Utils;

class HotspotCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View hotspots");

		$this->registerArgument(0, new RawStringArgument("rotate", true));
	}


	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		/*if(!Utils::isWarpServer()) {
			$player->sendMessage(Main::PREFIX . "This command can only be used in the grinding world (/warp)");
			return;
		}*/


		if(Server::getInstance()->isOp($player->getName()) && isset($args["rotate"]) && $args["rotate"] !== null){
			HotspotHandler::getInstance()->rotate();
		}
		$player->sendForm(new HotspotForm());
	}
}