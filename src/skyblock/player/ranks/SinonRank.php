<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class SinonRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Sinon",
			"format" => C::BOLD . C::YELLOW . "Sinon" . C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name} " . C::GRAY . ": §b{msg}",
			"tier" => 1,
			"isDefault" => false,
			"isStaff" => false,
			"discordGroupID" => "",
			"multiplier" => 1.1,
			"colour" => "§e",
			"ahLimit" => 3,
			"permissions" => [
				"playervaults.vault.1",
				"playervaults.vault.2",
				"skyblock.command.fix",
				"skyblock.command.feed",
				"aether.kit.sinon",
				"aether.kit.traveler",
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(5000, "Console"),
            MoneyNoteItem::getItem(50000, "Console"),
            CommonCrate::getInstance()->getKeyItem(1)
        ];
    }
}