<?php

declare(strict_types=1);

namespace skyblock\items\special\types;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\items\special\SpecialItem;
use skyblock\utils\EntityUtils;

class SpawnerItem extends SpecialItem {
	const WARDEN = "minecraft:warden";
	const PIGLIN_BRUTE = "minecraft:piglin_brute";

	public static function getItem(string $entityId): Item {
		$entityName = EntityUtils::getEntityNameFromID($entityId);

		$item = ItemFactory::getInstance()->get(ItemIds::MOB_SPAWNER);
		$item->setCustomName("§r§f§lSpawner \"§r§7{$entityName}§f§l\"");
		$item->setLore(array_merge([
			"§r§7Place this spawner in your island!",
			"§r§7This spawns the mob in the chunk the spawner",
			"§r§7is in, Use /showchunks to view Chunk Borders",
			"§r",
			"§r§l§fInformation",
		], self::getInformation($entityId)));


		$item->getNamedTag()->setString("entityID", $entityId);
		$item->setCustomBlockData((new CompoundTag())->setString("eid", $entityId));
		self::addNameTag($item);

		return $item;
	}

	public static function getCleanItem(string $entityId): Item {
		$entityName = EntityUtils::getEntityNameFromID($entityId);

		$item = ItemFactory::getInstance()->get(ItemIds::MOB_SPAWNER);
		$item->setCustomName("§r§f§lSpawner \"§r§7{$entityName}§f§l\"");


		return $item;
	}

	public static function getItemTag() : string{
		return "Spawner";
	}

	public static function getInformation(string $entityId): array {
		switch($entityId){
			case EntityIds::CHICKEN:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Chicken Meat, Feathers)",
					"§r§7(12$ each, 8$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lLOW-TIER SPAWNER",
				];
			case EntityIds::COW:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Raw Beef, Leather)",
					"§r§7(35$ each, 42$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lLOW-TIER SPAWNER",
				];
			case EntityIds::ZOMBIE:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Rotten Flesh, Poisonous Potato)",
					"§r§7(64$ each, 54$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lMID-TIER SPAWNER",
				];
			case EntityIds::SKELETON:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Bones, Arrows)",
					"§r§7(78$ each, 34$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lMID-TIER SPAWNER",
				];
			case EntityIds::BLAZE:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Blaze Rod)",
					"§r§7(92$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lMID-TIER SPAWNER",
				];
			case EntityIds::ZOMBIE_PIGMAN:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Gold Block)",
					"§r§7(306$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to water",
					"§r",
					"§r§6§lLEGENDARY",
				];
			case EntityIds::MAGMA_CUBE:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Diamond Block)",
					"§r§7(387$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to water",
					"§r",
					"§r§6§lLEGENDARY",
				];
			case EntityIds::GUARDIAN:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Emerald Block)",
					"§r§7(504$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§6§lLEGENDARY",
				];
			case EntityIds::TURTLE:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Turtle Scute)",
					"§r§7(1,200$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§6§lLEGENDARY",
				];
			case EntityIds::IRON_GOLEM:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Iron Block)",
					"§r§7(198$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lMID-TIER SPAWNER",
				];
			case EntityIds::SLIME:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Slime Balls)",
					"§r§7(102$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lMID-TIER SPAWNER",
				];
			case EntityIds::ZOGLIN:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Nautilus Shell)",
					"§r§7(1,600$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lEXOTIC SPAWNER",
				];
			case self::PIGLIN_BRUTE:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Fermented Spider Eye)",
					"§r§7(1,400$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lEXOTIC SPAWNER",
				];
			case EntityIds::RAVAGER:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Fire Charge)",
					"§r§7(1,800$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lEXOTIC SPAWNER",
				];
			case self::WARDEN:
				return [
					"§r§7Place this spawner in a chunk",
					"§r§7to start spawning chickens!",
					"§r§7You can view chunks with /showchunks",
					"§r",
					"§r§7§lInformation",
					"§r§8§l* §r§7Drops",
					"§r§7(Heart Of The Sea)",
					"§r§7(2,000$ each)",
					"§r§8§l* §r§7Other",
					"§r§7Dies to lava",
					"§r",
					"§r§f§lEXOTIC SPAWNER",
				];
		}

		return [];
	}
}