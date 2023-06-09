<?php

declare(strict_types=1);

namespace skyblock\misc\bounty;

use http\Client\Request;

class BountyHistoryData {

	public function __construct(
		public string $player,
		public string $killer,
		public int $amount,
		public int $unix,
	){ }

	public function toArray(): array {
		return [
			"player" => $this->player,
			"killer" => $this->killer,
			"amount" => $this->amount,
			"unix" => $this->unix,
		];
	}
}