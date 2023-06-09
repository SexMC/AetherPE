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

class ForagingSkill extends Skill {

	private static array $blocks = [];

	public function onRegister() : void{
		$blocks = [
			6 => VanillaBlocks::OAK_LOG(),
			8 => VanillaBlocks::BIRCH_LOG(),
			10 => VanillaBlocks::SPRUCE_LOG(),
			12 => VanillaBlocks::DARK_OAK_LOG(),
			14 => VanillaBlocks::ACACIA_LOG(),
			16 => VanillaBlocks::JUNGLE_LOG(),
		];

		/**
		 * @var int $k
		 * @var Block  $v
		 */
		foreach($blocks as $k => $v){
			self::$blocks[$v->getIdInfo()->getBlockId() . $v->getIdInfo()->getVariant()] = $k;
			self::$blocks[BlockLegacyIds::WOOD . $v->getIdInfo()->getVariant()] = $k; //this wood is some special thing idk
		}
	}

	protected function onLevelUp(AetherPlayer $player, Session $session, int $old, int $new) : void{
		$session->setFarmingFortune($session->getFarmingFortune() + 4); //increase both values here so the cache gets also updated
		$player->getPveData()->setFarmingFortune($player->getPveData()->getFarmingFortune() + 4); //increase both values here so the cache gets also updated

		$session->setStrength($session->getStrength() + 1);
		$player->getPveData()->setStrength($session->getStrength() + 1); //increase both values here so the cache gets also updated

		$session->increaseEssence($this->getEssenceGain($new));


        $player->sendMessage(array(
            "§r",
            "§r§l§6         » FORAGING SKILL LEVEL UP! « ",
            "§r§e           You are now level $new at Foraging!",
            "§r",
        ));
	}

	public static function id() : string{
		return "Foraging";
	}

	public function getMenuItemEvery5Levels() : Item{
		return VanillaBlocks::OAK_LOG()->asItem();
	}

	public function getMenuLore(AetherPlayer $player, int $level) : array{
		$previousExtraDamage = ($level - 1)  * 4 . "%";
		$current = $level * 4 . "%";

		$lore = [
			"§r§7Rewards:",
			"§r§e Logger" . CustomEnchantUtils::roman($level),
			"§r§f  Grants §8$previousExtraDamage -> §a$current §6Foraging",
			"§r§f  fortune§f, which increases your chance",
			"§r§f  for multiple ore drops.",
			"§r§f  §8+1 Strength",
			"§r§f  §8+§g" . number_format($this->getEssenceGain($level)) . " Essence",
		];

		return $lore;
	}

	public function getBaseItem(AetherPlayer $player) : Item{
		$item = VanillaItems::IRON_AXE();
		$level = $player->getSkillData()->getSkillLevel(self::id());
		$xp = $player->getSkillData()->getSkillXp(self::id());

		$progress = 100 / $this->getXpForLevel($level+1) * $xp;


		$item->setCustomName("§r§aForaging Skill");

		$lore = [
			"§r§7Cut trees and forage for other",
			"§r§7plant to earn Foraging XP!",
			"§r",
		];

		if($level < 50){
			$lore[] = "§r§7Progress to level " . CustomEnchantUtils::roman($level+1) . ": §e" . number_format($progress, 2) . "%";
			$lore[] = "§r§e" . number_format($xp) . "§6/§e" . number_format($this->getXpForLevel($level+1));
			$lore[] = "§r";
		}

		$skillStat = number_format(4 * $level);

		$lore[] = "§r§eLogger " . CustomEnchantUtils::roman($level);
		$lore[] = "§r§f Grants §a+$skillStat §6Foraging Fortune§f,";
		$lore[] = "§r§f which increases your chance for";
		$lore[] = "§r§f multiple log drops.";

		

		$item->setLore($lore);

		return $item;
	}

	public static function getXpDropByBlock(Block $block): float {
		$key = $block->getIdInfo()->getBlockId() . $block->getIdInfo()->getVariant();

		return self::$blocks[$key] ?? 0;
	}
}