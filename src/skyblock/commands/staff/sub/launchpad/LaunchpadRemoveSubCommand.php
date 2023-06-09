<?php

declare(strict_types=1);

namespace skyblock\commands\staff\sub\launchpad;

use CortexPE\Commando\args\RawStringArgument;
use Generator;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\EventPriority;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\commands\arguments\DefinedStringArgument;
use skyblock\Main;
use skyblock\misc\launchpads\Launchpad;
use skyblock\misc\launchpads\LaunchpadHandler;
use skyblock\misc\pve\zone\CombatZone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\player\AetherPlayer;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class LaunchpadRemoveSubCommand extends AetherSubCommand {
	use AwaitStdTrait;

	protected function prepare() : void{
		$this->setPermission("skyblock.command.launchpad");

		$this->registerArgument(0, new RawStringArgument("name"));
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		LaunchpadHandler::getInstance()->removePad($args["name"]);

		$player->sendMessage(Main::PREFIX . "Removed launch pad named §c" . $args["name"]);
	}
}