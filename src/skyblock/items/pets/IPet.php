<?php

declare(strict_types=1);

namespace skyblock\items\pets;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\itemattribute\ItemAttributeHolder;

interface IPet {

	const TAG_RARITY = "tag_pet_rarity";
	const TAG_LEVEL = "tag_pet_level";
	const TAG_UUID = "tag_pet_uuid";
	const TAG_ID = "tag_pet_id";
	const TAG_XP = "tag_pet_xp";
	const TAG_CANDY_USED = "tag_candy_used";

	const RARITY_UNCOMMON = 1;
	const RARITY_COMMON = 2;
	const RARITY_RARE = 3;
	const RARITY_EPIC = 4;
	const RARITY_LEGENDARY = 5;

	const TYPE_FARMING = 1;
	const TYPE_FISHING = 2;
	const TYPE_COMBAT = 3;
	const TYPE_FORAGING = 4;


	public function getSkillType(): int; //farming, combat, foraging, fishing, ..
	public function getName(): string;
	public function getIdentifier(): string;
	public function getColor(int $rarity): string;
	public function getAbilityText(int $level, int $rarity): array;
	public function getDesiredEvents(): array;
	public function getMaxLevel(): int;
	public function canOnlyBeUsedInPvP(): bool;
	public function onUse(Player $player, PetInstance $instance, Event $event): void;
	public function getEntityId(): string;
	public function applyStats(ItemAttributeHolder $item, int $level, int $rarity): void; //this is where you apply the pve stats such as health, intelligence etc when the item is being built in pet->getItem()

	public function tryCall(Event $event): void;

	public function onActivate(Player $player, PetInstance $pet): bool;
	public function onDeActivate(Player $player, PetInstance $pet): bool;
}