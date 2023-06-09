<?php

declare(strict_types=1);

namespace skyblock\misc\skills;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\CustomEnchantUtils;

class MiningSkill extends Skill {
	protected function onLevelUp(AetherPlayer $player, Session $session, int $old, int $new) : void{
		$session->setMiningFortune($session->getMiningFortune() + 4);
		$session->setDefense($session->getDefense() + 1);
		$session->increaseEssence($this->getEssenceGain($new));
		$player->getPveData()->setDefense($player->getPveData()->getDefense() + 1);
		$player->getPveData()->setMiningFortune($player->getPveData()->getMiningFortune() + 4);


        $player->sendMessage(array(
            "§r",
            "§r§l§6         » MINING SKILL LEVEL UP! « ",
            "§r§e           You are now level $new at Mining!",
            "§r",
        ));

	}

	public static function id() : string{
		return "Mining";
	}

	public function getMenuItemEvery5Levels() : Item{
		return VanillaItems::DIAMOND_HELMET();
	}

	public function getMenuLore(AetherPlayer $player, int $level) : array{
		$previousExtraDamage = ($level - 1)  * 4;
		$current = $level * 4;

		$lore = [
			"§r§7Rewards:",
			"§r§e Spelunker " . CustomEnchantUtils::roman($level),
			"§r§f  Grants §a+§8$previousExtraDamage §7-> §a$current §6Mining",
			"§r§f  §6Fortune§f, which increases your chance for",
			"§r§f  multiple ore drops",
			"§r§f  §8+§a1 §aDefense",
			"§r§f  §8+§g" . number_format($this->getEssenceGain($level)) . " Essence",
		];


		return $lore;
	}

	public function getBaseItem(AetherPlayer $player) : Item{
		$item = VanillaItems::STONE_PICKAXE();
		$level = $player->getSkillData()->getSkillLevel(self::id());
		$xp = $player->getSkillData()->getSkillXp(self::id());

		$progress = 100 / $this->getXpForLevel($level+1) * $xp;


		$item->setCustomName("§r§aMining Skill");

		$lore = [
			"§r§7Dive into deep caves and find",
			"§r§7rare ores and valuable materials",
			"§r§7to earn Mining XP!",
			"§r",
		];

		if($level < 50){
			$lore[] = "§r§7Progress to level " . CustomEnchantUtils::roman($level+1) . ": §e" . number_format($progress, 2) . "%";
			$lore[] = "§r§e" . number_format($xp) . "§6/§e" . number_format($this->getXpForLevel($level+1));
			$lore[] = "§r";
		}

		$skillStat = $level * 4;

		$lore[] = "§r§eSpelunker " . CustomEnchantUtils::roman($level);
		$lore[] = "§r§f Grants §a+$skillStat §6Mining Fortune§f,";
		$lore[] = "§r§f which increases your chance for";
		$lore[] = "§r§f multiple ore drops";

		$item->setLore($lore);

		return $item;
	}

	public static function getXpDropByBlock(Block $block): float {
		return match($block->getId()) {
			BlockLegacyIds::EMERALD_ORE => 13.5,
			BlockLegacyIds::DIAMOND_ORE => 15,
			BlockLegacyIds::STONE, BlockLegacyIds::COBBLESTONE => 1.5,
			BlockLegacyIds::GOLD_ORE => 9,
			BlockLegacyIds::COAL_ORE => 6,
			BlockLegacyIds::IRON_ORE => 7.5,
			BlockLegacyIds::GRAVEL => 1,
			BlockLegacyIds::LAPIS_ORE, BlockLegacyIds::REDSTONE_ORE => 10.5,
			default => 0
		};
	}
}