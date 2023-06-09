<?php

declare(strict_types=1);

namespace skyblock\items\masks\types;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Skin;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\masks\Mask;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\misc\skills\FarmingSkill;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;

//don't know what this is? possibly this is https://wiki.hypixel.net/Farmer_Boots

class FarmerMask extends Mask {

	public function getRarity() : Rarity{
		return Rarity::common();
	}

	public function getDesiredEvents() : array{
		return [];
	}

	public static function getItem() : Item{
		$item = SkyblockItems::MASK_ITEM();
		$item->setMask(self::getName());

		$item->setCustomName("§r§l§fMask \"§r§a§lFarmer Mask§f§l\"");
		$item->getProperties()->setDescription([
			"§r§b§a+2 " . PveUtils::getDefense() . " §band, §a+4 " . PveUtils::getSpeed(),
			"§r§bfor every Farming Skill Level you have.",
			"§r",
			"§r§7§oAttach this mask to any helmet",
			"§r§7§oto give it a visual override!",
			"§r",
			"§r§7To equip, place this mask on a helmet.",
			"§r§7To remove, use /removemask while holding the helmet.",
		]);

		self::addNameTag($item);

		return $item;
	}

	public static function getName() : string{
		return "farmermask";
	}

	public function getBlock() : Block{
		return VanillaBlocks::CARVED_PUMPKIN();
	}

	public function getFormat() : string{
		return "§r§a§lFarmer Mask";
	}

	public function tryCall(Event $event) : void{}

	public function onActivate(Player $player, Event $event) : void{}

	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onWear(Player $player, Item $old, Item $new) : bool{
		$level = $player->getSkillData()->getSkillLevel(FarmingSkill::id());
		$player->getPveData()->setDefense($player->getPveData()->getDefense() + 2 * $level);
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() + 4 * $level);

		return true;
	}

	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onTakeOff(Player $player, Item $old, Item $new) : bool{
		$level = $player->getSkillData()->getSkillLevel(FarmingSkill::id());
		$player->getPveData()->setDefense($player->getPveData()->getDefense() - 2 * $level);
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() - 4 * $level);
		return true;
	}
}