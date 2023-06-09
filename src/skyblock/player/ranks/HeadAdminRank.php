<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class HeadAdminRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Head-Admin",
			"format" => C::BOLD . C::RED . "Head-Admin" . C::RESET . C::DARK_GRAY . " |" . C::RESET . " {display_name}" . C::GRAY . ": §c{msg}",
			"tier" => 14,
			"isDefault" => false,
			"isStaff" => true,
			"ahLimit" => 8,
			"discordGroupID" => "",
			"multiplier" => 1.7,
			"colour" => "§c",
			"permissions" => [
                'skyblock.permissions.admin',
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