<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\ItemEditor;
use skyblock\player\AetherPlayer;

abstract class SpecialWeapon {

	const TAG_SPECIAL_TOOL = "tag_special_weapon";

	public abstract function getDesiredEvents(): array;

	public static abstract function getItem(): Item;

	public static abstract function getName(): string;

	public abstract function getExtraLore(): array;

	public function tryCall(Event $event): void {}

	public abstract function onActivate(Player $player, Event $event): void;

	public function onHold(AetherPlayer $player, Item $item): void {}
	public function onDeHold(AetherPlayer $player, Item $item): void {}

	public function getPriority(): int {
		return EventPriority::HIGH;
	}

	public static function addNametag(Item $item): void {
		$item->getNamedTag()->setString(self::TAG_SPECIAL_TOOL, static::getName());

		ItemEditor::updateCosmetics($item);
	}


}