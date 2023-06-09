<?php

declare(strict_types=1);

namespace skyblock\items\potions;

use pocketmine\entity\Living;
use pocketmine\item\ConsumableItem;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\TimeUtils;

abstract class SkyBlockPotion extends SkyblockItem implements ConsumableItem {
	public abstract function getPotionName(): string;
	public abstract static function getEffectsLore(int $level): array;
	public abstract function getInputWithOutputs(): array;
	public abstract function onActivate(AetherPlayer $player): void;
	public abstract function onDeActivate(AetherPlayer $player): void;





	public const TAG_LEVEL = "aether_potion_level";
	public const TAG_DURATION = "aether_potion_duration";
	public const TAG_REDSTONE_USED = "aether_potion_redstone_used";
	public const TAG_GLOW_STONE_DUST_USED = "aether_potion_glow_stone_used";

	public function __construct(ItemIdentifier $id){
		parent::__construct($id, $this->getPotionName());

		$this->updateCustomName();
		$this->makeGlow();
	}

	public function updateCustomName(): void {
		$this->setCustomName('§r§l§3Potion "§r§6' . $this->getPotionName() . ' ' . CustomEnchantUtils::roman($this->getPotionLevel()) . '"');
	}

	public function setUsedRedstoneModifier(bool $used = true): self {
		$this->getNamedTag()->setByte(self::TAG_REDSTONE_USED, (int) $used);
		return $this;
	}

	public function setUsedGlowstoneModifier(bool $used = true): self {
		$this->getNamedTag()->setByte(self::TAG_GLOW_STONE_DUST_USED, (int) $used);
		return $this;
	}

	public function usedGlowstoneModifier(): bool {
		return $this->getNamedTag()->getByte(self::TAG_GLOW_STONE_DUST_USED, 0) === 1;
	}

	public function usedRedstoneModifier(): bool {
		return $this->getNamedTag()->getByte(self::TAG_REDSTONE_USED, 0) === 1;
	}

	public function resetLore(array $lore = []) : void{

		$string = "";

		foreach(static::getEffectsLore($this->getPotionLevel()) as $l) {
			$string .= "§r§7§f§l *§r§a $l\n";
		}

		$lore = array_merge($lore, [
			"§r§7§oBrewed inside of the priceless",
			"§r§7§ointergalactic brewing stand.",
			"§r§7§oTreat this item with care.",
			"§r",
			"§r§l§3EFFECTS",
			$string,
			"§r§l§3DURATION",
			"§r§7" . TimeUtils::getFormattedTime($this->getDuration()),
			"§r",
			"§r§l§cNOTE §r§7The potion itself and effects",
			"§r§7given only works in the Grinding Planet",
		]);

		parent::resetLore($lore);
	}


	public function getPotionLevel(): int {
		return $this->getNamedTag()->getInt(self::TAG_LEVEL, 1);
	}

	public function setPotionLevel(int $level): self {
		$this->getNamedTag()->setInt(self::TAG_LEVEL, $level);

		match($level) {
			1, 2 => $this->getProperties()->setRarity(Rarity::common()),
			3, 4 => $this->getProperties()->setRarity(Rarity::uncommon()),
			5, 6 => $this->getProperties()->setRarity(Rarity::rare()),
			7, 8 => $this->getProperties()->setRarity(Rarity::epic()),
			default => $this->getProperties()->setRarity(Rarity::epic()),
		};

		$this->updateCustomName();
		$this->resetLore();

		return $this;
	}

	public function getDuration(): int {
		return $this->getNamedTag()->getInt(self::TAG_DURATION, 3 * 60); //default 3 mins
	}

	public function setDuration(int $duration): self {
		$this->getNamedTag()->setInt(self::TAG_DURATION, $duration);
		$this->updateCustomName();
		$this->resetLore();
		return $this;
	}

	public function onConsume(Living $consumer) : void{
		if($consumer instanceof AetherPlayer){
			$all = $consumer->getPotionData()->getActivePotions();
			if(isset($all[$this->getPotionName()])){
				if($all[$this->getPotionName()]->item->getPotionLevel() <= $this->getPotionLevel()){
					$consumer->sendMessage(Main::PREFIX . "You already have an active §c{$this->getPotionName()} §7potion");
					return;
				}
			}


			$all[$this->getPotionName()] = new AetherPotionInstance($consumer->getName(), clone $this, $this->getDuration(), time());
			$consumer->getPotionData()->setActivePotions($all);

			$this->onActivate($consumer);
		}
	}

	public function getResidue() : Item{
		return VanillaItems::GLASS_BOTTLE();
	}

	public function canStartUsingItem(Player $player) : bool{
		return true;
	}

	public function getMaxStackSize() : int{
		return 1;
	}


	public function buildProperties() : SkyblockItemProperties{
		return new SkyblockItemProperties();
	}

	public function getAdditionalEffects() : array{
		return [];
	}
}