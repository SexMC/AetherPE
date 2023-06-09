<?php

declare(strict_types=1);

namespace skyblock\commands\staff\sub;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\args\Vector3Argument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use poggit\libasynql\SqlError;
use skyblock\caches\pvpzones\PvpZone;
use skyblock\caches\pvpzones\PvpZonesCache;
use skyblock\commands\AetherSubCommand;
use skyblock\Database;
use skyblock\Main;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Queries;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class PvpZoneDeleteSubCommand extends AetherSubCommand {

	protected function prepare() : void{
		$this->setPermission("skyblock.command.pvpzone");

		$this->registerArgument(0, new RawStringArgument("name"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender instanceof Player) return;

		$name = strtolower($args["name"]);
		$zone = PvpZonesCache::getInstance()->getZoneByName($name);

		if($zone === null){
			$sender->sendMessage(Main::PREFIX . "$zone does not exists");
			return;
		}

		Database::getInstance()->getLibasynql()->executeChange(Queries::SAFEZONES_DELETE, [
			"name" => $name,
		], function(int $affectedRows) use($sender, $name): void {
			Await::f2c(function(){
				yield PvpZonesCache::getInstance()->updateSafezoneCache();
			});
			$sender->sendMessage(Main::PREFIX . "Pvp Zone $name has been successfully delete");
		}, function(SqlError $error) use($sender, $name): void {
			var_dump($error);
			$sender->sendMessage(Main::PREFIX . "Failed to delete pvp zone named $name");
		});
	}
}