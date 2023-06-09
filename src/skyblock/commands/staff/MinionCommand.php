<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\DefinedStringArgument;
use skyblock\entity\minion\farmer\FarmerMinion;
use skyblock\entity\minion\fishing\FishingMinion;
use skyblock\entity\minion\foraging\ForagingMinion;
use skyblock\entity\minion\miner\MinerMinion;
use skyblock\entity\minion\miner\MinerType;
use skyblock\entity\minion\MinionHandler;
use skyblock\entity\minion\slayer\SlayerMinion;
use skyblock\entity\minion\slayer\SlayerType;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\utils\Utils;

class MinionCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.commands.miner.give");
		
		$this->registerArgument(0, new DefinedStringArgument(array_merge(MinionHandler::getInstance()->getAllMinerTypes(), MinionHandler::getInstance()->getAllFarmerTypes(), MinionHandler::getInstance()->getAllSlayerTypes(), MinionHandler::getInstance()->getAllForagerTypes(), MinionHandler::getInstance()->getAllFishingTypes()), "type"));
		$this->registerArgument(1, new IntegerArgument("level", true));
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		$level = $args["level"] ?? 1;
		/** @var MinerType $type */
		$type = $args["type"];

		$nbt = new CompoundTag();
		$nbt->setString("type", $type->getName());
		$nbt->setInt("level", $level);

		$minion = null;
		if(MinionHandler::getInstance()->getMinerType($type->getName())){
			$minion = new MinerMinion($player->getLocation(), $nbt);
		}

		if(MinionHandler::getInstance()->getFarmerType($type->getName())){
			$minion = new FarmerMinion($player->getLocation(), $nbt);
		}

		if(MinionHandler::getInstance()->getSlayerType($type->getName())){
			$minion = new SlayerMinion($player->getLocation(), $nbt);
		}

		if(MinionHandler::getInstance()->getForagerType($type->getName())){
			$minion = new ForagingMinion($player->getLocation(), $nbt);
		}

		if(MinionHandler::getInstance()->getFishingType($type->getName())){
			$minion = new FishingMinion($player->getLocation(), $nbt);
		}



		if($minion === null){
			$player->sendMessage(Main::PREFIX . "Error occurred");
			return;
		}

		$item = $minion->getEggItem();
		$minion->flagForDespawn();

		Utils::addItem($player, $item);
		$player->sendMessage(Main::PREFIX . "Gave you " . $item->getCustomName());
	}
}