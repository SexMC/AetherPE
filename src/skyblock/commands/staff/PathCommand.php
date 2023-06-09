<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use Generator;

use lib\pathfinding\Pathfinder;
use pocketmine\command\CommandSender;
use pocketmine\entity\Living;
use pocketmine\entity\Zombie;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\EventPriority;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

class PathCommand extends AetherCommand {
	use AwaitStdTrait;
	
	protected function prepare() : void{
		$this->setPermission("skyblock.command.path");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		/*if(!$sender instanceof Player) return;
		Await::f2c(function() use($sender) {
			$initialPath = $sender->getPosition();
			$entity = $sender->getWorld()->getNearbyEntities($sender->getBoundingBox()->expandedCopy(50, 50, 50));

			foreach($entity as $e){
				if($e instanceof Zombie){
					$entity = $e;
					break;
				}
			}

			if($entity instanceof Living) {
				try {
					$start = Position::fromObject(
						(clone $initialPath)->subtract(0, -0.5, 0),
						$sender->getWorld()
					);
					$end = Position::fromObject(
						(clone $entity->getPosition())->subtract(0, -0.5, 0),
						$sender->getWorld()
					);
					$pathResult = Pathfinder::find($end, $start);
					$sender->sendMessage($pathResult->getDiagnose());

					if(!$pathResult->haveFailed()) {
						$nodesPath = $pathResult->getPath()->getNodes();
						$count = count($nodesPath);
						for($i = 0; $i < $count; $i++) {
							$node = $nodesPath[$i];

							$entity->teleport(new Vector3($node->x, $node->y, $node->z));
							yield $this->getStd()->sleep(10);
							$entity->getMotion()
						}
					}
				} catch (\Exception $e) {
					$sender->sendMessage('Error! Because, ' . $e->getMessage());
				}
			}
		});

		/*Await::f2c(function() use($sender) {
			$sender->sendMessage(Main::PREFIX . "Select first position");

			$pos1 = $this->getPosition($sender);

			$sender->sendMessage(Main::PREFIX . "Select second position");
			$pos2 = $this->getPosition($sender);

		});*/
	}
	
	public function getPosition(Player $sender): Generator {
		return yield $this->getStd()->awaitEvent(BlockBreakEvent::class, fn(BlockBreakEvent $event) => $event->getPlayer()->getName() === $sender->getName(), true, EventPriority::NORMAL, true);
	}
}