<?php

declare(strict_types=1);

namespace skyblock\misc\synchroniser;

use pocketmine\entity\Skin;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\player\Player;
use Ramsey\Uuid\Uuid;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class PlayerListSynchroniser {

	/** @var array<string, Uuid> */
	private array $uuidCache = [];

	use AwaitStdTrait;

	public function __construct(private Player $player){
		$this->start();
	}

	public function start(): void {
		Await::f2c(function() {

			$last = null;
			$first = true;

			while($this->player->isOnline()) {
				if($first === true){
					yield $this->getStd()->sleep(3);
				} else yield $this->getStd()->sleep(40);


				if(!$this->player->isOnline()) break;

				$local = Utils::getOnlinePlayerObjectsLocally();

				if(empty($local)) continue;

				if($last === null){
					$skin = SkinAdapterSingleton::get()->toSkinData(new Skin("Standard_Custom", str_repeat("\x00", 8192)));
					$id = $this->player->getId();
					$entries = [];
					foreach(($last = Utils::getOnlinePlayerUsernames()) as $username) {
						if(isset($local[$username])){
							continue;
						}

						$uuid = Uuid::uuid4();
						$this->uuidCache[$username] = $uuid;
						$entries[] = PlayerListEntry::createAdditionEntry($uuid, $id, $username, $skin);
					}

					if(!empty($entries)){
						$this->send(PlayerListPacket::add($entries));
					}
					continue;
				}

				$current = Utils::getOnlinePlayerUsernames();
				$difference = array_diff($current, $last);

				if(empty($difference)){
					$difference = array_diff($last, $current);
					if(empty($difference)) continue;
				}

				$local = Utils::getOnlinePlayerObjectsLocally();
				if(empty($local)) continue;

				$removeEntries = [];
				$addEntries = [];
				$id = $this->player->getId();
				$skin = SkinAdapterSingleton::get()->toSkinData(new Skin("Standard_Custom", str_repeat("\x00", 8192)));

				foreach($difference as $diff){
					if(isset($local[$diff])) continue;

					if(in_array($diff, $current) && !in_array($diff, $last)){
						$uuid = Uuid::uuid4();
						$this->uuidCache[$diff] = $uuid;
						$addEntries[] = PlayerListEntry::createAdditionEntry($uuid, $id, $diff, $skin);
					} elseif(!in_array($diff, $current) && in_array($diff, $last)){
						if(isset($this->uuidCache[$diff])){
							$removeEntries[] = PlayerListEntry::createRemovalEntry($this->uuidCache[$diff]);
						}
					}
				}

				if(!empty($removeEntries)){
					$pk = PlayerListPacket::remove($removeEntries);
					$this->send($pk);
				}

				if(!empty($addEntries)){
					$pk = PlayerListPacket::add($addEntries);
					$this->send($pk);
				}

				$last = $current;
			}
		});
	}


	public function send(PlayerListPacket $pk): void {
		if($this->player->isConnected() && $this->player->isOnline()){
			$this->player->getNetworkSession()->sendDataPacket($pk);
		}
	}
}