<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests;

use pocketmine\math\Vector3;
use pocketmine\player\Player;

interface INpcQuest {

	public function getName(): string;

	public function getIdentifier();

	public function getConversationMessages(): array; //string array

	public function getUnlockMessage(): array; //string array, this is the message sent after the conversation message is done

	public function getNotUnlockedMessage(): array; //message sent when the player has not unlocked the quest and is trying to hit the npc

	public function onComplete(Player $player): void;

	public function getOrder(): int; //the order of which npc quest comes first, starts at 0 and then 1, 2, 3 etc

	public function getNpcPosition(): Vector3; //position of the npc, used to spawn a beacon beam.
}