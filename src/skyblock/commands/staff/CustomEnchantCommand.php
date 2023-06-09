<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\CustomEnchantArgument;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\ICustomEnchantable;
use skyblock\Main;

class CustomEnchantCommand extends AetherCommand {


	protected function prepare() : void{
		$this->registerArgument(0, new CustomEnchantArgument("customenchant"));
		$this->registerArgument(1, new IntegerArgument("level"));
		$this->setPermission("skyblock.command.customenchant");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;

		$item = $sender->getInventory()->getItemInHand();

		if(!$item instanceof ICustomEnchantable){
			$sender->sendMessage(Main::PREFIX . "§cPlease hold a valid item");
			return;
		}

		$level = $args["level"];
		$ce = $args["customenchant"];

		$item->addCustomEnchant(new CustomEnchantInstance($ce, $level));
		//ItemEditor::addCustomEnchantment($item, new CustomEnchantInstance($ce, $level));
		$sender->getInventory()->setItemInHand($item);
		$sender->sendMessage(Main::PREFIX . "Added §c{$ce->getIdentifier()->getName()} {$level} §7to the item");
	}
}