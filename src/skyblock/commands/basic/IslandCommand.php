<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\island\IslandAdminSubCommand;
use skyblock\commands\basic\sub\island\IslandChatSubCommand;
use skyblock\commands\basic\sub\island\IslandDepositSubCommand;
use skyblock\commands\basic\sub\island\IslandEssenceDepositSubCommand;
use skyblock\commands\basic\sub\island\IslandGoSubCommand;
use skyblock\commands\basic\sub\island\IslandInfoSubCommand;
use skyblock\commands\basic\sub\island\IslandTopSubArgument;
use skyblock\commands\basic\sub\island\IslandUnlockablesSubCommand;
use skyblock\commands\basic\sub\island\IslandUpgradesSubCommand;
use skyblock\entity\MaskEntitity;
use skyblock\forms\island\IslandForm;
use skyblock\sessions\Session;

class IslandCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("Islands");

		$this->registerSubCommand(new IslandDepositSubCommand("deposit"));
		$this->registerSubCommand(new IslandInfoSubCommand("info", ["who", "bank"]));
		$this->registerSubCommand(new IslandGoSubCommand("go", ["h", "home", "g"]));
		$this->registerSubCommand(new IslandUpgradesSubCommand("upgrade", ["u", "upgrades"]));
		$this->registerSubCommand(new IslandUnlockablesSubCommand("unlockables", ["unlockable", "unlock"]));
		$this->registerSubCommand(new IslandTopSubArgument("top", ["t"]));
		$this->registerSubCommand(new IslandAdminSubCommand("admin"));
		$this->registerSubCommand(new IslandChatSubCommand("chat", ["c"]));
		$this->registerSubCommand(new IslandEssenceDepositSubCommand("essencedeposit", ["edeposit"]));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$sender->sendForm(new IslandForm(new Session($sender->getName())));
		}
	}
}