<?php

declare(strict_types=1);

namespace skyblock\blocks\custom\types;

use pocketmine\block\VanillaBlocks;
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

class GalaxyTotemCustomBlock extends CustomBlock {
	use AwaitStdTrait;

	protected static function buildItem() : Item{
		$item = VanillaBlocks::BREWING_STAND()->asItem();
		$item->setCustomName("§r§l§dGalaxy Totem");
		$item->setLore([
			"§r§dCost to Mine: §r§f$100,000,000",
			"§r§7(Only after 5 minutes after being placed)",
			"§r§dIsland Value Worth: §r§f50,000,000",
			"§r§dTotal Per Chunk: §r§fNo Limit",
			"§r",
			"§r§7This Galaxy Totem generates a",
			"§r§7random galaxy key a player can claim",
			"§r§7every week.",
			"§r",
			"§r§7Place custom block to use.",
		]);

		return $item;
	}

	public function onLoad(CustomBlockTile $tile) : void{
		Await::f2c(function() use($tile){
			while(!$tile->isClosed()){
				$this->getAndCheckAvailableKey($tile);

				yield $this->getStd()->sleep(20 * 5);
			}
		});
	}

	public function getMineCost() : int{
		return 100000000;
	}

	public function getLimitPerChunk() : int{
		return -1; //no limit
	}

	public function getIslandValue() : int{
		return 50000000;
	}

	public static function getIdentifier() : string{
		return "GalaxyTotem";
	}

	public function getNameTag(CustomBlockTile $tile) : string{
		return "§r§l§dGalaxy Totem\n§r§7(§c" . ($this->getReward($tile) ? $this->getReward($tile)->getCustomName() : "No available key") . " §r§7)";
	}


	public function getAndCheckAvailableKey(CustomBlockTile $tile): ?string {
		if($tile->getBlockData()->getString("reward", "") !== "") {
			return $tile->getBlockData()->getString("reward");
		}

		if(time() - $tile->getLastTime() >= $this->giveRewardSeconds()){
			$crates = CrateHandler::getInstance()->getAllCrates();
			$tile->getBlockData()->setString("reward", $crates[array_rand($crates)]->getName());

			$tile->updateNameTag();

			return $tile->getBlockData()->getString("reward");
		}

		return null;
	}

	public function getReward(CustomBlockTile $tile): ?Item {
		$key = $this->getAndCheckAvailableKey($tile);

		if($key !== null){
			return CrateHandler::getInstance()->getCrate($key)?->getKeyItem(1) ?? null;
		}

		return null;
	}

	public function giveRewardSeconds() : int{
		return TimeUtils::SECONDS_IN_WEEK;
	}

	public function giveRewards(Player $player, CustomBlockTile $tile) : void{
		$reward = $this->getReward($tile);

		if($reward === null) return;

		$island = IslandUtils::getIslandByWorld($player->getPosition()->getWorld());
		if(!$island->exists()) return;

		$name = self::buildItem()->getCustomName();
		$island->announce(Main::PREFIX . "§c{$player->getName()}§7 has claimed a §c{$reward->getCustomName()}§r§7 from {$name}");

		Utils::addItem($player, $reward);

		$tile->setLastTime(time());
		$tile->getBlockData()->removeTag("reward");
		$tile->updateNameTag();
	}

	public function hasAvailableRewards(CustomBlockTile $tile) : bool{
		return $this->getReward($tile) instanceof Item;
	}

	public function getDesiredEvents() : array{
		return [];
	}

	public function onEvent(CustomBlockTile $tile, Event $event){}
}