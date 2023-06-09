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

class PvpZoneListSubCommand extends AetherSubCommand {

	protected function prepare() : void{
		$this->setPermission("skyblock.command.pvpzone");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender instanceof Player) return;

		Await::f2c(function(){
			yield PvpZonesCache::getInstance()->updateSafezoneCache();
		});

		$sender->sendMessage(Main::PREFIX . "All pvp zones:");
		foreach(PvpZonesCache::getInstance()->getAllZonesByName() as $zone){
			$string = "§7world: §c{$zone->world} §7pos1: §c" . json_encode(PvpZone::jsonSerializeVector($zone->pos1)) . "§7  pos2: §c" . json_encode(PvpZone::jsonSerializeVector($zone->pos2));
			$sender->sendMessage("§c* §7$zone->name\n$string");
		}
	}
}