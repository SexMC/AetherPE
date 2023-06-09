<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\menus\commands\TradeMenu;
use skyblock\traits\StringArrayCache;

class TradeCommand extends AetherCommand {
	use StringArrayCache;

	protected function prepare() : void{
		$this->setDescription("Trade with nearby players");

		$this->registerArgument(0, new RawStringArgument("player"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$partner = Server::getInstance()->getPlayerByPrefix($args["player"]);

			if($partner === null){
				$sender->sendMessage(Main::PREFIX . "No player named §c" . $args["player"] . "§7 was found");
				return;
			}
			
			$distance = $partner->getLocation()->maxPlainDistance($sender->getLocation()->asVector3());

			if($partner->getLocation()->getWorld()->getDisplayName() !== $sender->getLocation()->getWorld()->getDisplayName()){
				$distance = 50;
			}

			if($partner->getName() === $sender->getName()){
				$sender->sendMessage(Main::PREFIX . "You cannot trade yourself");
				return;
			}


			if($distance > 10){
				$sender->sendMessage(Main::PREFIX . "{$args["player"]} is not 10 blocks nearby you");
				return;
			}


			if($this->exists($partner->getName())){
				$data = $this->get($partner->getName());
				if($data[0] === $sender->getName() && (time() - $data[1]) <= 30){
					$menu = new TradeMenu($partner, $sender);
					$menu->send($partner);
					$menu->send($sender);
					return;
				}
			}

			$this->set($sender->getName(), [$partner->getName(), time()]);
			$sender->sendMessage(Main::PREFIX . "You have sent a trade request to §c" . $partner->getName());
			$partner->sendMessage(Main::PREFIX . "You have received a trade request from §c" . $sender->getName());
		}
	}
}