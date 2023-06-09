<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\bounty;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\forms\commands\bounty\BountyInfoForm;
use skyblock\Main;
use skyblock\misc\bounty\BountyData;
use skyblock\misc\bounty\BountyHandler;
use skyblock\sessions\Session;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class BountyInfoSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;
		$target = $args["player"] ?? $sender->getName();


		Await::f2c(function() use($sender, $target) {
			$s = new Session($target);

			if(!$s->playerExists()){
				$sender->sendMessage(Main::PREFIX . "No player named §c$target §7was found");
				return;
			}

			/** @var BountyData $data */
			$data = yield BountyHandler::getInstance()->getBountyData($target);
			$sender->sendForm(new BountyInfoForm($data));
		});
	}
}