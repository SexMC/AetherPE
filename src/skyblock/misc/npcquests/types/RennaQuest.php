<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests\types;

use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\misc\kits\KitHandler;
use skyblock\misc\npcquests\NpcQuest;
use skyblock\utils\Utils;

class RennaQuest extends NpcQuest {
	public function getName() : string{
		return "Talk to Renna";
	}

	public function getIdentifier(){
		return "renna";
	}

	public function onComplete(Player $player) : void{
	}

	public function getConversationMessages() : array{
		return [
			"§r§7(1/8) §r§l§6Renna: §r§7Hello there {username}, what can I help you with today?",
			"§r§7(2/8) §r§l§b{username}: §r§7Hi Renna, I wanted to ask about collections. What are they exactly?",
			"§r§7(3/8) §r§l§6Renna: §r§7Collections are groups of items that can be obtained by playing the game, such as killing monsters, fishing, or farming. When you complete a collection, you can claim rewards such as items or coins.",
			"§r§7(4/8) §r§l§b{username}: §r§7How do I start a collection?",
			"§r§7(5/8) §r§l§6Renna: §r§7You can start a collection by obtaining the first item in that collection. For example, to start the Wheat collection, you need to obtain one piece of wheat. You can then view your progress and claim rewards by opening your Skyblock Menu and selecting the Collections tab.",
			"§r§7(6/8) §r§l§b{username}: §r§7Are there any benefits to completing collections?",
			"§r§7(7/8) §r§l§6Renna: §r§7Yes, besides the rewards you get from claiming them, completing collections can also unlock perks such as faster minion production or increased mob spawn rates. It's definitely worth it to work on completing collections as you play through the game.",
			"§r§7(8/8) §r§l§3Renna: §r§7It's time you meet my friend §fWalter§7, §bHe's also in the Mines§7, he loves talking to newcomers like you!",

		];
	}

	public function getUnlockMessage() : array{
		return [
			"",
			"§r§l§6            NEW QUEST: §r§e§lTalk to Renna",
			"§r§7As Adam said, find and talk to Renna, he shouldn't be far from here.",
			"",
		];
	}

	public function getNotUnlockedMessage() : array{
		return [
			"§r§7(1/1) §r§l§3Renna: §r§7Back off chump, you're not following your quest playthrough, open your quest book and follow the instructions!",
		];
	}

	public function getOrder() : int{
		return 4;
	}

	public function getNpcPosition() :Vector3{
		return new Vector3(-13, 67, -139);
	}
}