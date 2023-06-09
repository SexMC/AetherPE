<?php

declare(strict_types=1);

namespace skyblock\communication\datatypes;

use JsonSerializable;
use skyblock\utils\IslandUtils;
use skyblock\utils\Utils;

class ServerInformation implements JsonSerializable{
	public function __construct(
		public string $identifier,
		public bool $isHub,
		public array $onlinePlayers,
		public array $loadedIslands,
	){ }

	public function jsonSerialize(){
		return [
			"identifier" => $this->identifier,
			"isHub" => $this->isHub,
			"onlinePlayers" => $this->onlinePlayers,
			"loadedIslands" => $this->loadedIslands
		];
	}

	public static function create(): self {
		return new ServerInformation(
			Utils::getServerName(),
			Utils::isHubServer(),
			Utils::getOnlinePlayerUsernamesLocally(),
			(Utils::isIslandServer() ? IslandUtils::getLoadedIslands() : [])
		);
	}
}