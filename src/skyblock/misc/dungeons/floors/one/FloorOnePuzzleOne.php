<?php

declare(strict_types=1);

namespace skyblock\misc\dungeons\floors\one;

use Generator;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\event\EventPriority;
use pocketmine\math\Vector3;
use pocketmine\world\particle\BlockBreakParticle;
use skyblock\events\block\LeverPullEvent;
use skyblock\misc\dungeons\DungeonInstance;
use skyblock\misc\dungeons\DungeonPuzzle;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\EntityUtils;

class FloorOnePuzzleOne extends DungeonPuzzle {

	use AwaitStdTrait;

	private array $coords = [];

	private Vector3 $leverPos;


	public function start(DungeonInstance $instance): Generator {
		$world = $instance->getWorld();
		$e = EntityUtils::spawnTextEntity(Location::fromObject($this->leverPos->add(0.5, 0.5, 0.5), $world), "§r§cPress this lever to continue.", 60 * 60 * 2);


		$block = VanillaBlocks::REDSTONE_LAMP()->setPowered(true);
		foreach($this->coords as $coord){
			$world->setBlock($coord, $block);
			$world->addParticle($coord, new BlockBreakParticle($block));
			yield $this->getStd()->sleep(1);
		}

		/** @var LeverPullEvent $event */
		$event = yield $this->getStd()->awaitEvent(
			LeverPullEvent::class,
			fn(LeverPullEvent $event) => ($instance->getWorld()->getFolderName() === $event->getBlock()->getPosition()->getWorld()->getFolderName() && $instance->isParticipant($event->getPlayer())),
			true,
			EventPriority::LOW,
			true
		);

		$player = $event->getPlayer();
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§c{$player->getName()} §7solved the §cFirst Puzzle§7.");

		foreach($this->coords as $coord){
			$world->setBlock($coord, VanillaBlocks::AIR());
			$world->addParticle($coord, new BlockBreakParticle($block));
		}


		$e->flagForDespawn();

		return $player->getName();
	}
	
	public function setup(): void {
		$this->leverPos = new Vector3(29, 193, -38);
		
		$this->addCoords(-7, 177, -43);
		$this->addCoords(-3,179,-39);
		$this->addCoords(0,180,-39);
		$this->addCoords(6,181,-38);
		$this->addCoords(10,182,-38);
		$this->addCoords(14,183,-38);
		$this->addCoords(17,184,-37);
		$this->addCoords(26,186,-37);
	}
	
	public function addCoords(int $x, int $y, int $z): void {
		$this->coords[] = new Vector3($x, $y, $z);
	}

	public function getName() : string{
		return "Floor 1 Puzzle 1";
	}
}