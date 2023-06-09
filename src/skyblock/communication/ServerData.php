<?php

declare(strict_types=1);

namespace skyblock\communication;

class ServerData {

	public function __construct(array $data){
		$this->players = $data["players"];
		$this->identifier = $data["identifier"];
		$this->loadedIslands = $data["loadedIslands"];
		$this->isIslandServer = $data["isIslandServer"];
		$this->tps = $data["tps"];
		$this->load = $data["load"];
	}

	public array $players = [];
	public string $identifier;
	public array $loadedIslands = [];
	public bool $isIslandServer;
	public float $tps;
	public float $load;
}