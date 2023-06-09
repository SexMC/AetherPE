<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class AuroraRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Aurora",
			"format" => C::BOLD . C::GOLD . "Aurora" . C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name}" . C::GRAY . ": §b{msg}",
			"tier" => 5,
			"isDefault" => false,
			"isStaff" => false,
			"discordGroupID" => "",
			"multiplier" => 1.5,
			"colour" => "§6",
			"ahLimit" => 7,
			"extraProfiles" => 3,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"playervaults.vault.4",
				"playervaults.vault.5",
				"playervaults.vault.6",
				"cucumber.command.mywarnings",
				"skyblock.command.fix",
				"skyblock.command.fix.all",
				"skyblock.command.heal",
				"skyblock.command.feed",
				"skyblock.command.sellinventory",
				"aether.kit.sinon",
				"aether.kit.troje",
				"aether.kit.hydra",
				"aether.kit.theseus",
				"aether.kit.aurora",
				"aether.kit.traveler"
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(25000, "Console"),
            MoneyNoteItem::getItem(250000, "Console"),
            RareCrate::getInstance()->getKeyItem(1),
            CommonCrate::getInstance()->getKeyItem(3)
        ];
    }
}