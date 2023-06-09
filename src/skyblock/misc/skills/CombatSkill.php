<?php

declare(strict_types=1);

namespace skyblock\misc\skills;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\RayTraceResult;
use pocketmine\player\Player;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\CustomEnchantUtils;

class CombatSkill extends Skill {
	protected function onLevelUp(AetherPlayer $player, Session $session, int $old, int $new) : void{
		$player->getSkillData()->increaseSkillStat(ISkill::SKILL_STAT_EXTRA_DAMAGE, 4);

		$session->setCritChance($session->getCritChance() + 0.5);
		$player->getPveData()->setCritChance($player->getPveData()->getCritChance() + 0.5);

		$session->increaseEssence($this->getEssenceGain($new));


        $player->sendMessage(array(
            "§r",
            "§r§l§6         » COMBAT SKILL LEVEL UP! « ",
            "§r§e           You are now level $new at Combat!",
            "§r",
        ));


		//TODO:
		if($new === 6){
			//unlocks ability: auto pickup mob & block drops in pve

		}

		if($new === 12){
			//accesss to the end
		}
	}

	public static function id() : string{
		return "Combat";
	}

	public function getMenuItemEvery5Levels() : Item{
		return VanillaItems::DIAMOND_HELMET();
	}

	public function getMenuLore(AetherPlayer $player, int $level) : array{
		$previousExtraDamage = ($level - 1)  * 4 . "%";
		$current = $level * 4 . "%";

		$lore = [
			"§r§7Rewards:",
			"§r§e Warrior " . CustomEnchantUtils::roman($level),
			"§r§f  Deal §8$previousExtraDamage -> §a$current more",
			"§r§f  damage to mobs.",
			"§r§f  §8+§a0.5% §9Crit Chance",
			"§r§f  §8+§g" . number_format($this->getEssenceGain($level)) . " Essence",
		];

		if($level === 6){
			$lore[] = "§r§f  §8+§aAuto-pickup mob and block drops";
		}

		if($level === 11){
			$lore[] = "§r§f  §8+§aAccess to §bThe End";
		}

		return $lore;
	}

	public function getBaseItem(AetherPlayer $player) : Item{
		$item = VanillaItems::STONE_SWORD();
		$level = $player->getSkillData()->getSkillLevel(self::id());
		$xp = $player->getSkillData()->getSkillXp(self::id());

		$progress = 100 / $this->getXpForLevel($level+1) * $xp;

		
		$item->setCustomName("§r§aCombat Skill");

		$lore = [
			"§r§7Fight mobs and special bosses to",
			"§r§7earn Combat XP!",
			"§r",
		];

		if($level < 50){
			$lore[] = "§r§7Progress to level " . CustomEnchantUtils::roman($level+1) . ": §e" . number_format($progress, 2) . "%";
			$lore[] = "§r§e" . number_format($xp) . "§6/§e" . number_format($this->getXpForLevel($level+1));
			$lore[] = "§r";
		}

		//TODO: implement skill stat (i think imma change it to actual session data and not into skill data)
		$skillStat = number_format($player->getSkillData()->getSkillStat(self::SKILL_STAT_EXTRA_DAMAGE));

		$lore[] = "§r§eWarrior " . CustomEnchantUtils::roman($level);
		$lore[] = "§r§f Deal §a{$skillStat}%§f more damage to mobs";



		$item->setLore($lore);

		return $item;
	}
}