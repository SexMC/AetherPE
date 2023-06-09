<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\DefinedStringArgument;
use skyblock\commands\arguments\MaskArgument;
use skyblock\commands\arguments\PetArgument;
use skyblock\items\masks\Mask;
use skyblock\items\pets\IPet;
use skyblock\items\pets\Pet;
use skyblock\Main;
use skyblock\utils\Utils;

class PetCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setPermission("skyblock.command.pet");
		$this->setDescription("Give pets");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new PetArgument("pet"));
		$this->registerArgument(2, new DefinedStringArgument([
			"uncommon" => IPet::RARITY_UNCOMMON,
			"common" => IPet::RARITY_COMMON,
			"rare" => IPet::RARITY_RARE,
			"epic" => IPet::RARITY_EPIC,
			"legendary" => IPet::RARITY_LEGENDARY
		], "rarity"));
		$this->registerArgument(3, new IntegerArgument("level", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$rarity = $args["rarity"];
			$lvl = $args["level"] ?? 1;

			/** @var Pet $pet */
			$pet = $args["pet"];
			if(!$pet instanceof Pet){
				$sender->sendMessage(Main::PREFIX . "error");
				return;
			}


			$player = Server::getInstance()->getPlayerExact($args["player"]);

			if($player === null){
				$sender->sendMessage(Main::PREFIX . "Invalid player");
				return;
			}

			$item = $pet->getItem($lvl, $rarity, 0, 0);

			$sender->sendMessage(Main::PREFIX . "Gave {$player->getName()} a {$item->getCustomName()} §l" . $pet->getColor($rarity) . $pet->getRarityName($rarity));
			$player->sendMessage(Main::PREFIX . "You have received a {$item->getCustomName()} §l" . $pet->getColor($rarity) . $pet->getRarityName($rarity));
			Utils::addItem($player, $item);
		}
	}
}