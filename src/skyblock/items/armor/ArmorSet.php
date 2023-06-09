<?php

declare(strict_types=1);

namespace skyblock\items\armor;

use Closure;
use pocketmine\event\EventPriority;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\sound\PopSound;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\rarity\Rarity;
use skyblock\items\sets\abilities\SetAbility;
use skyblock\items\SkyblockItem;
use skyblock\Main;
use skyblock\player\AetherPlayer;

abstract class ArmorSet {

	private static array $cache = [];

	private static array $sets = [];

	const PIECE_HELMET = "Helmet";
	const PIECE_CHESTPLATE = "Chestplate";
	const PIECE_LEGGINGS = "Leggings";
	const PIECE_BOOTS = "Boots";


	public function onWear(AetherPlayer $player): void {
		$identifier = ucwords(str_replace("_", " ", $this->getIdentifier()));

		$player->sendMessage("§r§l§f» §r§bNow Wearing §l§b\"§l§d{$identifier}§r§l§b\" §r§l§f«");
		$player->broadcastSound(new PopSound());
	}


	public function onTakeoff(AetherPlayer $player): void {}

	/**
	 * @return ItemAttributeInstance[]
	 */
	public abstract function getItemAttributes(string $piece): array;
	public abstract function getRarity(): Rarity;

	/**
	 * @return SetAbility[]
	 */
	public abstract function getAbilities(): array;
	public abstract function getIdentifier(): string;
	public abstract function getName(string $piece = null): string;
	public abstract function getLore(Item $item): array;
	public abstract function getPieceItems(): array;


	public static function getCache(Player $player): ?ArmorSet {
		return self::$cache[$player->getName()] ?? null;
	}


	public static function setCache(AetherPlayer $player, ?ArmorSet $set): void {
		$old = self::$cache[$player->getName()] ?? null;

		if($old !== null){
			if($set === null){
				$old->onTakeoff($player, $set);
			} elseif($set->getIdentifier() !== $old->getIdentifier()){
				$old->onTakeoff($player, $set);
				$set->onWear($player);
			}
		} elseif($set !== null) $set->onWear($player);

		self::$cache[$player->getName()] = $set;
	}

	public static function check(Player $player): void {
		$contents = $player->getArmorInventory()->getContents();

		if(count($contents) < 4){
			var_dump("sets null less than 4");
			self::setCache($player, null);
			return;
		}

		$set = [];
		foreach($contents as $content) {
			if(($string = $content->getNamedTag()->getString("armor_set", "")) !== ""){
				$set[] = $string;
			}
		}

		if(count($set) !== 4){
			var_dump("count is not 4");
			self::setCache($player, null);
			return;
		}

		$found = true;
		$eq = $set[0];

		foreach($set as $tag){
			if($tag !== $eq){
				$found = false;
			}
		}

		//var_dump(self::getSet($eq), $eq);
		if($found === true && ($set = self::getSet($eq)) !== null){
			self::setCache($player, self::getSet($eq));
			return;
		}

		self::setCache($player, null);
	}




	public static function registerSet(ArmorSet $set): void {
		self::$sets[strtolower($set->getIdentifier())] = $set;

		foreach($set->getAbilities() as $ability){
			foreach($ability->getDesiredEvents() as $event) {
				Server::getInstance()->getPluginManager()->registerEvent(
					$event,
					Closure::fromCallable([$ability, "tryCall"]),
					EventPriority::HIGH,
					Main::getInstance()
				);
			}
		}
		
		Main::debug("set: " . $set->getIdentifier() . " registered");
	}

	public static function getSet(string $setName): ?ArmorSet {
		return self::$sets[strtolower($setName)] ?? null;
	}

	/**
	 * @return ArmorSet[]
	 */
	public static function getAllSets() : array{
		return self::$sets;
	}
}