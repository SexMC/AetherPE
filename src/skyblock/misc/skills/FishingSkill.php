<?php

declare(strict_types=1);

namespace skyblock\misc\skills;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\CustomEnchantUtils;

class FishingSkill extends Skill {
	protected function onLevelUp(AetherPlayer $player, Session $session, int $old, int $new) : void{
		$session->setHealth($session->getHealth() + 2);
		$session->increaseEssence($this->getEssenceGain($new));

		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + 2);


		$player->sendMessage(array(
            "§r",
            "§r§l§6         » FISHING SKILL LEVEL UP! « ",
            "§r§e            You are now level $new at Fishing!",
            "§r",
        ));
	}

	public static function id() : string{
		return "Fishing";
	}

	public function getMenuItemEvery5Levels() : Item{
		return VanillaBlocks::PRISMARINE()->asItem();
	}

	public function getMenuLore(AetherPlayer $player, int $level) : array{
		$previousTreasure = ($level - 1)  * 0.4;
		$currentTreasure = $level * 0.4;

		$previousBoss = ($level - 1)  * 0.005;
		$currentBoss = $level * 0.005;

		$lore = [
			"§r§7Rewards:",
			"§r§e Treasure Hunter " . CustomEnchantUtils::roman($level),
			"§r§f  §a§8$previousTreasure §7-> §a$currentTreasure% §fTreasure Chance",
			"§r§f  §a§8$previousBoss §7-> §a$currentBoss% §fSea Boss Chance",
			"§r§f  §8+§a2 §cHealth",
			"§r§f  §8+§g" . number_format($this->getEssenceGain($level)) . " Essence",
		];


		return $lore;
	}

	public function getBaseItem(AetherPlayer $player) : Item{
		$item = VanillaItems::FISHING_ROD();
		$level = $player->getSkillData()->getSkillLevel(self::id());
		$xp = $player->getSkillData()->getSkillXp(self::id());

		$progress = 100 / $this->getXpForLevel($level+1) * $xp;


		$item->setCustomName("§r§aFishing Skill");

		$lore = [
			"§r§7Visit your local pond to fish",
			"§r§7and earn Fishing XP!",
			"§r",
		];

		if($level < 50){
			$lore[] = "§r§7Progress to level " . CustomEnchantUtils::roman($level+1) . ": §e" . number_format($progress, 2) . "%";
			$lore[] = "§r§e" . number_format($xp) . "§6/§e" . number_format($this->getXpForLevel($level+1));
			$lore[] = "§r";
		}


		$lore[] = "§r§eTreasure Hunter " . CustomEnchantUtils::roman($level);
		$lore[] = "§r§f §a" . number_format(0.4*$level) . "% §fTreasure Loot Chance";
		$lore[] = "§r§f §a" . number_format(0.005*$level) . "% §fSea Boss Spawn Egg chance";

		$item->setLore($lore);

		return $item;
	}
}