<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests\types;

use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\items\SkyblockItems;
use skyblock\misc\kits\KitHandler;
use skyblock\misc\npcquests\NpcQuest;
use skyblock\utils\Utils;

class YekrutQuest extends NpcQuest {
	public function getName() : string{
		return "Talk to Yekrut";
	}

	public function getIdentifier(){
		return "yekrut";
	}

	public function onComplete(Player $player) : void{
		$items = [
			SkyblockItems::ROGUE_SWORD(),
			VanillaItems::WOODEN_PICKAXE(),
			VanillaItems::WOODEN_AXE(),
		];

		foreach($items as $item){
			Utils::addItem($player, $item);
		}
	}

	public function getConversationMessages() : array{
		return [
            "§r§7[1/3] §r§2Yekrut: §r§aGreetings {username}, Welcome to §bAetherPE Skyblock!",
            "§r§7[2/3] §r§2Yekrut: §r§aBefore you begin, let's have you explore the place and get comfy, please §bFind §7and §bTalk to Gared.",
            "§r§7[3/3] §r§2Yekrut: §r§aHere's some tools to get you started.",
			"",
			"§r§7* Yekrut is Handing you something *",
		];
	}

	public function getUnlockMessage() : array{
		return [
			"",
			"§r§l§6» NEW QUEST: §r§e§lTalk to Gared",
			"§r§7Talk to Gared to begin your adventure.",
			""
		];
	}

	public function getNotUnlockedMessage() : array{
		return [
			"§r§7[1/1] §r§2Yekrut: §r§aDid you talk to Gared yet? He's located somewhere near me.",
		];
	}

	public function getOrder() : int{
		return 0;
	}

	public function getNpcPosition() :Vector3{
		return new Vector3(-7, 69, -79);
	}
}