<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\commands\arguments\KitArgument;
use skyblock\Main;
use skyblock\misc\kits\Kit;
use skyblock\misc\kits\KitHandler;
use SOFe\AwaitGenerator\Await;

class KitResetCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("Reset a players kit cooldown");
		$this->setPermission("skyblock.command.kitreset");
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new KitArgument("kit"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		Await::f2c(function() use ($args, $sender){
			$p = $args["player"];
			/** @var Kit $kit */
			$kit = $args["kit"];

			$data = yield KitHandler::getInstance()->getCooldownData($p);

			if(isset($data[$kit->getName()])){
				if($kit->getCooldown() -  (time() - $data[$kit->getName()]) > 0){
					unset($data[$kit->getName()]);
					$success = yield KitHandler::getInstance()->setCooldownData($p, $data);

					if($success){
						$sender->sendMessage(Main::PREFIX . "successfully reset $p's " . $kit->getName() . "§r§7 cooldown");
						return;
					}
				}

			}

			$sender->sendMessage(Main::PREFIX . "$p does not have the kit " . $kit->getName() . "§r§7 on cooldown");
		});
	}
}