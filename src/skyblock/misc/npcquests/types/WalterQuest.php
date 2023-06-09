<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests\types;

use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\misc\kits\KitHandler;
use skyblock\misc\npcquests\NpcQuest;
use skyblock\utils\Utils;

class WalterQuest extends NpcQuest {
	public function getName() : string{
		return "Talk to Walter";
	}

	public function getIdentifier(){
		return "walter";
	}

	public function onComplete(Player $player) : void{}

	public function getConversationMessages() : array{
		return [
			"§r§7[1/10] §r§6Walter: §r§7Hello there, welcome to Walter's (invisible) Workshop! What can I do for you today?",
			"§r§7[2/10] §r§l§b{username}: §r§7Hi Walter! I'm interested in crafting some items, can you tell me more about it?",
			"§r§7[3/10] §r§6Walter: §r§7Certainly! Crafting is an essential part of AetherPE Skyblock, and you'll need to know how to do it to progress in the game. Each item in the game has a recipe, which is a specific combination of materials and items that you need to have in order to create it.",
			"§r§7[4/10] §r§l§b{username}: §r§7How do I find out what the recipes are?",
			"§r§7[5/10] §r§6Walter: §r§7There are a few ways. Some recipes can be found by unlocking collections, which are groups of related items that you can obtain and level up to gain rewards. Others can be obtained by purchasing recipe books from various NPCs around the map, or by completing certain quests or challenges.",
			"§r§7[6/10] §r§l§b{username}: §r§7That sounds complicated. Is there an easy way to keep track of all the recipes I've unlocked?",
			"§r§7[7/10] §r§6Walter: §r§7Yes! You can keep track of your unlocked recipes by opening the Recipe Book in your SkyBlock Menu. This will show you a list of all the recipes you have unlocked, as well as the ones you have yet to discover. You can also use the search function to look for specific items or materials.",
			"§r§7[8/10] §r§l§b{username}: §r§7That's really helpful, thanks Walter! Is there anything else I should know about crafting?",
			"§r§7[9/10] §r§6Walter: §r§7Well, each recipe requires a specific set of materials, which can be obtained through various means such as farming, mining, or trading with other players. You'll also need to have the appropriate crafting station, such as a workbench or an anvil, to create certain items. But don't worry, I'm sure you'll get the hang of it in no time! Good luck, and happy crafting!",
			"§r§7[10/10] §r§6Walter: §r§7Well, each recipe requires a specific set of materials, which can be obtained through various means such as farming, mining, or trading with other players. You'll also need to have the appropriate crafting station, such as a workbench or an anvil, to create certain items. But don't worry, I'm sure you'll get the hang of it in no time! Good luck, and happy crafting!",
		];
	}

	public function getUnlockMessage() : array{
		return [
			"",
			"§r§l§6            NEW QUEST: §r§e§lTalk to Walter",
			"§r§7As Renna said, find and talk to Walter, he shouldn't be far from here.",
			"",
		];
	}

	public function getNotUnlockedMessage() : array{
		return [
			"§r§7(1/1) §r§l§3Walter: §r§7Back off chump, you're not following your quest playthrough, open your quest book and follow the instructions!",
		];
	}

	public function getOrder() : int{
		return 5;
	}

	public function getNpcPosition() :Vector3{
		return new Vector3(-18, 70, -160);
	}
}