<?php

declare(strict_types=1);

namespace skyblock\misc\bounty;

class BountyData {

	public function __construct(
		public string $username,
		public int $currentBounty,
		public int $lifeTimeBounty,
		public int $maxBounty,
		public int $earned,
	){ }


	public static function fromArray(array $data): self {
		return new self(
			$data["username"],
			$data["currentBounty"],
			$data["lifeTimeBounty"],
			$data["maxBounty"],
			$data["earned"],
		);
	}


	public function toArray(): array {
		return [
			"username" => $this->username,
			"currentBounty" => $this->currentBounty,
			"lifeTimeBounty" => $this->lifeTimeBounty,
			"maxBounty" => $this->maxBounty,
			"earned" => $this->earned,
		];
	}
}