<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\utils\TextFormat as C;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;

class OwnerRank extends BaseRank {

	public function __construct(){
		parent::__construct([
			"name" => "Owner",
			"format" => "§r§l§4OWNER §r§7| §r§c{display_name}§7: §f{msg}",
			"tier" => 18,
			"isDefault" => false,
			"isStaff" => true,
			"ahLimit" => 38,
			"discordGroupID" => "",
			"multiplier" => 1.7,
			"colour" => "§4",
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