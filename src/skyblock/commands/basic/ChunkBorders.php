<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\particle\HeartParticle;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\StringIntCache;
use SOFe\AwaitGenerator\Await;

class ChunkBorders extends AetherCommand {

	use AwaitStdTrait;
	use StringIntCache;

	protected function prepare() : void{
		$this->setDescription("View the chunk borders");

		$this->setAliases(["showchunks", "showchunk", "chunkborder"]);
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			if($this->get($sender->getName()) === 1){
				$this->remove($sender->getName());
				$sender->sendMessage(Main::PREFIX . "Disabled chunk borders");
				return;
			}

			$this->set($sender->getName(), 1);
			$sender->sendMessage(Main::PREFIX . "Enabled chunk borders");

			Await::f2c(function() use($sender) {
				while(true){
					if($sender->isOnline() && $this->get($sender->getName()) === 1){
						yield $this->getStd()->sleep(10);

						$x1 = ($sender->getLocation()->getX() >> 4) * 16 + 0.5;
						$z1 = ($sender->getLocation()->getZ() >> 4) * 16 + 0.5;
						$y = $sender->getLocation()->getY();

						$coords = [];
						for($i = 0; $i <= 2; $i++){
							$coords[] = new Vector3($x1, $y + $i, $z1);
							$coords[] = new Vector3($x1 + 15, $y + $i, $z1 + 15);
							$coords[] = new Vector3($x1, $y + $i, $z1 + 15);
							$coords[] = new Vector3($x1 + 15, $y + $i, $z1);
						}

						foreach($coords as $coord){
							$sender->getWorld()->addParticle($coord, new HeartParticle(), [$sender]);
						}
						continue;
					}

					$this->remove($sender->getName());
					break;
				}
			});
		}
	}
}