<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class TrialModeratorRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Moderator",
			"format" => "§r§l§3TRIAL MOD" . C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name}" . C::GRAY . ": §5{msg}",
			"tier" => 11,
			"multiplier" => 1.7,
			"isDefault" => false,
			"isStaff" => true,
			"discordGroupID" => "",
			"colour" => "§2",
			"ahLimit" => 8,
			"permissions" => [
				"playervaults.vault.2",
				"playervaults.vault.3",
				"playervaults.vault.4",
				"playervaults.vault.5",
				"playervaults.vault.6",
				"skyblock.chat.staffchat",
				"playervaults.vault.7",
				"playervaults.vault.8",
				"invsee.inventory.view",
				"antiac.command.alerts",
				"invsee.inventory",
				"anticheat38.alerts",
				"aetherpunishments.command.ban",
				"aetherpunishments.command.history",
				"aetherpunishments.command.mute",
				"aetherpunishments.command.unmute",
				"aetherpunishments.command.warn",
				"aetherpunishments.command.alias",
				"skyblock.command.autosell",
				"cucumber.command.pardon",
				"invsee.enderinventory.view",
				"invsee.inventory.view",
				"skyblock.command.staffmode",
				"skyblock.command.fix",
				"skyblock.command.fix.all",
				"skyblock.msg.bypass",
				"skyblock.command.alias",
				"skyblock.command.sellinventory",
				"skyblock.command.heal",
				"skyblock.command.blocks",
				"skyblock.command.staffchat",
				"skyblock.command.feed",
				"skyblock.command.vanish",
				"aether.kit.sinon",
				"aether.kit.troje",
				"aether.kit.hydra",
				"aether.kit.aether+",
				"aether.kit.theseus",
				"aether.kit.aurora",
				"aether.kit.aether",
				"aether.kit.youtuber",
				"aether.kit.traveler",
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