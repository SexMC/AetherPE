<?php

declare(strict_types=1);

namespace skyblock\items\misc;

use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\menus\items\SkyblockMenu;
use skyblock\traits\PlayerCooldownTrait;

class SkyblockMenuItem extends SkyblockItem {
	use PlayerCooldownTrait;


	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->setCustomName("§r§a§l» Skyblock Menu « §r§7(Click)");
		$this->getProperties()->setDescription([
			"§r§7View all of your skyblock",
			"§r§7progress, including your skills,",
			"§r§7collections, recipes and more!",
			"§r§7",
			"§r§eClick to open!"
		]);

		$this->makeUnique();
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		if(!$this->isOnCooldown($player)){
			$this->setCooldown($player, 1);
			(new SkyblockMenu($player))->send($player);
		}

		return parent::onClickAir($player, $directionVector);
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::uncommon());
	}
}