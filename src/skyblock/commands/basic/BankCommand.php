<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\PlayerArgument;
use skyblock\Main;
use skyblock\menus\bank\BankMenu;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\BankUtils;
use slapper\SlapperCommandSender;

class BankCommand extends AetherCommand {
	use PlayerCooldownTrait;

	protected function prepare() : void{
		$this->setDescription("bank");
		$this->setPermission("skyblock.command.bank");

		$this->registerArgument(0, new PlayerArgument("player", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$p = null;


		if($sender instanceof SlapperCommandSender){
			if(isset($args["player"])){
				$p = $args["player"];
			}
		}

		/*if($sender instanceof AetherPlayer){
			$p = $sender;
		}*/

		if($p instanceof AetherPlayer){
			if($this->isOnCooldown($p)) return;

			$this->setCooldown($p, 1);
			
			$amount = BankUtils::checkForInterest($p->getCurrentProfile()->getProfileSession());
			
			if($amount !== null){
				$format = number_format($amount);
				$p->getCurrentProfile()->announce(Main::PREFIX . "You have just received §6$format coins §7as §binterest§7 in your profile's bank account!");
			}

			(new BankMenu($p))->send($p);

		}
	}
}