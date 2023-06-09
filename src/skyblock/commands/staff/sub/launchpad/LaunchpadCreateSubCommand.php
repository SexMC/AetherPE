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
use skyblock\Main;
use skyblock\misc\launchpads\Launchpad;
use skyblock\misc\launchpads\LaunchpadHandler;
use skyblock\misc\pve\zone\CombatZone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class LaunchpadCreateSubCommand extends AetherSubCommand {
	use AwaitStdTrait;

	protected function prepare() : void{
		$this->setPermission("skyblock.command.launchpad");

		$this->registerArgument(0, new RawStringArgument("name"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;

		Await::f2c(function() use ($sender, $args) {
			$sender->sendMessage(Main::PREFIX . "Select the from position by breaking a block.");
			$pos1 = yield $this->getPosition($sender);
			$sender->sendMessage(Main::PREFIX . "Select the to position by breaking a block.");
			$pos2 = yield $this->getPosition($sender);


			$pad = new Launchpad($args["name"], $pos1, $pos2);
			LaunchpadHandler::getInstance()->addLaunchpad($pad);
			$sender->sendMessage(Main::PREFIX . "Launchpad §c" .  $args["name"] .  "§7 created");
		});
	}

	public function getPosition(Player $sender): Generator {
		return (yield $this->getStd()->awaitEvent(BlockBreakEvent::class, fn(BlockBreakEvent $event) => $event->getPlayer()->getName() === $sender->getName(), true, EventPriority::NORMAL, true))->getBlock()->getPosition();
	}
}