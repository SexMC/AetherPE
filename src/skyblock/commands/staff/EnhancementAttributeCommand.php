<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\EnhancementAttributeArgument;
use skyblock\commands\arguments\ItemModArgument;
use skyblock\items\itemattribute\EnhancementAttribute;
use skyblock\Main;

class EnhancementAttributeCommand extends AetherCommand {


	protected function prepare() : void{
		$this->registerArgument(0, new EnhancementAttributeArgument("enhancement"));
		$this->registerArgument(1, new IntegerArgument("level", true));
		$this->registerArgument(2, new IntegerArgument("chance", true));
		$this->registerArgument(3, new IntegerArgument("xp", true));

		$this->setPermission("skyblock.command.enhancementattribute");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			/** @var EnhancementAttribute $mod */
			$mod = $args["enhancement"];
			$sender->getInventory()->addItem($mod::getItem($mod->getUniqueId(), ($args["chance"] ?? mt_rand(1, 100)), ($args["level"] ?? 1), ($args["xp"] ?? 1)));
			$sender->sendMessage(Main::PREFIX . "Gave {$sender->getName()} a {$mod->getUniqueID()} enhancement attribute");
		}
	}
}