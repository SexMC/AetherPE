<?php

declare(strict_types=1);

namespace skyblock\blocks\custom\types;

use pocketmine\block\Cactus;
use pocketmine\block\Crops;
use pocketmine\block\Sugarcane;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\blocks\custom\CustomBlock;
use skyblock\blocks\custom\CustomBlockTile;
use skyblock\items\crates\CrateHandler;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\IslandUtils;
use skyblock\utils\TimeUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class CropTotemCustomBlock extends CustomBlock {

	protected static function buildItem() : Item{
		$item = VanillaBlocks::BREWING_STAND()->asItem();
		$item->setCustomName("§r§l§aCrop Totem");
		$item->setLore([
			"§r§aCost to Mine: §r§f$50,000,000",
			"§r§7(Only after 5 minutes after being placed)",
			"§r§aIsland Value Worth: §r§f25,000,000",
			"§r§aTotal Per Chunk: §r§f1",
			"§r",
			"§r§7Crops in the same chunk as the",
			"§r§7custom block will grow faster.",
			"§r",
			"§r§7Place custom block to use.",
		]);


		return $item;
	}

	public function onLoad(CustomBlockTile $tile) : void{}

	public function getMineCost() : int{
		return 50000000;
	}

	public function getLimitPerChunk() : int{
		return 1;
	}

	public function getIslandValue() : int{
		return 25000000;
	}

	public static function getIdentifier() : string{
		return "CropTotem";
	}

	public function getNameTag(CustomBlockTile $tile) : string{
		return "§r§l§aCrop Totem";
	}



	public function giveRewardSeconds() : int{
		return -1;
	}

	public function giveRewards(Player $player, CustomBlockTile $tile) : void{}

	public function hasAvailableRewards(CustomBlockTile $tile) : bool{
		return false;
	}

	public function getDesiredEvents() : array{
		return [BlockGrowEvent::class];
	}

	public function onEvent(CustomBlockTile $tile, Event $event){
		assert($event instanceof BlockGrowEvent);

		$block = $event->getBlock();
		$world = $block->getPosition()->getWorld();
		$x = $block->getPosition()->getFloorX();
		$y = $block->getPosition()->getFloorY();
		$z = $block->getPosition()->getFloorZ();

		for($xx = $x - 2; $xx <= $x + 2; $xx++){
			for($yy = $y - 1; $yy <= $y; $yy++){
				for($zz = $z - 2; $zz <= $z + 2; $zz++){
					if(mt_rand(1, 20) === 1){
						$world->getBlockAt($xx, $yy, $zz)->onRandomTick();
					}
				}
			}
		}
	}
}