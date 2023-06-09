<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\bounty;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\bounty\BountyData;
use skyblock\misc\bounty\BountyHandler;
use skyblock\sessions\Session;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class BountyAddSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new IntegerArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;
		$amount = $args["amount"];
		$target = $args["player"];


		Await::f2c(function() use($sender, $amount, $target) {
			/** @var BountyData $data */
			$data = yield BountyHandler::getInstance()->getBountyData($target);


			$s = new Session($sender);

			if($s->getPurse() < $amount){
				$sender->sendMessage(Main::PREFIX . "You cannot bounty more money than you have");
				return;
			}

			if($amount < 1000){
				$sender->sendMessage(Main::PREFIX . "Bounty amount must be greater than 1.000");
				return;
			}

			$ss = new Session($target);
			if(!$ss->playerExists()){
				$sender->sendMessage(Main::PREFIX . "No player named §c$target §7was found");
				return;
			}

			$data->currentBounty += $amount;
			$data->lifeTimeBounty += $amount;

			if($data->currentBounty > $data->maxBounty){
				$data->maxBounty = $data->currentBounty;
			}

			BountyHandler::getInstance()->saveBountyData($data);

			$a = number_format($amount);
			$sender->sendMessage(Main::PREFIX . "Added a bounty of §c$" . $a . "§7 to §c$target");
			Utils::sendMessage($target, Main::PREFIX . "§c{$sender->getName()}§7 has added a bounty of §c$" . $a . "§7 to you.");
		});
	}
}