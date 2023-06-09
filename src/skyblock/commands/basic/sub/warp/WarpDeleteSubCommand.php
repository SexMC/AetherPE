<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\warp;

use CortexPE\Commando\args\RawStringArgument;
use SOFe\AwaitGenerator\Await;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\warps\Warp;
use skyblock\misc\warps\WarpHandler;

class WarpDeleteSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.warp.delete");
		$this->registerArgument(0, new RawStringArgument("name"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			Await::f2c(function() use($sender, $args){
				$name = $args["name"];
				$warps = yield WarpHandler::getInstance()->getAllWarps();

				/** @var Warp $selected */
				$selected = $warps[$name] ?? null;
				if($selected === null){
					$sender->sendMessage(Main::PREFIX . "No warp named §c$name §7was found");
					return;
				}

				$selected->delete();
				$sender->sendMessage(Main::PREFIX . "Deleted warp §c" . $selected->name);
			});
		}
	}
}