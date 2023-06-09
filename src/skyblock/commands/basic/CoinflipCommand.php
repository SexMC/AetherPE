<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\CoinflipCancelCommand;
use skyblock\forms\commands\coinflip\CoinflipForm;
use skyblock\forms\commands\coinflip\CoinflipSelectColorForm;
use skyblock\Main;
use skyblock\misc\coinflip\CoinflipHandler;
use skyblock\misc\warpspeed\IWarpSpeed;
use skyblock\misc\warpspeed\WarpSpeedHandler;
use SOFe\AwaitGenerator\Await;

class CoinflipCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("Gamble with money");

		$this->registerArgument(0, new IntegerArgument("amount", true));
		$this->registerSubCommand(new CoinflipCancelCommand("cancel"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			if(!WarpSpeedHandler::getInstance()->isUnlocked(IWarpSpeed::COINFLIP)){
				WarpSpeedHandler::getInstance()->sendMessage($sender);
				return;
			}

			$amount = $args["amount"] ?? null;

			if($amount === null){
				Await::f2c(function() use($sender){
					$sender->sendForm(new CoinflipForm($sender, yield CoinflipHandler::getInstance()->getAllCoinflips()));
				});

				return;
			}

			if($amount < 1000){
				$sender->sendMessage(Main::PREFIX . "Amount must be greater than 1,000");
				return;
			}

			if($amount > 2000000000){
				$sender->sendMessage(Main::PREFIX . "Amount must be smaller than 2,000,000,000");
				return;
			}

			Await::f2c(function() use($sender, $amount){
				$data = yield CoinflipHandler::getInstance()->getAllCoinflips();

				foreach($data as $k => $v){
					if(strtolower($v->getPlayer()) === strtolower($sender->getName())){
						$sender->sendMessage(Main::PREFIX . "You already have submitted a coin flip.");

						return;
					}
				}

				$sender->sendForm(new CoinflipSelectColorForm(null, $amount));
			});
		}
	}
}
