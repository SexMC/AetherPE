<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\bounty\BountyAddSubCommand;
use skyblock\commands\basic\sub\bounty\BountyInfoSubCommand;
use skyblock\Database;
use skyblock\forms\commands\bounty\BountyForm;
use skyblock\utils\Queries;
use SOFe\AwaitGenerator\Await;

class BountyCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Bounties");

		$this->registerSubCommand(new BountyAddSubCommand("add"));
		$this->registerSubCommand(new BountyInfoSubCommand("info"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			Await::f2c(function() use ($sender){
				$data = yield Database::getInstance()->getLibasynql()->asyncSelect(Queries::BOUNTY_CURRENT);
				$sender->sendForm(new BountyForm($data));
			});
		}
	}
}