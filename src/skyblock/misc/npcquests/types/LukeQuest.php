<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests\types;

use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\misc\kits\KitHandler;
use skyblock\misc\npcquests\NpcQuest;
use skyblock\utils\Utils;

class LukeQuest extends NpcQuest {
	public function getName() : string{
		return "Talk to Luke";
	}

	public function getIdentifier(){
		return "luke";
	}

	public function onComplete(Player $player) : void{}

	public function getConversationMessages() : array{
		return [
			"§r§7(1/8) §r§l§eLuke: §r§7Oh, hey there {username}. Looking for a companion to help you out in your adventures?",
			"§r§7(2/8) §r§l§6{username}: §r§7Yeah, I've heard a lot about pets in Skyblock. Can you tell me more about them?",
			"§r§7(3/8) §r§l§eLuke: §r§7Sure thing! Pets are animal companions that provide various benefits to players. They can help you fight, mine, farm, and even collect items.",
			"§r§7(4/8) §r§l§6{username}: §r§7That sounds great! How do I get a pet?",
			"§r§7(5/8) §r§l§eLuke: §r§7You can get pets through various ways. Some pets can be obtained by completing quests, while others can be obtained through the collections. Some pets can also be bought from the pet shop in the Hub.",
			"§r§7(6/8) §r§l§6{username}: §r§7What are the benefits of having a pet?",
			"§r§7(7/8) §r§l§eLuke: §r§7Pets can provide various benefits to players, such as increasing your stats, providing extra damage in combat, or even helping you collect items. Some pets can also grant you special abilities or perks.",
			"§r§7(8/8) §r§l§eLuke: §r§7It's time you meet my friend §fLuke§7, §bHe should be close to spawn",
		];
	}

	public function getUnlockMessage() : array{
		return [
			"",
			"§r§l§6            NEW QUEST: §r§e§lTalk to Luke",
			"§r§7As Walter said, find and talk to Luke, he should be close to spawn.",
			"",
		];
	}

	public function getNotUnlockedMessage() : array{
		return [
			"§r§7(1/1) §r§l§3Luke: §r§7Back off chump, you're not following your quest playthrough, open your quest book and follow the instructions!",
		];
	}

	public function getOrder() : int{
		return 6;
	}

	public function getNpcPosition() :Vector3{
		return new Vector3(20, 70, -45);
	}
}