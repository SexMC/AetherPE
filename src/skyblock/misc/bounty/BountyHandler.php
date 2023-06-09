<?php

declare(strict_types=1);

namespace skyblock\misc\bounty;

use Generator;
use pocketmine\player\Player;
use skyblock\Database;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\Queries;
use SOFe\AwaitGenerator\Await;

;

class BountyHandler {
	use AetherHandlerTrait;

	public function getBountyData(Player|string $player): Generator {
		$data = yield Database::getInstance()->getLibasynql()->asyncSelect(Queries::BOUNTY_SELECT, ["username" => ($player instanceof Player ? $player->getName() : $player)]);


		if(!isset($data[0])){
			return new BountyData(
				($player instanceof Player ? $player->getName() : $player),
				0,
				0,
				0,
				0
			);
		}

		return BountyData::fromArray($data[0]);
	}

	public function saveBountyHistory(BountyHistoryData $data): void {
		Await::f2c(function() use($data) {
			yield Database::getInstance()->getLibasynql()->asyncInsert(Queries::BOUNTY_HISTORY_UPDATE, $data->toArray());
		});
	}

	public function saveBountyData(BountyData $data): void {
		Await::f2c(function() use($data) {
			yield Database::getInstance()->getLibasynql()->asyncInsert(Queries::BOUNTY_UPDATE, $data->toArray());
		});
	}
}