<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class TheseusRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Theseus",
			"format" => C::BOLD . C::LIGHT_PURPLE . "Theseus" . C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name}" . C::GRAY . ": §b{msg}",
			"tier" => 4,
			"isDefault" => false,
			"isStaff" => false,
			"discordGroupID" => "",
			"multiplier" => 1.4,
			"colour" => "§d",
			"ahLimit" => 6,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"playervaults.vault.4",
				"playervaults.vault.5",
				"cucumber.command.mywarnings",
				"skyblock.command.fix",
				"skyblock.command.fix.all",
				"skyblock.command.feed",
				"skyblock.command.sellinventory",
				"aether.kit.sinon",
				"aether.kit.troje",
				"aether.kit.hydra",
				"aether.kit.theseus",
				"aether.kit.traveler"
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(20000, "Console"),
            MoneyNoteItem::getItem(200000, "Console"),
            RareCrate::getInstance()->getKeyItem(1),
            CommonCrate::getInstance()->getKeyItem(2)
        ];
    }
}