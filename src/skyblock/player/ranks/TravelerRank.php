<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class TravelerRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Traveler",
			"format" => C::WHITE . "Traveler" . C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name}§7: {msg}",
			"tier" => 0,
			"isDefault" => true,
			"isStaff" => false,
			"discordGroupID" => "",
			"colour" => "§7",
			"ahLimit" => 2,
			"multiplier" => 1,
			"permissions" => [
				"aether.kit.traveler"
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(2500, "Console"),
            MoneyNoteItem::getItem(25000, "Console"),
            AetherCrate::getInstance()->getKeyItem(2)
        ];
    }
}