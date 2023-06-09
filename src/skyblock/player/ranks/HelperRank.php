<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class HelperRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Helper",
			"format" => C::BOLD . C::LIGHT_PURPLE . "Helper " . C::RESET . C::DARK_GRAY . "| " . C::RESET . "{display_name}" . C::GRAY . ": §5{msg}",
			"tier" => 10,
			"isDefault" => false,
			"multiplier" => 1.7,
			"isStaff" => true,
			"discordGroupID" => "",
			"colour" => "§d",
			"ahLimit" => 8,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"playervaults.vault.4",
				"playervaults.vault.5",
				"skyblock.chat.staffchat",
				"antiac.command.alerts",
				"playervaults.vault.6",
				"playervaults.vault.7",
				"playervaults.vault.8",
				"invsee.enderinventory.view",
				"invsee.inventory.view",
				"aetherpunishments.command.ban",
				"aetherpunishments.command.history",
				"aetherpunishments.command.mute",
				"aetherpunishments.command.unmute",
				"aetherpunishments.command.warn",
				"aetherpunishments.command.alias",
				"skyblock.command.staffmode",
				"skyblock.command.fix",
				"skyblock.command.fix.all",
				"skyblock.command.staffchat",
				"skyblock.command.sellinventory",
				"skyblock.command.heal",
				"skyblock.command.alias",
				"skyblock.command.autosell",
				"skyblock.command.blocks",
				"skyblock.command.vanish",
				"skyblock.command.vanish",
				"skyblock.command.freeze",
				"skyblock.command.feed",
				"skyblock.msg.bypass",
				"aether.kit.sinon",
				"aether.kit.aether+",
				"aether.kit.troje",
				"aether.kit.hydra",
				"aether.kit.theseus",
				"aether.kit.aurora",
				"aether.kit.aether",
				"aether.kit.youtuber",
				"aether.kit.traveler",
				"anticheat38.alerts",
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