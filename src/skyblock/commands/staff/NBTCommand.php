<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\forms\commands\nbt\NBTItemForm;
use skyblock\items\lootbox\types\TestAetherCrate;

class NBTCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setPermission("skyblock.command.nbt");

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player) {
			//(new AetherCrateAnimation(new TestAetherCrate(), $player))->send($player);

			$sender->sendForm(new NBTItemForm($sender->getInventory()->getItemInHand()));
		}
	}
}