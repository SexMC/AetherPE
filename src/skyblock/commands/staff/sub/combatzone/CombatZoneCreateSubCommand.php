<?php

declare(strict_types=1);

namespace skyblock\commands\staff\sub\combatzone;

use CortexPE\Commando\args\RawStringArgument;
use Generator;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\EventPriority;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\pve\zone\CombatZone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

class CombatZoneCreateSubCommand extends AetherSubCommand {
	use AwaitStdTrait;

	protected function prepare() : void{
		$this->setPermission("skyblock.command.combatzone");

		$this->registerArgument(0, new RawStringArgument("name"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;

		Await::f2c(function() use ($sender, $args) {
			$sender->sendMessage(Main::PREFIX . "Select the first position by breaking a block.");
			$pos1 = yield $this->getPosition($sender);
			$sender->sendMessage(Main::PREFIX . "Select the second position by breaking a block.");
			$pos2 = yield $this->getPosition($sender);

			$minX = min($pos1->x, $pos2->x);
			$minY = min($pos1->y, $pos2->y);
			$minZ = min($pos1->z, $pos2->z);

			$maxX = max($pos1->x, $pos2->x);
			$maxY = max($pos1->y, $pos2->y);
			$maxZ = max($pos1->z, $pos2->z);

			$zone = new CombatZone($args["name"], new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ), []);

			ZoneHandler::getInstance()->addZone($zone);
			$sender->sendMessage(Main::PREFIX . "Zone §c" .  $args["name"] .  "§7 created");
		});
	}

	public function getPosition(Player $sender): Generator {
		return (yield $this->getStd()->awaitEvent(BlockBreakEvent::class, fn(BlockBreakEvent $event) => $event->getPlayer()->getName() === $sender->getName(), true, EventPriority::NORMAL, true))->getBlock()->getPosition();
	}
}