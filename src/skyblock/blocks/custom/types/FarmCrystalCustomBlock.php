<?php

declare(strict_types=1);

namespace skyblock\blocks\custom\types;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\blocks\custom\CustomBlock;
use skyblock\blocks\custom\CustomBlockTile;

class FarmCrystalCustomBlock extends CustomBlock {

	protected static function buildItem() : Item{
		$item = VanillaBlocks::BREWING_STAND()->asItem();
		$item->setCustomName("§r§l§eFarm Crystal");
		$item->setLore([
			"§r§eCost to Mine: §r§f$5,000,000",
			"§r§7(Only after 5 minutes after being placed)",
			"§r§eIsland Value Worth: §r§f1,000,000",
			"§r§eTotal Per Chunk: §r§f1",
			"§r",
			"§r§7Farming minions in the same chunk",
			"§r§7will grant them a 10% speed boost",
			"§r",
			"§r§7Place custom block to use.",
		]);

		return $item;
	}

	public function onLoad(CustomBlockTile $tile) : void{}

	public function getMineCost() : int{
		return 5000000;
	}

	public function getLimitPerChunk() : int{
		return 1;
	}

	public function getIslandValue() : int{
		return 1000000;
	}

	public static function getIdentifier() : string{
		return "FarmCrystal";
	}

	public function getNameTag(CustomBlockTile $tile) : string{
		return "§r§l§eFarm Crystal";
	}



	public function giveRewardSeconds() : int{
		return -1;
	}

	public function giveRewards(Player $player, CustomBlockTile $tile) : void{}

	public function hasAvailableRewards(CustomBlockTile $tile) : bool{
		return false;
	}

	public function getDesiredEvents() : array{
		return [];
	}

	public function onEvent(CustomBlockTile $tile, Event $event){}
}