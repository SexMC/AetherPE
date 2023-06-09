<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\island;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use skyblock\commands\AetherSubCommand;
use skyblock\forms\island\admin\IslandAdminForm;
use skyblock\forms\island\management\IslandManageForm;
use skyblock\islands\Island;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\quest\HeroicQuestToken;
use skyblock\items\special\types\quest\QuestToken;
use skyblock\logs\LogHandler;
use skyblock\logs\types\BankLog;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class IslandAdminSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.islandadmin");

		$this->setDescription("Island Admin");
		$this->registerArgument(0, new RawStringArgument("island"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			$island = new Island($args["island"]);

			if(!$island->exists()){
				$sender->sendMessage(Main::PREFIX . "No island found named §c" . $args["island"]);
				return;
			}

			$sender->sendForm(new IslandAdminForm($island));
		}
	}
}