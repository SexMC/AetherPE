<?php

declare(strict_types=1);

namespace skyblock\misc\dungeons\floors\one;


use Generator;
use pocketmine\block\Air;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\event\EventPriority;
use pocketmine\event\world\ChunkUnloadEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use skyblock\entity\boss\BonzoBoss;
use skyblock\entity\boss\PveEntity;
use skyblock\events\block\ButtonClickEvent;
use skyblock\events\pve\PlayerKillPveEvent;
use skyblock\items\accessory\Accessory;
use skyblock\items\accessory\AccessoryHandler;
use skyblock\misc\dungeons\DungeonFloor;
use skyblock\misc\dungeons\DungeonInstance;
use skyblock\misc\pve\PveHandler;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

class DungeonFloorOne extends DungeonFloor {
	use AwaitStdTrait;
	
	private AxisAlignedBB $gates;
	private AxisAlignedBB $gate2;
	private AxisAlignedBB $entrance;
	private Vector3 $waveStartButton;
	private Vector3 $bonzoLocation;

	//final boss area
	private AxisAlignedBB $area;


	private bool $running = false;

	public function start(DungeonInstance $instance): Generator {
		$this->running = true;
		Await::f2c(function() use($instance) {
			while($this->running){
				$event = yield $this->getStd()->awaitEvent(
					ChunkUnloadEvent::class,
					fn(ChunkUnloadEvent $e) => $e->getWorld()->getFolderName() === $instance->getWorld()->getFolderName(),
					true,
					EventPriority::LOW,
					true,
				);


				if($this->running){
					$event->cancel();
				}
			}
		});

		$instance->broadcastMessage("§r§7");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7Solve all the puzzles to open The Gates");
		$instance->broadcastMessage("§r§7");



		yield $this->getStd()->awaitEvent(
			ButtonClickEvent::class,
			function(ButtonClickEvent $e) use($instance) {
				if($instance->isParticipant($e->getPlayer())){
					if( $e->getBlock()->getPosition()->getWorld()->getFolderName() === $instance->getWorld()->getFolderName()){
						return true;
					}
				}

				return false;
			} ,
			true,
			EventPriority::LOW,
			true,
		);

		yield $this->getPuzzle(0)->start($instance);
		yield $this->getPuzzle(1)->start($instance);


		for($x = (int) floor($this->gates->minX); $x <= (int) floor($this->gates->maxX); $x++){
			for($z = (int) floor($this->gates->minZ); $z <= (int) floor($this->gates->maxZ); $z++){
				for($y = (int) floor($this->gates->minY); $y <= (int) floor($this->gates->maxY); $y++){
					if($instance->getWorld()->getBlockAt($x, $y, $z)->getId() === BlockLegacyIds::IRON_BARS){
						$instance->getWorld()->setBlockAt($x, $y, $z, VanillaBlocks::AIR());
					}
				}
			}
		}

		$instance->broadcastMessage("§r§7");
		$instance->broadcastMessage("§r§l§bb» DUNGEONS « §r§cThe Gates§r§7 have been opened!!");
		$instance->broadcastMessage("§r§7");

		$wave1 = ["floor-one-zombie" => 17, "floor-one-spider" => 3];
		$wave2 = ["floor-one-zombie" => 16, "floor-one-spider" => 3, "floor-one-witch" => 1];
		$wave3 = ["floor-one-zombie" => 10, "floor-one-spider" => 6, "floor-one-witch" => 4];

		yield $this->getStd()->sleep(20 * 10);
		
		
		yield $this->getStd()->awaitEvent(ButtonClickEvent::class, fn(ButtonClickEvent $event) => $event->getBlock()->getPosition()->equals($this->waveStartButton), true, EventPriority::LOW, true);

		$instance->broadcastMessage("§r§7");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7I see you, you are coming for me!");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7Many have tried before, but have failed dramatically.");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7Before you can even get close to my home, you will have to face my slaves!");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7~ §l§aTurtle Bonzo");
		$instance->broadcastMessage("§r§7");


		$instance->title("§b§lWave I");
		$this->spawnWave($wave1, $instance, $this->entrance);

		$i = 1;
		$first = true;
		while(true){

			if($i >= 20){
				if($first === null) break;

				$i = 0;

				if($first){
					$instance->title("§b§lWave II");
					$this->spawnWave($wave2, $instance, $this->entrance);

					$first = false;
				} else {
					$first = null;
					$this->spawnWave($wave3, $instance, $this->entrance);
					$instance->title("§b§lWave III");
				}
			}

			yield $this->getStd()->awaitEvent(
				PlayerKillPveEvent::class,
				fn(PlayerKillPveEvent $event) => $event->getPlayer()->getWorld()->getFolderName() === $instance->getWorld()->getFolderName(),
				true,
				EventPriority::LOW,
				true
			);
			$i++;
			var_dump("event received");
		}

		$instance->broadcastMessage("§r§7");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7You guys are stronger than I thought!");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7You are one of the few players that were able to beat my slaves!");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7I have unlocked the gates for you. §4§l:))");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7I'll let you fight against more slaves and against some of my personal protectors!");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7~ §l§aTurtle Bonzo");
		$instance->broadcastMessage("§r§7");




		for($x = (int) floor($this->gate2->minX); $x <= (int) floor($this->gate2->maxX); $x++){
			for($z = (int) floor($this->gate2->minZ); $z <= (int) floor($this->gate2->maxZ); $z++){
				for($y = (int) floor($this->gate2->minY); $y <= (int) floor($this->gate2->maxY); $y++){
					$id = $instance->getWorld()->getBlockAt($x, $y, $z)->getId();
					if($id === BlockLegacyIds::IRON_BARS || $id === BlockLegacyIds::BEDROCK || $id === BlockLegacyIds::WOODEN_BUTTON){
						$instance->getWorld()->setBlockAt($x, $y, $z, VanillaBlocks::AIR());
					}
				}
			}
		}

		$finalWave = ["floor-one-zombie" => 25, "floor-one-spider" => 15, "floor-one-witch" => 10, "floor-one-iron-golem" => 10];
		$this->spawnWave($finalWave, $instance, $this->area);

		$bonzo = new BonzoBoss(Location::fromObject($this->bonzoLocation->add(0.5, 0, 0.5), $instance->getWorld()));
		$bonzo->spawnToAll();

		$i = 0;
		while(true){
			yield $this->getStd()->awaitEvent(
				PlayerKillPveEvent::class,
				fn(PlayerKillPveEvent $event) => $event->getPlayer()->getWorld()->getFolderName() === $instance->getWorld()->getFolderName(),
				true,
				EventPriority::LOW,
				true
			);


			$i++;

			if($i % 8 === 0){
				foreach($instance->getParticipants() as $player){
					if($player->isOnline() && $player->getWorld()->getFolderName() === $instance->getWorld()->getFolderName()){
						$bonzo->useAbility($player);
					}
				}
			}


			if($i >= 45){
				//get rid of bonzo!
				$bonzo->spawnToAll();

				$instance->broadcastMessage("§r");
				$instance->broadcastMessage("§r§l§a» Mr Bonzo «§r§7 You are way stronger than I thought!");
				$instance->broadcastMessage("§r§l§a» Mr Bonzo «§r§7 You have won this time, but we will meet again.");
				$instance->broadcastMessage("§r§l§a» Mr Bonzo «§r§7 I will be stronger, smarter and better than you then.");
				$instance->broadcastMessage("§r");
				break;
			}
		}


		yield $this->getStd()->sleep(20 * 5);
		$instance->broadcastMessage("§r");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§aCongratulations, §r§7you have completed");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7the first dungeon floor.");
		$instance->broadcastMessage("§r§l§b» DUNGEONS « §r§7 Your rewards can be found in the chests.");
		$instance->broadcastMessage("§r");
		$bonzo->flagForDespawn();


		$this->running = false;
	}

	public function spawnWave(array $data, DungeonInstance $instance, AxisAlignedBB $bb): void {
		foreach($data as $k => $v){
			for($i = 0; $i <= $v; $i++){
				$c = $this->getRandomCoord($bb);
				while(!$this->isSuitableForMob($c, $instance)){
					$c = $this->getRandomCoord($bb);
				}
				
				$instance->getWorld()->loadChunk($c->getFloorX() >> 4, $c->getFloorZ() >> 4);

				$d = PveHandler::getInstance()->getEntities()[$k];
				$e = new PveEntity($d["networkID"], Location::fromObject($c->add(0.5, 0, 0.5), $instance->getWorld()), $d["nbt"]);
				$e->spawnToAll();
			}
		}
	}

	public function isSuitableForMob(Vector3 $v, DungeonInstance $instance): bool {
		$coords = [$v, $v->add(0, -1, 0), $v->add(0, 1, 0)];

		$instance->getWorld()->loadChunk($v->getFloorX() >> 4, $v->getFloorZ() >> 4);

		foreach($coords as $c){
			if($instance->getWorld()->getBlock($c) instanceof Air) continue;

			return false;
		}

		return true;
	}

	public function getRandomCoord(AxisAlignedBB $bb): Vector3 {
		return new Vector3(
			mt_rand((int) $bb->minX, (int) $bb->maxX),
			mt_rand((int) $bb->minY, (int) $bb->maxY),
			mt_rand((int) $bb->minZ, (int) $bb->maxZ)
		);
	}

	public function setup() : void{
		$this->waveStartButton = new Vector3(129, 188, 110);
		$this->gates = new AxisAlignedBB(82, 191, -47, 87, 208, -36);
		$this->entrance = new AxisAlignedBB(109, 187, 3, 140, 190, 101);
		$this->gate2 = new AxisAlignedBB(126, 186, 110, 133, 196, 112);
		$this->area = new AxisAlignedBB(63, 185, 116, 200, 220, 280);
		$this->bonzoLocation = new Vector3(129, 197, 187);

		$this->addPuzzle(new FloorOnePuzzleOne());
		$this->addPuzzle(new FloorOnePuzzleTwo());
	}

}