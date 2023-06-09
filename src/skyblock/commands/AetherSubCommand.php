<?php

declare(strict_types=1);

namespace skyblock\commands;


use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use skyblock\caches\combat\CombatCache;
use skyblock\Main;

abstract class AetherSubCommand extends BaseSubCommand {

	protected Main $plugin;

	private bool $canBeUsedInCombat = false;

	protected string $desc;

	public function __construct(string $name, array $aliases = []){
		parent::__construct($name, "", $aliases);
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

	public function getDescription() : string{
		return $this->desc;
	}

	public function setDescription(string $desc): void {
		$this->desc = $desc;
	}
}