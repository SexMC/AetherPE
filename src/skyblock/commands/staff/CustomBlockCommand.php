<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\blocks\custom\CustomBlock;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\CustomBlockArgument;
use skyblock\commands\arguments\MaskArgument;
use skyblock\commands\arguments\PetArgument;
use skyblock\items\masks\Mask;
use skyblock\items\pets\Pet;
use skyblock\Main;
use skyblock\utils\Utils;

class CustomBlockCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setPermission("skyblock.command.custom_blocks");
		$this->setDescription("Give custom blocks");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new CustomBlockArgument("block"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			/** @var CustomBlock $pet */
			$pet = $args["block"];
			if(!$pet instanceof CustomBlock){
				$sender->sendMessage(Main::PREFIX . "error");
				return;
			}


			$player = Server::getInstance()->getPlayerExact($args["player"]);

			if($player === null){
				$sender->sendMessage(Main::PREFIX . "Invalid player");
				return;
			}

			$item = $pet::getItem();

			$sender->sendMessage(Main::PREFIX . "Gave {$player->getName()} a {$item->getCustomName()}");
			$player->sendMessage(Main::PREFIX . "You have received a {$item->getCustomName()}");
			Utils::addItem($player, $item);
		}
	}
}