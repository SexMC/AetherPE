<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests\types;

use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\misc\kits\KitHandler;
use skyblock\misc\npcquests\NpcQuest;
use skyblock\utils\Utils;

class AdamQuest extends NpcQuest {
	public function getName() : string{
		return "Talk to Adam";
	}

	public function getIdentifier(){
		return "adam";
	}

	public function onComplete(Player $player) : void{
	}

	public function getConversationMessages() : array{
		return [
			"§r§7(1/11) §r§l§bAdam: §r§7Welcome {username}! Have you heard about the Skills system in AetherPE Skyblock?",
			"§r§7(2/11) §r§l§6{username}: §r§7No, I haven't. What are Skills?",
			"§r§7(3/11) §r§l§bAdam: §r§7Skills are abilities that you can level up by performing various activities in the game. There are nine different skills in total, each with their own benefits and rewards.",
			"§r§7(4/11) §r§l§6{username}: §r§7What are the different skills and how do I level them up?",
			"§r§7(5/11) §r§l§bAdam: §r§7The skills are Farming, Mining, Combat, Foraging, Fishing, Enchanting, Alchemy, Taming, and Carpentry. Each skill has its own unique way to level up, such as breaking crops for Farming, killing monsters for Combat, or chopping down trees for Foraging.",
			"§r§7(6/11) §r§l§6{username}: §r§7What benefits do I get for leveling up my skills?",
			"§r§7(7/11) §r§l§bAdam: §r§7Leveling up your skills can provide various benefits such as increased damage, better loot drops, faster farming times, and more efficient resource gathering. Additionally, leveling up certain skills can unlock special abilities and perks.",
			"§r§7(8/11) §r§l§6{username}: §r§7How do I track my skill progress?",
			"§r§7(9/11) §r§l§bAdam: §r§7You can track your skill progress in your Skyblock Menu, by clicking on the Skills section. There, you can see your current skill level, progress towards the next level, and the benefits and rewards for each level.",
			"§r§7(10/11) §r§l§bAdam: §r§7I hope this has been helpful! If you have any more questions, feel free to ask. Good luck on your skill leveling journey!",
			"§r§7(11/11) §r§l§3Adam: §r§7It's time you meet my friend §fRenna§7, §bShe's in the Mines§7, she loves talking to newcomers like you!",
		];
	}

	public function getUnlockMessage() : array{
		return [
			"",
			"§r§l§6            NEW QUEST: §r§e§lTalk to Adam",
			"§r§7As seleric said, find and talk to adam, he shouldn't be far from here.",
			"",
		];
	}

	public function getNotUnlockedMessage() : array{
		return [
			"§r§7(1/1) §r§l§3Adam: §r§7Back off chump, you're not following your quest playthrough, open your quest book and follow the instructions!",
		];
	}

	public function getOrder() : int{
		return 3;
	}

	public function getNpcPosition() :Vector3{
		return new Vector3(37, 67, -74);
	}
}