<?php

declare(strict_types=1);

namespace skyblock\commands\economy\sub;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\menus\commands\EssenceShop;
use skyblock\sessions\Session;

class EssenceShopSubCommand extends AetherSubCommand{
	protected function prepare() : void{
		$this->setDescription("Open the essence shop");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			(new EssenceShop())->send($sender);
		}
	}
}