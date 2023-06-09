<?php

declare(strict_types=1);

namespace skyblock\items\armor\leaflet;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\PveUtils;
use SOFe\AwaitGenerator\Await;

class LeafletSet extends ArmorSet {
	use AetherHandlerTrait;
	use AwaitStdTrait;

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getItemAttributes(string $piece) : array{
		$arr = [];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 20);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 35);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 30);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 15);
				break;
		}

		return $arr;
	}

	public function onWear(AetherPlayer $player) : void{
		parent::onWear($player);

		Await::f2c(function() use ($player){
			yield $this->getStd()->sleep(10);

			while($player->isOnline() && ArmorSet::getCache($player) instanceof LeafletSet){
				$player->getPveData()->setHealth($player->getPveData()->getHealth() + 5);

				yield $this->getStd()->sleep(20);
			}
		});
	}

	public function getAbilities() : array{
		return [];
	}

	public function getIdentifier() : string{
		return "leaflet_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§fLeaflet " . $piece;
	}

	public function getLore(Item $item) : array{
		return [ //TODO: implement ability of this
			"§r§l§fSet Bonus: §fEnergy of the Forest",
			"§r§l§f » §r§fRegenerate §a+5 " . PveUtils::getHealth() . " §r§fevery second §r§l§f«",
		];
	}

	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::LEAFLET_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::LEAFLET_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::LEAFLET_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::LEAFLET_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}
}