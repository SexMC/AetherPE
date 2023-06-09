<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\SingletonTrait;

class RankHandler {
	use SingletonTrait;

	/**
	 * @var array<string, BaseRank>
	 */
	private array $ranks = [];

	public function __construct(){
		/** @var BaseRank $v */
		foreach($this->getAll() as $v){
			$this->ranks[strtolower($v->getName())] = $v;
		}
	}

	public function getRank(string $rank): ?BaseRank {
		return $this->ranks[strtolower($rank)] ?? null;
	}

	/**
	 * @return BaseRank[]
	 */
	public function getRanks() : array{
		return $this->ranks;
	}

	private function getAll() {
		return [
			new YoutuberRank(),
			new TrojeRank(),
			new TrialModeratorRank(),
			new TravelerRank(),
			new SinonRank(),
			new TheseusRank(),
			new OwnerRank(),
			new ModeratorRank(),
			new ManagerRank(),
			new HydraRank(),
			new HelperRank(),
			new HeadAdminRank(),
			new AuroraRank(),
			new AstronomicalRank(),
			new AetherRank(),
			new AetherPlusRank(),
			new AdminRank(),
		];
	}
}