<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\coinflip;

use Closure;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;
use skyblock\misc\coinflip\CoinflipHandler;

class CoinflipProgressUpdatePacket extends BasePacket{

	public function __construct(public string $player, public string $opponent, public string $title, public float $progress, public string $winner, Closure $closure = null){ parent::__construct($closure); }

	public function handle(array $data) : void{
		CoinflipHandler::getInstance()->handleIncomingCoinflipData($this);
	}

	public static function create(array $data) : static{
		$pk = new self($data["player"], $data["opponent"], $data["title"], $data["progress"], $data["winner"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function getType() : int{
		return PacketIds::COINFLIP_PROGRESS_UPDATE;
	}
}