<?php

declare(strict_types=1);

namespace skyblock\items\pets;

use pocketmine\event\EventPriority;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\PvEItemEditor;
use skyblock\items\SkyblockItems;
use skyblock\Main;
use skyblock\misc\skills\CombatSkill;
use skyblock\misc\skills\FarmingSkill;
use skyblock\misc\skills\FishingSkill;
use skyblock\misc\skills\ForagingSkill;

abstract class Pet implements IPet{

	public function getItem(int $level, int $rarity, int $xp, int $candy): Item {
		$item = $this->getPlainItem();

		$this->applyStats($item, $level, $rarity);
		$lore = PvEItemEditor::getCosmeticsLore($item);
		$lore[] = "§r";

		foreach($this->getAbilityText($level, $rarity) as $k => $v){
			$lore[] = "§r" . $v;
		}



		if($level < 100){
			$next = $level + 1;
			$lore[] = "§r";
			$lore[] = "§r§7Progress to level $next: §e" . number_format(($xp/self::getXpNeeded($rarity, $next)*100)) . "%";
			$lore[] = "§r§7(§e" . number_format($xp) . "§6/§e" . number_format(self::getXpNeeded($rarity, $next)) . "§7)";
			$lore[] = "§r";
		}

		if($candy > 0){
			$lore[] = "§r§a($candy/10) Pet Candy Used";
			$lore[] = "§r";
		}

		$lore[] = "§r§eRight-Click to add this to your Pet Collection!";

		$lore[] = "§r";
		$lore[] = "§r§l" . $this->getColor($rarity) . $this->getRarityName($rarity);


		$item->setCustomName("§r§l§f» §r§fLvl $level §l«§r " . $this->getColor($rarity) . $this->getName());
		$item->setLore($lore);
		
		$item->getNamedTag()->setInt(self::TAG_LEVEL, $level);
		$item->getNamedTag()->setInt(self::TAG_RARITY, $rarity);
		$item->getNamedTag()->setInt(self::TAG_XP, $xp);
		$item->getNamedTag()->setInt(self::TAG_CANDY_USED, $candy);
		$item->getNamedTag()->setString(self::TAG_UUID, uniqid("pet-id-" . mt_rand(1, 10)));
		$item->getNamedTag()->setString(self::TAG_ID, $this->getIdentifier());

		return $item;
	}

	public static function getXpNeeded(int $rarity, int $level): int {
		return (int) floor(match($rarity) {
			IPet::RARITY_COMMON => 100 * 1.085835**$level,
			IPet::RARITY_UNCOMMON => 175 * 1.085835**$level,
			IPet::RARITY_RARE => 275 * 1.085835**$level,
			IPet::RARITY_EPIC => 440 * 1.085835**$level,
			IPet::RARITY_LEGENDARY => 660 * 1.085835**$level,
		});
	}


	public function getPrefix(): string {
		return "§r§l{$this->getColor()}{$this->getName()}§r§7 ";
	}

	public static function getXuid(Item $item): string {
		return $item->getNamedTag()->getString(self::TAG_UUID);
	}

	public static function getXp(Item $item): int {
		return $item->getNamedTag()->getInt(self::TAG_XP, 0);
	}


	public static function getLevel(Item $item): int {
		return $item->getNamedTag()->getInt(self::TAG_LEVEL, 1);
	}

	public static function getRarity(Item $item): int {
		return $item->getNamedTag()->getInt(self::TAG_RARITY);
	}

	public static function getId(Item $item): ?Pet {
		$type = $item->getNamedTag()->getString(self::TAG_ID, "");

		if($type === "") return null;

		return PetHandler::getInstance()->getPet($type);
	}

	/**
	 * @return string
	 */
	public function getSkillClassByType(): string {
		return match($this->getSkillType()){
			self::TYPE_COMBAT => CombatSkill::id(),
			self::TYPE_FARMING => FarmingSkill::id(),
			self::TYPE_FISHING => FishingSkill::id(),
			self::TYPE_FORAGING => ForagingSkill::id(),
		};
	}


	public function getPlainItem(): PetItem {
		return SkyblockItems::PET_ITEM();
	}

	public function sendsActivationMessage(): bool {
		return true;
	}

	public function getColor(int $rarity) : string{
		return match($rarity) {
			self::RARITY_COMMON => "§f",
			self::RARITY_UNCOMMON => "§a",
			self::RARITY_RARE => "§3",
			self::RARITY_EPIC => "§5",
			self::RARITY_LEGENDARY => "§6",
		};
	}

	public function getRarityName(int $rarity) : string{
		return match($rarity) {
			self::RARITY_COMMON => "COMMON",
			self::RARITY_UNCOMMON => "UNCOMMON",
			self::RARITY_RARE => "RARE",
			self::RARITY_EPIC => "EPIC",
			self::RARITY_LEGENDARY => "LEGENDARY",
		};
	}

	public function getPriority(): int {
		return EventPriority::NORMAL;
	}

	public function onClose(Main $plugin): void {}

	public function canOnlyBeUsedInPvP() : bool{
		return false;
	}

	public function onDeActivate(Player $player, PetInstance $pet) : bool{
		return true;
	}

	public abstract function getCraftingIngredient(): Item;

}