<?php

declare(strict_types=1);

namespace skyblock\misc\dungeons\floors\one;

use Generator;
use pocketmine\block\Chest;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\block\inventory\DoubleChestInventory;
use pocketmine\block\Ladder;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\event\EventPriority;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\math\Vector3;
use pocketmine\utils\InternetRequestResult;
use pocketmine\world\particle\BlockBreakParticle;
use skyblock\events\block\LeverPullEvent;
use skyblock\items\tools\types\pve\PigmanSword;
use skyblock\misc\dungeons\DungeonInstance;
use skyblock\misc\dungeons\DungeonPuzzle;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\EntityUtils;

class FloorOnePuzzleTwo extends DungeonPuzzle {
	use AwaitStdTrait;
	private array $laddercoords = [];
	private array $coords = [];

	private Vector3 $chestPos;

	public function start(DungeonInstance $instance): Generator {
		$world = $instance->getWorld();
		$e = EntityUtils::spawnTextEntity(Location::fromObject($this->chestPos->add(0.5, 0.5, 0.5), $world), "§r§cOpen this chest to continue.", 60 * 60 * 2);


		$block = VanillaBlocks::REDSTONE_LAMP()->setPowered(true);
		foreach($this->coords as $coord){
			$world->setBlock($coord, $block);
			$world->addParticle($coord, new BlockBreakParticle($block));
			yield $this->getStd()->sleep(1);
		}

		$block = VanillaBlocks::LADDER();
		foreach($this->laddercoords as $v){
			$coord = $v[0];
			$block->setFacing($v[1]);

			$world->setBlock($coord, $block);
			$world->addParticle($coord, new BlockBreakParticle($block));
			yield $this->getStd()->sleep(1);
		}

		/** @var LeverPullEvent $event */
		$event = yield $this->getStd()->awaitEvent(
			InventoryOpenEvent::class,
			fn(InventoryOpenEvent $event) => (
				$event->getPlayer()->getWorld()->getFolderName() === $instance->getWorld()->getFolderName()
				&& $instance->isParticipant($event->getPlayer())
				&& ($event->getInventory() instanceof ChestInventory || $event->getInventory() instanceof DoubleChestInventory)),
			true,
			EventPriority::LOW,
			true
		);

		foreach($this->coords as $coord){
			$world->setBlock($coord, VanillaBlocks::AIR());
			$world->addParticle($coord, new BlockBreakParticle($block));
		}

		foreach($this->laddercoords as $coord){
			$coord = $coord[0];
			$world->setBlock($coord, VanillaBlocks::AIR());
			$world->addParticle($coord, new BlockBreakParticle(VanillaBlocks::LADDER()));
		}

		$player = $event->getPlayer();
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§c{$player->getName()} §7solved the §cSecond Puzzle§7.");


		$e->flagForDespawn();

		return $player->getName();
	}

	public function setup(): void {
		$this->chestPos = new Vector3(47, 178, -44);
		
		$this->addLadderCoords(26,190,-68,3);
		$this->addLadderCoords(26,191,-68,3);
		$this->addLadderCoords(26,192,-68,3);
		$this->addLadderCoords(26,193,-68,3);
		$this->addLadderCoords(31,195,-69,4);
		$this->addLadderCoords(31,196,-69,4);
		$this->addLadderCoords(31,197,-69,4);
		$this->addLadderCoords(31,198,-69,4);
		$this->addLadderCoords(32,198,-68,3);
		$this->addLadderCoords(32,199,-68,3);
		$this->addLadderCoords(32,200,-68,3);
		$this->addLadderCoords(32,201,-68,3);
		$this->addLadderCoords(36,203,-69,4);
		$this->addLadderCoords(43,206,-69,4);


		$this->addCoords(-13,177,-66);
		$this->addCoords(-10,178,-69);
		$this->addCoords(-5,179,-69);
		$this->addCoords(-2,180,-69);
		$this->addCoords(-2,181,-72);
		$this->addCoords(-5,182,-74);
		$this->addCoords(-6,184,-73);
		$this->addCoords(-5,183,-69);
		$this->addCoords(1,185,-71);
		$this->addCoords(1,186,-67);
		$this->addCoords(3,187,-67);
		$this->addCoords(6,188,-67);
		$this->addCoords(21,190,-69);
		$this->addCoords(26,190,-69);
		$this->addCoords(26,191,-69);
		$this->addCoords(26,192,-69);
		$this->addCoords(26,193,-69);
		$this->addCoords(32,195,-69);
		$this->addCoords(32,196,-69);
		$this->addCoords(32,197,-69);
		$this->addCoords(32,198,-69);
		$this->addCoords(32,199,-69);
		$this->addCoords(32,200,-69);
		$this->addCoords(32,201,-69);
		$this->addCoords(37,203,-69);
		$this->addCoords(40,204,-69);
		$this->addCoords(44,206,-69);
	}

	public function addLadderCoords(int $x, int $y, int $z, int $facing): void {
		$this->laddercoords[] = [new Vector3($x, $y, $z), $facing];
	}

	public function addCoords(int $x, int $y, int $z): void {
		$this->coords[] = new Vector3($x, $y, $z);
	}

	public function getName() : string{
		return "Floor 1 Puzzle 2";
	}
}