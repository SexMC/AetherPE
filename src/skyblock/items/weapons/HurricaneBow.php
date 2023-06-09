<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use pocketmine\entity\projectile\Arrow as ArrowEntity;
use pocketmine\item\ItemIdentifier;
use skyblock\entity\boss\PveEntity;
use skyblock\entity\projectile\RunaansArrow;
use skyblock\entity\projectile\SkyBlockArrow;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;

class HurricaneBow extends SkyBlockBow {

	const TAG_HURRICANE_KILLS = "tag_hurricane_bow_kills";

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->resetLore();
		$this->properties->setRarity(Rarity::epic());

		$this->setCustomName("§r" . $this->getProperties()->getRarity()->getColor() . "Hurricane Bow");
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 120));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 50));
	}

	public function getNextUpgradeName(): ?string {
		$kills = $this->getKills();

		if($kills === 0){
			return "Double Shot";
		}

		switch($kills){
			case $kills < 20:
				return "Double Shot";
			case $kills < 50:
				return "Triple Shot";
			case $kills < 125:
				return "Quadra Shot";
			case $kills < 250:
				return "Penta Shot";
		}

		return null;
	}

	public function getCurrentExtraArrowCount(): int {
		$kills = $this->getKills();

		if($kills === 0){
			return 0;
		}

		switch($kills){
			case $kills < 20:
				return 0;
			case $kills < 50:
				return 1;
			case $kills < 125:
				return 2;
			case $kills < 250:
				return 3;
		}

		return 4;
	}

	public function getNextUpgradeNeeded(): ?int {
		$kills = $this->getKills();

		if($kills === 0){
			return 20;
		}

		switch($kills){
			case $kills < 20:
				return 20;
			case $kills < 50:
				return 50;
			case $kills < 125:
				return 125;
			case $kills < 250:
				return 250;
		}

		return null;
	}

	public function resetLore(array $lore = []) : void{
		$kills = $this->getKills();

		$x = [];
		if($this->getNextUpgradeName() !== null){
			$x[] = "§r§7Next upgrade: §e" . $this->getNextUpgradeName() . " §8(§a{$kills}§7/§c{$this->getNextUpgradeNeeded()}§8)";
		}

		$x[] = "";
		$x[] = "§r§7Kills: §b" . number_format($kills);

		$this->properties->setDescription(array_merge([
			"§r§7§oThe calm before the storm, a feeling of dread,",
			"§r§7§oand then the sudden violence of the hurricane.",
			"§r",
			"§r§5Ability: Tempest",
			"§r§7The more kills you get using",
			"§r§7this bow the more powerful it",
			"§r§7becomes! Reach §5250§7 kills to",
			"§r§7unlock its full potential",
		], $x));

		parent::resetLore($lore);
	}

	public function onAttackPve(AetherPlayer $player, PlayerAttackPveEvent $event) : void{
		parent::onAttackPve($player, $event);

		$e = $event->getEntity();

		$damage = $event->getFinalDamage();

		if($damage >= $e->getHealth()){
			$this->setKills($this->getKills() + 1);
			$this->resetLore();
			$player->getInventory()->setItemInHand($this);
		}
	}

	public function setKills(int $kills): self {
		$this->getNamedTag()->setInt(self::TAG_HURRICANE_KILLS, $kills);
		return $this;
	}

	public function getKills(): int {
		return $this->getNamedTag()->getInt(self::TAG_HURRICANE_KILLS, 0);
	}

	public function onShootArrow(AetherPlayer $player, SkyBlockArrow $arrow) : void{
		parent::onShootArrow($player, $arrow);

		for($i = 1; $i <= $this->getCurrentExtraArrowCount(); $i++){



			$entity = new SkyBlockArrow($arrow->getLocation(), $player, false);

			$add = match ($i) {
				1 => 0.2,
				2 => -0.2,
				3 => 0.4,
				4 => -0.4,
			};

			$entity->setMotion($arrow->getMotion()->add($add, 0, $add));
			$entity->setPickupMode(ArrowEntity::PICKUP_CREATIVE);

			$entity->setSourceItem($this);
			$entity->spawnToAll();
		}

	}
}