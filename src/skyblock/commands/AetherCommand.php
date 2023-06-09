<?php

declare(strict_types=1);

namespace skyblock\commands;


use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;
use pocketmine\timings\Timings;
use pocketmine\utils\TextFormat;
use skyblock\caches\combat\CombatCache;
use skyblock\Main;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\Utils;
use slapper\SlapperCommandSender;

abstract class AetherCommand extends BaseCommand {

	protected Main $plugin;

	private bool $canBeUsedInCombat = false;

	public function __construct(string $name, array $aliases = []){
		parent::__construct($this->plugin = Main::getInstance(), $name, "", $aliases);
	}

	public function execute(CommandSender $sender, string $usedAlias, array $args){
		Timings::$playerCommand->startTiming();


		if($this->getPermission() !== null && !$sender->getServer()->isOp($sender->getName()) && !$sender instanceof ConsoleCommandSender && !$sender instanceof SlapperCommandSender){
			if(!$sender->hasPermission($this->getPermission())){
				$sender->sendMessage(Main::PREFIX . "§cYou don't have permissions to run this command!");
				return;
			}
		}

		if($sender instanceof AetherPlayer){
			Utils::checkForAHDupedItems($sender);


			$ignoreCombatCheck = false;

			if(count($args) > 0){
				if(isset($this->getSubCommands()[$args[0]])){
					$cmd = $this->getSubCommands()[$args[0]];
					if($cmd instanceof AetherSubCommand) {
						if(!$cmd->canBeUsedInCombat() && CombatCache::getInstance()->isInCombat($sender)) {
							$sender->sendMessage(Main::PREFIX . "You cannot execute this command in combat.");
							return;
						} else $ignoreCombatCheck = true;
					}
				}
			}

			if(!$ignoreCombatCheck){
				if(!$this->canBeUsedInCombat() && CombatCache::getInstance()->isInCombat($sender)){
					$sender->sendMessage(Main::PREFIX . "You cannot execute this command in combat.");
					return;
				}
			}



			QuestHandler::getInstance()->increaseProgress(IQuest::COMMAND, $sender, new Session($sender), $usedAlias);
		}


		parent::execute($sender, $this->getName(), $args);

		Timings::$playerCommand->stopTiming();

	}

	public function testPermission(CommandSender $target, ?string $permission = null) : bool{
		if($target instanceof ConsoleCommandSender){
			return true;
		}

		return parent::testPermission($target, $permission);
	}


	public function getOwningPlugin() : Plugin{
		return $this->plugin;
	}

	public function setPermission(?string $permission) : void{
		if($permission !== null){
			$manager = PermissionManager::getInstance();

			if($manager->getPermission($permission) === null){
				$manager->addPermission(new Permission($permission));
			}
		}
		parent::setPermission($permission);
	}

	public function setCanBeUsedInCombat(bool $canBeUsedInCombat) : void{
		$this->canBeUsedInCombat = $canBeUsedInCombat;
	}

	public function canBeUsedInCombat() : bool{
		return $this->canBeUsedInCombat;
	}
}