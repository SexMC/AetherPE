<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class YoutuberRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Youtuber",
			"format" => C::BOLD . C::RED . "You" . C::WHITE . "tuber " . C::RESET . C::DARK_GRAY . "| " . C::RESET . "{display_name}" . C::GRAY . ": §b{msg}",
			"tier" => 8,
			"multiplier" => 1.7,
			"isDefault" => false,
			"isStaff" => false,
			"discordGroupID" => "",
			"colour" => "§a",
			"ahLimit" => 9,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"playervaults.vault.4",
				"playervaults.vault.5",
				"playervaults.vault.6",
				"playervaults.vault.7",
				"playervaults.vault.8",
				"cucumber.command.mywarnings",
				"skyblock.command.fix",
				"skyblock.command.fix.all",
				"skyblock.command.bless",
				"skyblock.command.autosell",
				"skyblock.command.blocks",
				"skyblock.command.sellinventory",
				"skyblock.command.heal",
				"skyblock.command.blocks",
				"skyblock.command.feed",
				"aether.kit.sinon",
				"aether.kit.troje",
				"aether.kit.hydra",
				"aether.kit.theseus",
				"aether.kit.aurora",
				"aether.kit.aether",
				"aether.kit.youtuber",
				"aether.kit.rookie",
                'skyblock.fly.auto'
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(40000, "Console"),
            MoneyNoteItem::getItem(400000, "Console"),
            AetherCrate::getInstance()->getKeyItem(2),
            RareCrate::getInstance()->getKeyItem(3),
            CommonCrate::getInstance()->getKeyItem(5)
        ];
    }
}