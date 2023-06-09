<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\warp\WarpCreateSubCommand;
use skyblock\commands\basic\sub\warp\WarpDeleteSubCommand;
use skyblock\commands\basic\sub\warp\WarpOpenSubCommand;
use skyblock\forms\commands\WarpForm;
use skyblock\misc\warps\WarpHandler;
use SOFe\AwaitGenerator\Await;

class WarpCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View Warps");
		$this->registerSubCommand(new WarpCreateSubCommand("create"));
		$this->registerSubCommand(new WarpDeleteSubCommand("delete"));
		$this->registerSubCommand(new WarpOpenSubCommand("open"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			Await::f2c(function() use($sender){
				$sender->sendForm(new WarpForm(yield WarpHandler::getInstance()->getAllWarps()));
			});
		}
	}
}