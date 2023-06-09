<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class AetherRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Aether",
			"format" => C::BOLD . C::AQUA . "Aether" . C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name}" . C::GRAY . ": §b{msg}",
			"tier" => 6,
			"isDefault" => false,
			"isStaff" => false,
			"multiplier" => 1.6,
			"discordGroupID" => "",
			"colour" => "§b",
			"ahLimit" => 8,
			"extraProfiles" => 4,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"playervaults.vault.4",
				"playervaults.vault.5",
				"playervaults.vault.6",
				"playervaults.vault.7",
				"cucumber.command.mywarnings",
				"skyblock.command.fix",
				"skyblock.command.fix.all",
				"skyblock.command.sellinventory",
				"skyblock.command.heal",
				"skyblock.command.bless",
				"skyblock.command.feed",
				"skyblock.command.autosell",
				"aether.kit.sinon",
				"aether.kit.troje",
				"aether.kit.hydra",
				"aether.kit.theseus",
				"aether.kit.aurora",
				"aether.kit.aether",
				"aether.kit.traveler",
                'skyblock.fly.auto'
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(30000, "Console"),
            MoneyNoteItem::getItem(300000, "Console"),
            AetherCrate::getInstance()->getKeyItem(1),
            RareCrate::getInstance()->getKeyItem(2),
            CommonCrate::getInstance()->getKeyItem(4)
        ];
    }
}