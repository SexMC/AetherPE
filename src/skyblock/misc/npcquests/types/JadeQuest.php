<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests\types;

use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\misc\kits\KitHandler;
use skyblock\misc\npcquests\NpcQuest;
use skyblock\utils\Utils;

class JadeQuest extends NpcQuest {
	public function getName() : string{
		return "Talk to Jade";
	}

	public function getIdentifier(){
		return "jade";
	}

	public function onComplete(Player $player) : void{
	}

	public function getConversationMessages() : array{
		return [
			"§r§7(1/10) §r§l§3Jade: §r§7Welcome, adventurer! I'm Jade, and I'm here to help you with your accessories.",
			"§r§7(2/10) §r§l§6{username}: §r§7Hi, Jade. I've been playing Skyblock for a while now, but I'm still a bit confused about accessories. Can you help me out?",
			"§r§7(3/10) §r§l§3Jade: §r§7Of course, I'd be happy to help. Accessories are items that you can wear to boost your stats and abilities in combat.",
			"§r§7(4/10) §r§l§6{username}: §r§7Oh, I see. So how do I get accessories?",
			"§r§7(5/10) §r§l§3Jade: §r§7There are a few different ways to obtain accessories. You can craft them using recipes, buy them from the Auction House or NPCs, or get them as drops from various enemies in Skyblock.",
			"§r§7(6/10) §r§l§6{username}: §r§7That makes sense. So, what are the different types of accessories?",
			"§r§7(7/10) §r§l§3Jade: §r§7There are several different types of accessories, each with their own unique benefits. There are rings, talismans, artifacts, and more.",
			"§r§7(8/10) §r§l§6{username}: §r§7Can you give me an example of what a ring does?",
			"§r§7(9/10) §r§l§3Jade: §r§7Sure thing. Rings are accessories that you can wear in your ring slot to provide various benefits, such as increased defense or health regeneration.",
			"§r§7(10/10) §r§l§3Jade: §r§7That's a brief overview of accessories in Skyblock. If you ever need more help or have any questions, don't hesitate to ask me or any of the other NPCs around here. Good luck, adventurer!",		];
	}

	public function getUnlockMessage() : array{
		return [
			"",
			"§r§l§6            NEW QUEST: §r§e§lTalk to Jade",
			"§r§7As Luke said, find and talk to Jade, he shouldn't be far from here.",
			"",
		];
	}

	public function getNotUnlockedMessage() : array{
		return [
			"§r§7(1/1) §r§l§3Jade: §r§7Back off chump, you're not following your quest playthrough, open your quest book and follow the instructions!",
		];
	}

	public function getOrder() : int{
		return 7;
	}

	public function getNpcPosition() :Vector3{
		return new Vector3(51, 68, -41);
	}
}