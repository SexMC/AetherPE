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

class PvpZoneCreateSubCommand extends AetherSubCommand {

	protected function prepare() : void{
		$this->setPermission("skyblock.command.pvpzone");

		$this->registerArgument(0, new RawStringArgument("name"));
		$this->registerArgument(1, new Vector3Argument("pos1"));
		$this->registerArgument(2, new Vector3Argument("pos2"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender instanceof Player) return;

		$name = strtolower($args["name"]);
		$pos1 = $args["pos1"];
		$pos2 = $args["pos2"];
		$world = $sender->getWorld()->getDisplayName();

		$zone = new PvpZone($name, strtolower($world), $pos1, $pos2);
		Database::getInstance()->getLibasynql()->executeInsert(Queries::SAFEZONES_UPDATE, [
			"name" => $name,
			"world" => strtolower($world),
			"pos1" => json_encode(PvpZone::jsonSerializeVector($pos1)),
			"pos2" => json_encode(PvpZone::jsonSerializeVector($pos2)),
		], function(int $insertId, int $affectedRows) use($sender, $name): void {
			Await::f2c(function(){
				yield PvpZonesCache::getInstance()->updateSafezoneCache();
			});

			$sender->sendMessage(Main::PREFIX . "Pvp Zone $name has been successfully created");
		}, function(SqlError $error) use($sender, $name): void {
			var_dump($error);
			$sender->sendMessage(Main::PREFIX . "Failed to create pvp zone named $name");
		});
	}
}