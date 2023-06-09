<?php

declare(strict_types=1);

namespace skyblock\items\crates;

use pocketmine\utils\SingletonTrait;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\AstronomicalCrate;
use skyblock\items\crates\types\ChallengesCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\HeroicCrate;
use skyblock\items\crates\types\LegendaryCrate;
use skyblock\items\crates\types\MythicCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\traits\AetherHandlerTrait;

class CrateHandler {
	use AetherHandlerTrait;

	/** @var Crate[] */
	private array $crates = [];

	public function onEnable() : void{
	}

	public function register(Crate $crate): void {
		$this->crates[strtolower($crate->getName())] = $crate;
	}

	public function getCrate(string $crate): ?Crate {
		return $this->crates[strtolower($crate)] ?? null;
	}

	public function getAllCrates(): array {
		return $this->crates;
	}


}