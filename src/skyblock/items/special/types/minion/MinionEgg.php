<?php

declare(strict_types=1);

namespace skyblock\items\special\types\minion;

use pocketmine\entity\Location;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\farmer\FarmerMinion;
use skyblock\entity\minion\farmer\FarmerType;
use skyblock\entity\minion\farmer\FarmingMinionLevelInitializer;
use skyblock\entity\minion\fishing\FishingMinion;
use skyblock\entity\minion\fishing\FishingMinionLevelInitializer;
use skyblock\entity\minion\fishing\FishingType;
use skyblock\entity\minion\foraging\ForagerType;
use skyblock\entity\minion\foraging\ForagingMinion;
use skyblock\entity\minion\foraging\ForagingMinionLevelInitializer;
use skyblock\entity\minion\miner\MinerMinion;
use skyblock\entity\minion\miner\MinerMinionLevelInitializer;
use skyblock\entity\minion\miner\MinerType;
use skyblock\entity\minion\MinionHandler;
use skyblock\entity\minion\slayer\SlayerMinion;
use skyblock\entity\minion\slayer\SlayerMinionLevelInitializer;
use skyblock\entity\minion\slayer\SlayerType;
use skyblock\items\special\SpecialItem;
use skyblock\misc\recipes\RecipesHandler;

class MinionEgg extends SpecialItem {

	public static function getItem(): Item {
		$item = VanillaItems::PAPER();
		$item->setCustomName("use the minion commands");

		return $item;
	}

	public static function getAllLevelItemsByEggItem(Item $egg): array {
		$type = $egg->getNamedTag()->getString("type");
		$found = false;
		$init = false;

		if(MinionHandler::getInstance()->getMinerType($type)){
			$found = MinionHandler::getInstance()->getMinerType($type);
			$init = MinerMinionLevelInitializer::getInstance();
		}

		if(MinionHandler::getInstance()->getFarmerType($type)){
			$found = MinionHandler::getInstance()->getFarmerType($type);
			$init = FarmingMinionLevelInitializer::getInstance();

		}

		if(MinionHandler::getInstance()->getSlayerType($type)){
			$found = MinionHandler::getInstance()->getSlayerType($type);
			$init = SlayerMinionLevelInitializer::getInstance();

		}

		if(MinionHandler::getInstance()->getForagerType($type)){
			$found = MinionHandler::getInstance()->getForagerType($type);
			$init = ForagingMinionLevelInitializer::getInstance();
		}

		if(MinionHandler::getInstance()->getFishingType($type)){
			$found = MinionHandler::getInstance()->getFishingType($type);
			$init = FishingMinionLevelInitializer::getInstance();

		}


		if(!$found) return [];


		$arr = [];
		for($i = 1; $i <= 11; $i++){
			$m = $init->createNewMinion($i, $found, "console");
			$m->close();

			$arr[] = RecipesHandler::getInstance()->getRecipeByItem($m->getEggItem());
		}

		return $arr;
	}

	public static function getFarmingEggItem(int $level, FarmerType $type, string $owner = "console", int $resources = 0): Item {
		$minion = FarmingMinionLevelInitializer::getInstance()->createNewMinion($level, $type, $owner);
		$minion->close();

		return $minion->getEggItem();
	}

	public static function getMiningEggItem(int $level, MinerType $type, string $owner = "console", int $resources = 0): Item {
		$minion = MinerMinionLevelInitializer::getInstance()->createNewMinion($level, $type, $owner);
		$minion->close();

		return $minion->getEggItem();
	}

	public static function getCombatEggItem(int $level, SlayerType $type, string $owner = "console", int $resources = 0): Item {
		$minion = SlayerMinionLevelInitializer::getInstance()->createNewMinion($level, $type, $owner);
		$minion->close();

		return $minion->getEggItem();
	}

	public static function getForagerEggItem(int $level, ForagerType $type, string $owner = "console", int $resources = 0): Item {
		$minion = ForagingMinionLevelInitializer::getInstance()->createNewMinion($level, $type, $owner);
		$minion->close();

		return $minion->getEggItem();
	}

	public static function getFishingEggItem(int $level, FishingType $type, string $owner = "console", int $resources = 0): Item {
		$minion = FishingMinionLevelInitializer::getInstance()->createNewMinion($level, $type, $owner);
		$minion->close();

		return $minion->getEggItem();
	}



	public function onUse(Player $player, Event $event, Item $item) : void{
		if($event instanceof Cancellable){
			$event->cancel();
		}

		if($event instanceof PlayerInteractEvent){
			$event->cancel();

			$item = $event->getItem();
			$item->pop();
			$player->getInventory()->setItemInHand($item);

			$nbt = $item->getNamedTag();
			$nbt->setString("owner", $event->getPlayer()->getName());

			$spawnPos = $event->getBlock()->getPosition()->addVector($event->getTouchVector());
			$type = $nbt->getString("type");

			if(MinionHandler::getInstance()->getMinerType($type)){
				(new MinerMinion(Location::fromObject($spawnPos, $player->getWorld()), $nbt))->spawnToAll();
				return;
			}

			if(MinionHandler::getInstance()->getFarmerType($type)){
				(new FarmerMinion(Location::fromObject($spawnPos, $player->getWorld()), $nbt))->spawnToAll();
				return;
			}

			if(MinionHandler::getInstance()->getSlayerType($type)){
				(new SlayerMinion(Location::fromObject($spawnPos, $player->getWorld()), $nbt))->spawnToAll();
				return;
			}

			if(MinionHandler::getInstance()->getForagerType($type)){
				(new ForagingMinion(Location::fromObject($spawnPos, $player->getWorld()), $nbt))->spawnToAll();
				return;
			}

			if(MinionHandler::getInstance()->getFishingType($type)){
				(new FishingMinion(Location::fromObject($spawnPos, $player->getWorld()), $nbt))->spawnToAll();
				return;
			}
		}
	}

	public static function getItemTag() : string{
		return "minionegg";
	}
}