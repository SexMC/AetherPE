<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class TrojeRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Troje",
			"format" => C::BOLD . C::DARK_AQUA . "Troje". C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name}" . C::GRAY. ": §b{msg}",
			"tier" => 2,
			"isDefault" => false,
			"isStaff" => false,
			"discordGroupID" => "",
			"multiplier" => 1.2,
			"colour" => "§3",
			"ahLimit" => 4,
			"extraProfiles" => 1,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"skyblock.command.fix",
				"skyblock.command.feed",
				"aether.kit.sinon",
				"aether.kit.troje",
				"aether.kit.traveler",
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(10000, "Console"),
            MoneyNoteItem::getItem(100000, "Console"),
            CommonCrate::getInstance()->getKeyItem(1)
        ];
    }
}