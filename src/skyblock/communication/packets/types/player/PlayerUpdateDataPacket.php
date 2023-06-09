<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\player;

use pocketmine\Server;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;
use skyblock\misc\booster\BoosterHandler;
use skyblock\sessions\Session;
use skyblock\utils\ScoreboardUtils;

class PlayerUpdateDataPacket extends BasePacket{

	const UPDATE_PERMS = "perms";
	const UPDATE_BOOSTERS = "boosters";
	const UPDATE_SCOREBOARD = "scoreboard";
	const UPDATE_FROZEN = "frozen";

	public function __construct(public string $player, public string $data){
		parent::__construct();
	}

	public function handle(array $data) : void{
		parent::handle($data);

		$p = Server::getInstance()->getPlayerExact($this->player);

		if($p === null) return;

		$session = new Session($p);

		switch($this->data){
			case self::UPDATE_PERMS:
				$session->updatePerms();
				break;
			case self::UPDATE_BOOSTERS:
				BoosterHandler::getInstance()->check($p, $session);
				break;
			case str_contains($this->data, self::UPDATE_SCOREBOARD):
				$line = str_replace(self::UPDATE_SCOREBOARD, "", $this->data);

				ScoreboardUtils::setLine($p, $line, null, $session);
				break;
			case self::UPDATE_FROZEN:
				$session->setFrozen($session->isFrozen());
				break;
		}
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["player"] = $this->player;
		$r["data"] = $this->data;

		return $r;
	}

	public static function create(array $data) : static{
		return new self($data["player"], $data["data"]);
	}

	public function getType() : int{
		return PacketIds::PLAYER_UPDATE_DATA;
	}
}