<?php

declare(strict_types=1);

namespace skyblock\blocks\custom;

use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;

interface ICustomBlock {

	public function onLoad(CustomBlockTile $tile): void;
	public function onPlace(Player $player, Position $pos): bool; //returns bool/false to whether cancel the event or not
	public function onBreak(Player $player, CustomBlockTile $tile): bool;//returns bool/false to whether cancel the event or not
	public function onInteract(Player $player, CustomBlockTile $tile, PlayerInteractEvent $event): void;


	public function getMineCost(): int;

	public function getLimitPerChunk(): int;

	public function getIslandValue(): int;

	public function giveRewardSeconds(): int; //every how many seconds it gives rewards, e.g. one week

	public function giveRewards(Player $player, CustomBlockTile $tile): void;
	public function hasAvailableRewards(CustomBlockTile $tile): bool;

	public static function getIdentifier(): string;

	public function getNameTag(CustomBlockTile $tile): string;

	public function getDesiredEvents(): array;
	public function hasEvent(Event $event): bool;
	public function onEvent(CustomBlockTile $tile, Event $event);
}