<?php

declare(strict_types=1);

namespace skyblock\misc\warps;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use skyblock\caches\pvpzones\PvpZone;
use skyblock\caches\ruins\RuinsCache;
use skyblock\communication\packets\types\player\PlayerTeleportRequestPacket;
use skyblock\communication\packets\types\player\PlayerTeleportResponsePacket;
use skyblock\Database;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Queries;
use skyblock\utils\TimeUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class Warp {
	use AwaitStdTrait;

	public function __construct(
		public string $name,
		public string $world,
		public string $server,
		public Vector3 $pos,
		public bool $open
	){}


	public function teleport(Player $player): void {
		if(!$this->open){
			$player->sendMessage(Main::PREFIX . "Warp §c" . $this->name . " is closed");
			return;
		}

		if(strtolower(Utils::getServerName()) === strtolower($this->server)){
			$manager = Server::getInstance()->getWorldManager();
			$manager->loadWorld($this->world);

			if(($world = Server::getInstance()->getWorldManager()->getWorldByName($this->world))){
				$player->teleport(Position::fromObject($this->pos, $world));
			}
			return;
		}

		Await::f2c(function() use($player){
			Main::getInstance()->getCommunicationLogicHandler()->sendPacket(new PlayerTeleportRequestPacket(
				$player->getName(),
				PlayerTeleportRequestPacket::MODE_WARP,
				TextFormat::clean($this->name),
				Utils::getServerName(),
				yield Await::RESOLVE
			));

			/** @var PlayerTeleportResponsePacket $data */
			$data = yield Await::ONCE;

			if(!$player->isOnline()) return;

			if($data->server === "error"){
				$player->sendMessage(Main::PREFIX . "Error occurred while trying to teleport to warp §c" . $this->name);
				return;
			}

			Utils::transfer($player, $data->server);
		});
	}

	public function save(): void {
		Database::getInstance()->getLibasynql()->executeInsert(Queries::WARP_UPDATE, [
			"name" => $this->name,
			"world" => $this->world,
			"server" => Utils::getServerName(),
			"pos" => json_encode(PvpZone::jsonSerializeVector($this->pos)),
			"open" => $this->open
		]);
	}

	public function delete(): void {
		Database::getInstance()->getLibasynql()->executeChange(Queries::WARP_DELETE, [
			"name" => $this->name
		]);
	}
}