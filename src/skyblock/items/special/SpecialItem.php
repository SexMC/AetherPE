<?php

declare(strict_types=1);

namespace skyblock\items\special;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class SpecialItem {

	public const TAG_SPECIAL_ITEM = "SpecialItem";
	public const TAG_SPECIAL_ITEM_UNIQUE_ID = "SpecialItemUniqueId";

	public function onUse(Player $player, Event $event, Item $item): void{}

	public abstract static function getItemTag(): string;

	protected static function addNameTag(Item $item): Item {
		$item->getNamedTag()->setString(self::TAG_SPECIAL_ITEM, static::getItemTag());

		return $item;
	}

	protected static function addUniqueIdNameTag(Item $item): void {
		$item->getNamedTag()->setString(self::TAG_SPECIAL_ITEM_UNIQUE_ID, uniqid(static::getItemTag()));
	}

	public static function getSpecialItem(Item $item): ?SpecialItem {
		return SpecialItemHandler::getItem($item->getNamedTag()->getString(self::TAG_SPECIAL_ITEM, ""));
	}
}