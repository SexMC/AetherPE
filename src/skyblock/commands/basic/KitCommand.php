<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\KitResetCommand;
use skyblock\Database;
use skyblock\forms\commands\KitsForm;
use skyblock\menus\commands\KitsMenu;
use skyblock\misc\kits\KitHandler;
use SOFe\AwaitGenerator\Await;

class KitCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("Claim your kits");
		$this->registerSubCommand(new KitResetCommand("reset"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			KitsMenu::create($sender);
		}
	}
}