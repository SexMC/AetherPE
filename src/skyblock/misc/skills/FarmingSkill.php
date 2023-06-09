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

class FarmingSkill extends Skill {
	protected function onLevelUp(AetherPlayer $player, Session $session, int $old, int $new) : void{
		$session->setFarmingFortune($session->getFarmingFortune() + 4);
		$session->setHealth($session->getHealth() + 2);

		$session->increaseEssence($this->getEssenceGain($new));

		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + 2);
		$player->getPveData()->setFarmingFortune($player->getPveData()->getFarmingFortune() + 4);


        $player->sendMessage(array(
            "§r",
            "§r§l§6         » FARMING SKILL LEVEL UP! « ",
            "§r§e              You are now level $new at Farming!",
            "§r",
        ));
	}

	public static function id() : string{
		return "Farming";
	}

	public function getMenuItemEvery5Levels() : Item{
			return VanillaBlocks::HAY_BALE()->asItem();
	}

	public function getMenuLore(AetherPlayer $player, int $level) : array{
		$previousExtraDamage = ($level - 1)  * 4;
		$current = $level * 4;

		$lore = [
			"§r§7Rewards:",
			"§r§e Farmhand " . CustomEnchantUtils::roman($level),
			"§r§f  Grants §a+§8$previousExtraDamage §7-> §a$current §6Farming",
			"§r§f  §6Fortune§f, which increases your chance for",
			"§r§f  multiple crops",
			"§r§f  §8+§a2 §cHealth",
			"§r§f  §8+§g" . number_format($this->getEssenceGain($level)) . " Essence",
		];


		return $lore;
	}

	public function getBaseItem(AetherPlayer $player) : Item{
		$item = VanillaItems::DIAMOND_HOE();
		$level = $player->getSkillData()->getSkillLevel(self::id());
		$xp = $player->getSkillData()->getSkillXp(self::id());

		$progress = 100 / $this->getXpForLevel($level+1) * $xp;


		$item->setCustomName("§r§aFarming Skill");

		$lore = [
			"§r§7Harvest crops and shear sheep to",
			"§r§7earn Farming XP",
			"§r",
		];

		if($level < 50){
			$lore[] = "§r§7Progress to level " . CustomEnchantUtils::roman($level+1) . ": §e" . number_format($progress, 2) . "%";
			$lore[] = "§r§e" . number_format($xp) . "§6/§e" . number_format($this->getXpForLevel($level+1));
			$lore[] = "§r";
		}

		$skillStat = number_format(4 * $level);

		$lore[] = "§r§eFarmhand " . CustomEnchantUtils::roman($level);
		$lore[] = "§r§f Grants §a+$skillStat §6Farming Fortune§f,";
		$lore[] = "§r§f which increases your chance for";
		$lore[] = "§r§f multiple crops.";

		$item->setLore($lore);

		return $item;
	}




	private static array $meta4 = [BlockLegacyIds::PUMPKIN, BlockLegacyIds::MELON_BLOCK];

	private static array $meta3 = [BlockLegacyIds::NETHER_WART_PLANT];

	private static array $meta7 = [BlockLegacyIds::WHEAT_BLOCK, BlockLegacyIds::CARROT_BLOCK, BlockLegacyIds::POTATO_BLOCK, BlockLegacyIds::BEETROOT_BLOCK];

	public static function getXpByCrop(int $id): int {
		return match($id) {
			BlockLegacyIds::PUMPKIN => 10,
			BlockLegacyIds::MELON_BLOCK => 10,
			BlockLegacyIds::NETHER_WART_PLANT => 25,
			BlockLegacyIds::WHEAT_BLOCK => 5,
			BlockLegacyIds::CARROT_BLOCK => 6,
			BlockLegacyIds::POTATO_BLOCK => 7,
			BlockLegacyIds::BEETROOT_BLOCK => 8,
		};
	}

	public static function isCropBlock(int $id, int $meta): bool {
		if(in_array($id, self::$meta7) && $meta === 7){
			return true;
		}

		if(in_array($id, self::$meta4) && $meta === 4){
			return true;
		}

		return in_array($id, self::$meta3) && $meta === 3;
	}
}