<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class AstronomicalRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Astronomical",
			"format" => "§r§l§5§oAstronomical§r§l§r" . C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name}" . C::GRAY . ": §d{msg}",
			"tier" => 9,
			"isDefault" => false,
			"isStaff" => false,
			"multiplier" => 1.7,
			"discordGroupID" => "",
			"colour" => "§r§l§5",
			"ahLimit" => 10,
			"extraProfiles" => 8,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"playervaults.vault.4",
				"playervaults.vault.5",
				"playervaults.vault.6",
				"playervaults.vault.7",
				"playervaults.vault.8",
				"playervaults.vault.9",
				"playervaults.vault.10",
				"skyblock.command.fix",
				"skyblock.command.fix.all",
				"skyblock.command.sellinventory",
				"skyblock.command.heal",
				"skyblock.command.vaccine",
				"skyblock.command.blocks",
				"skyblock.command.feed",
				"skyblock.command.bless",
				"skyblock.command.blocks",
				"skyblock.command.jet",
				"skyblock.command.autosell",
				"skyblock.command.nick",
				"aether.kit.sinon",
				"aether.kit.troje",
				"aether.kit.hydra",
				"aether.kit.theseus",
				"aether.kit.aurora",
				"aether.kit.astronomical",
				"aether.kit.astronomicallootbox",
				"aether.kit.aether",
				"aether.kit.aether+",
				"aether.kit.aether+lootbox",
				"aether.kit.lootbox",
				"aether.kit.traveler",
                'skyblock.fly.auto'
			]
		]);
	}

    public function getReclaim(): array {
        return [
            EssenceNoteItem::getItem(40000, "Console"),
            MoneyNoteItem::getItem(400000, "Console"),
            AetherCrate::getInstance()->getKeyItem(3),
            RareCrate::getInstance()->getKeyItem(4),
            CommonCrate::getInstance()->getKeyItem(6)
        ];
    }
}