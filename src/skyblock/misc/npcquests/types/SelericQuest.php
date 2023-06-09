<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests\types;

use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\misc\npcquests\NpcQuest;
use skyblock\utils\PveUtils;
use skyblock\utils\Utils;

class SelericQuest extends NpcQuest{
	public function getName() : string{
		return "Talk to Seleric";
	}

	public function getIdentifier(){
		return "seleric";
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
			"§r§7[1/7] §r§l§3Seleric: §r§7Greetings, {username}. Welcome to AetherPE Skyblock!",
			"§r§7[2/7] §r§l§3Seleric: §r§7Before you begin your journey, it's important to understand your core player stats. These stats are crucial for your survival in this world.",
			"§r§7[3/7] §r§l§3Seleric: §r§7There are seven core stats that you should be aware of: " . PveUtils::getHealth() . ", " . PveUtils::getCritDamage() . "§7, " . PveUtils::getCritChance() . "§7, " . PveUtils::getStrength() . "§7, " . PveUtils::getIntelligence() . " §7, " . PveUtils::getSpeed() . "§7, and " . PveUtils::getDefense() . "§7.",
			"§r§7[4/7] §r§l§6{username}: §r§7How can I improve my stats?",
			"§r§7[5/7] §r§l§3Seleric: §r§7You can improve your stats in several ways. One way is to equip better armor and weapons, which can give you additional bonuses to your stats.",
			"§r§7[6/7] §r§l§3Seleric: §r§7Another way to improve your stats is to level up your skills, which can give you passive bonuses to your stats. You can also collect items and complete collections to unlock more bonuses.",
			"§r§7[7/7] §r§l§3Seleric: §r§7Remember to keep an eye on your stats and try to improve them whenever possible. It could mean the difference between life and death in this world. Good luck!",
		];
	}

	public function getUnlockMessage() : array{
		return [
			"",
			"§r§l§6            NEW QUEST: §r§e§lTalk to Seleric",
			"§r§7As what Gared said, he's not far from here, look around maybe?",
			"",
		];
	}

	public function getNotUnlockedMessage() : array{
		return [
			"§r§7(1/1) §r§l§3Seleric: §r§7Back off chump, you're not following your quest playthrough, open your quest book and follow the instructions!",
		];
	}

	public function getOrder() : int{
		return 2;
	}

	public function getNpcPosition() : Vector3{
		return new Vector3(34, 69, -96);
	}
}