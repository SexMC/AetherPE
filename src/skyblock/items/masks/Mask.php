<?php

declare(strict_types=1);

namespace skyblock\items\masks;

use pocketmine\block\Block;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\object\HatEntity;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\Main;
use skyblock\misc\warpspeed\IWarpSpeed;
use skyblock\misc\warpspeed\WarpSpeedHandler;
use skyblock\player\AetherPlayer;

abstract class Mask {

	public function __construct(){ }

	public abstract function getDesiredEvents(): array;

	public static abstract function getItem(): Item;

	public static abstract function getName(): string;

	public abstract function getBlock(): Block;

	public abstract function getFormat(): string;

	public abstract function tryCall(Event $event): void;

	public abstract function onActivate(Player $player, Event $event): void;

	public abstract function getRarity(): Rarity;

	/**
	 * @param Player $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool if returned false it'll be cancelled
	 */
	public abstract function onWear(Player $player, Item $old, Item $new): bool;

	/**
	 * @param Player $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool if returned false it'll be cancelled
	 */
	public abstract function onTakeOff(Player $player, Item $old, Item $new): bool;

	public static function addNameTag(Item $item): void {
		$item->getNamedTag()->setString("mask", static::getName());
		if($item instanceof SkyblockItem){
			$item->resetLore();
		}
	}

	public function onInternalWear(Player $player, Item $old, Item $new): void {
		if(!WarpSpeedHandler::getInstance()->isUnlocked(IWarpSpeed::MASKS)){
			WarpSpeedHandler::getInstance()->sendMessage($player);
			return;
		}

		if(isset(MasksHandler::getInstance()->cachedMasks[$player->getName()])){
			$e = MasksHandler::getInstance()->cachedMasks[$player->getName()];
			if(!$e->isClosed()){
				MasksHandler::getInstance()->cachedMasks[$player->getName()]->flagForDespawn();
			}
		}

		$e = new HatEntity($player->getLocation(), null, $player, $this->getBlock());
		$e->spawnToAll();
		MasksHandler::getInstance()->cachedMasks[$player->getName()] = $e;

		$player->sendMessage(Main::PREFIX . "Activated " . static::getFormat() . " §7mask");

		$this->onWear($player, $old, $new);
	}

	public function onInternalTakeOff(Player $player, Item $old, Item $new): void {
		assert($player instanceof AetherPlayer);


		MasksHandler::getInstance()->set($player->getName(), null);
		if(isset(MasksHandler::getInstance()->cachedMasks[$player->getName()])){
			$e = MasksHandler::getInstance()->cachedMasks[$player->getName()];
			if(!$e->isClosed()){
				MasksHandler::getInstance()->cachedMasks[$player->getName()]->flagForDespawn();
			}
		}

		$this->onTakeOff($player, $new, $new);
	}

	public function getPriority(): int {
		return EventPriority::NORMAL;
	}

	public function listenToCancelled(): bool {
		return false;
	}

}