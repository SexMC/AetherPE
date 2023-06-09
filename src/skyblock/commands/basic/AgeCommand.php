<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\AgeSetSubCommand;
use skyblock\commands\basic\sub\AgeStartSubCommand;
use skyblock\commands\basic\sub\AgeStopSubCommand;
use skyblock\Database;
use skyblock\Main;
use skyblock\utils\TimeUtils;

class AgeCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("See the server's age");

		$this->registerSubCommand(new AgeStartSubCommand("start"));
		$this->registerSubCommand(new AgeStopSubCommand("stop"));
		$this->registerSubCommand(new AgeSetSubCommand("set"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		//$e = new WitheredBlazeBoss($sender->getLocation());
		//$e->spawnToAll();
		//return;

		//$e = new IslandBossEntity(EntityIds::BLAZE, $sender->getLocation(), (new CompoundTag())->setString("networkID38", EntityIds::BLAZE)->setInt("hardness", 7));
		//$e->spawnToAll();
		//return;

			/*
			$e = new MovingEntity($sender->getLocation());
			$e->spawnToAll();


			if($vec2 === null){
				var_dump("none found");
				return;
			}

			$vec2 = $vec2->getPosition()->asVector3();

			$x1 = $vec1->getX();
			$x2 = $vec2->getX();
			$y1 = $vec1->getZ();
			$y2 = $vec2->getZ();
			var_dump("x1 $x1", "x2 $x2", "y1 $y1", "y2 $y2");


			$y1 = $vec1->getZ();

			$y2 = $vec2->getZ();

			$x1 = $vec1->getX();
			$x2 = $vec2->getX();

			var_dump("x1 $x1", "x2 $x2", "y1 $y1", "y2 $y2");

			//y-y1=(y2-y1)/(x2-x1)*(x-x1)
			//y=ax+b

			$rico = ($y2-$y1)/($x2-$x1);
			$b = $y1 - ($rico * $x1);
			$b = $y1 - $a * $x1;

			var_dump("rico: $a");

			$zz = fn(float $x) => $a * $x + $b;
			if($x1 > $x2) {
				$min = $x1 - 0.01;
			} else $min = $x1 + 0.01;


			$this->teleport(new Vector3($min, $this->getLocation()->getY(), $zz($min)));
			$this->lookAt($target->getLocation());
			$zz = fn(float $x) => (($y2-$y1)/($x2-$x1)) * $x + ($y1 - (($y2-$y1)/($x2-$x1) * $x1));

			$rangeX = range(min($x1, $x2), max($x1, $x2), 0.5);

			var_dump("rico: $rico");
			var_dump("b: $b");


			foreach($rangeX as $x){
				$vec = new Vector3($x, $sender->getLocation()->getY(), $zz($x));
				var_dump((string) $vec);
				$sender->getWorld()->addParticle($vec, new RedstoneParticle());



				if(isset($rangeZ[$k])){
					var_dump("adds");
					$v = new Vector3($xx($rangeZ[$k]), $sender->getLocation()->getY(), $zz($x));
					var_dump((string) $v);
					$sender->getWorld()->addParticle($v, new RedstoneParticle());
				}
			}
		}


		return;*/


		/*$part = new RedstoneParticle(3);
		/** @var Player $p */
		/*$p = $sender;


		$add = $p->getPosition()->getX();
		$adz = $p->getPosition()->getZ();
		$r = 5;
		for($i = 0; $i <= 90; $i++){
			$x = cos($i) * $r;
			$z = sin($i) * $r;


			$p->getWorld()->addParticle(new Vector3($add + $x, $p->getPosition()->getY(), $adz + $z), $part);
			$p->getWorld()->addParticle(new Vector3($add - $x, $p->getPosition()->getY(), $adz - $z), $part);
			$p->getWorld()->addParticle(new Vector3($add + $x, $p->getPosition()->getY(), $adz - $z), $part);
			$p->getWorld()->addParticle(new Vector3($add + $x, $p->getPosition()->getY(), $adz + $z), $part);
		}

		return;*/

		$time = (int) (Database::getInstance()->redisGet("server.age") ?? 0);
		if($time === 0){
			$sender->sendMessage(Main::PREFIX . "Planet countdown hasn't started yet");
			return;
		}


		$time = TimeUtils::getFullyFormattedTime(time() - $time);
		$sender->sendMessage(Main::PREFIX . "§r§3§lPlanet §r§8: §r§l§bNeptune§r§7 Age: §c$time");
	}
}