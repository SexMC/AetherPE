<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\SellHandSubCommand;
use skyblock\commands\basic\sub\SellInventorySubCommand;

class SellCommand extends AetherCommand {

	private SellHandSubCommand $hand;

	protected function prepare() : void{
		$this->setDescription("Sell your items");

		$this->registerSubCommand($this->hand = new SellHandSubCommand("hand", ["h"],));
		$this->registerSubCommand(new SellInventorySubCommand("all", ["inventory", "inv"]));

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$this->hand->onRun($sender, $aliasUsed, $args);
		}
	}
}