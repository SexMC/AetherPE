<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests\types;

use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\misc\kits\KitHandler;
use skyblock\misc\npcquests\NpcQuest;
use skyblock\utils\Utils;

class GaredQuest extends NpcQuest {
	public function getName() : string{
		return "Talk to Gared";
	}

	public function getIdentifier(){
		return "gared";
	}

	public function onComplete(Player $player) : void{
		$items = [
			VanillaItems::DIAMOND()->setCount(5),
		];

		foreach($items as $item){
			Utils::addItem($player, $item);
		}
	}

	public function getConversationMessages() : array{
		return [
			"§r§7(1/9) §r§l§3Gared: §r§7Hello there!",
			"§r§7(2/9) §r§l§3Gared: §r§7I'm assuming §bYekrut §7told you to talk to me?",
			"§r§7(3/9) §r§l§3Gared: §r§7Well, welcome to AetherPE!",
			"§r§7(4/9) §r§l§3Gared: §r§7It's an open world minecraft server, with skyblock islands!",
			"§r§7(5/9) §r§l§3Gared: §r§7You'll be able to enter your own island once you're done doing your quests.",
			"§r§7(6/9) §r§l§3Gared: §r§7Yes, chat is disabled for only you during the mean time, to prevent any interruption with npc dialogs.",
			"§r§7(7/9) §r§l§3Gared: §r§7Fear not, it will be enabled soon!",
			"§r§7(8/9) §r§l§3Gared: §r§7Find and talk to Seleric, He's not far from where i am.",
			"§r§7(9/9) §r§l§3Gared: §r§7I work for the King, here's some diamonds, you can spend them or use them in the future.",
		];
	}

	public function getUnlockMessage() : array{
		return [
			"",
			"§r§l§6            QUEST COMPLETE: §r§e§lTalk to Gared",
			"§r§7As what Gared said, he's not far from here, look around maybe?",
			""
		];
	}

	public function getNotUnlockedMessage() : array{
		return [
			"§r§7(1/1) §r§l§3Gared: §r§7Sorry, can't help you, you have to finish §3[QUEST: Talk to Yekrut]",
		];
	}

	public function getOrder() : int{
		return 1;
	}

	public function getNpcPosition() :Vector3{
		return new Vector3(10, 69, -89);
	}
}