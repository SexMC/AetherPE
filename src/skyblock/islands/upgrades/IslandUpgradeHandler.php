<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades;


use skyblock\islands\upgrades\types\IslandDuplicationTechniqueUpgrade;
use skyblock\islands\upgrades\types\IslandFarmingExpertUpgrade;
use skyblock\islands\upgrades\types\IslandHoppersUpgrade;
use skyblock\islands\upgrades\types\IslandLoweredPricesUpgrade;
use skyblock\islands\upgrades\types\IslandMemberUpgrade;
use skyblock\islands\upgrades\types\IslandPersonalBoosterUpgrade;
use skyblock\islands\upgrades\types\IslandSizeUpgrade;
use skyblock\islands\upgrades\types\IslandSpawnersUpgrade;
use skyblock\islands\upgrades\types\IslandWarzoneControlUpgrade;
use skyblock\traits\AetherSingletonTrait;

class IslandUpgradeHandler  {
	use AetherSingletonTrait;

	private array $upgrades = [];

	public function __construct(){
		self::setInstance($this);

		$this->registerUpgrade(new IslandDuplicationTechniqueUpgrade());
		$this->registerUpgrade(new IslandFarmingExpertUpgrade());
		$this->registerUpgrade(new IslandLoweredPricesUpgrade());
		$this->registerUpgrade(new IslandMemberUpgrade());
		$this->registerUpgrade(new IslandPersonalBoosterUpgrade());
		$this->registerUpgrade(new IslandWarzoneControlUpgrade());

		$this->registerUpgrade(new IslandHoppersUpgrade());
		$this->registerUpgrade(new IslandSizeUpgrade());
		$this->registerUpgrade(new IslandSpawnersUpgrade());
	}

	public function registerUpgrade(IslandUpgrade $upgrade): void {
		$this->upgrades[$upgrade::getIdentifier()] = $upgrade;
	}

	/**
	 * @return IslandUpgrade[]
	 */
	public function getAllUpgrades(): array {
		return $this->upgrades;
	}

	public function getUpgrade(string $identifier): ?IslandUpgrade {
		return $this->upgrades[$identifier] ?? null;
	}
}