<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class HydraRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Hydra",
			"format" => C::BOLD . C::DARK_GREEN . "Hydra" . C::RESET . C::DARK_GRAY . " | " . C::RESET . "{display_name}" . C::GRAY . ": §b{msg}",
			"tier" => 3,
			"isDefault" => false,
			"isStaff" => false,
			"discordGroupID" => "",
			"multiplier" => 1.3,
			"colour" => "§2",
			"ahLimit" => 5,
			"extraProfiles" => 2,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"playervaults.vault.4",
				"skyblock.command.fix",
				"skyblock.command.fix.all",
				"skyblock.command.feed",
				"skyblock.command.sellinventory",
				"aether.kit.sinon",
				"aether.kit.troje",
				"aether.kit.hydra",
				"aether.kit.traveler"
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(15000, "Console"),
            MoneyNoteItem::getItem(150000, "Console"),
            CommonCrate::getInstance()->getKeyItem(2)
        ];
    }
}