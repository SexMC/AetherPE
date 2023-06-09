<?php

declare(strict_types=1);

namespace skyblock\items\transactions;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class BaseTransactionHandler {

	public abstract function itemMatchesRequired(Item $item): bool;


	public abstract function onTransaction(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $itemClickedAction, SlotChangeAction $itemClickedWithAction, InventoryTransactionEvent $event): void;
}