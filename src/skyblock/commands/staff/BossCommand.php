<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\entity\boss\JosephBoss;
use skyblock\Main;
use skyblock\menus\commands\ArmorInvseeMenu;
use skyblock\menus\commands\EnderInvseeMenu;
use skyblock\menus\commands\InvseeMenu;
use skyblock\sessions\Session;
use skyblock\utils\EntityUtils;
use skyblock\utils\Utils;

class BossCommand extends AetherCommand {


	protected function prepare() : void{
		$this->setPermission("skyblock.command.bossspawn");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player) {
			$v = $sender->getPosition();
			$skin = new Skin("humanoid.custom", EntityUtils::getSkinAsRaw("skins/zombie_boss.png"));

			/** @var Vector3 $v */
			(new JosephBoss(Location::fromObject($v, $sender->getWorld()), $skin))->spawnToAll();
			Utils::announce("\n§r§l§cBOSS§r§c Joseph, Crowned King §r§7has spawned at nether\n§r§7Coordinates: §c" . $v->getFloorX() . " " . $v->getFloorY() . " " . $v->getFloorZ() . "\n\n");

		}
	}
}