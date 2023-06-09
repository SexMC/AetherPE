<?php

declare(strict_types=1);

namespace skyblock\items\armor\emerald;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\sets\SpecialSet;
use skyblock\items\SkyblockItems;
use skyblock\misc\collection\mining\EmeraldCollection;
use skyblock\player\AetherPlayer;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\PveUtils;
use SOFe\AwaitGenerator\Await;

class EmeraldSet extends ArmorSet {
	use AetherHandlerTrait;
	use AwaitStdTrait;

	private array $data = [];




	public function getItemAttributes(string $piece) : array{
		$arr = [];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 50);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 100);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 75);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 45);
				break;
		}

		return $arr;
	}

	public function onWear(AetherPlayer $player) : void{
		parent::onWear($player);
		//TODO: ability

		$id = $player->getCurrentProfile()->getUniqueId();

		Await::f2c(function() use($player, $id) {
			while($player->isOnline() && $id === $player->getCurrentProfile()->getUniqueId()){
				yield $this->getStd()->sleep(20);

				if(ArmorSet::getCache($player) instanceof EmeraldSet){
					$c = 7000;

					$increase = (int) floor($c / 3000);
					if(!isset($this->data[$player->getName()])){
						$this->data[$player->getName()] = $increase;

						$player->getPveData()->setDefense($player->getPveData()->getDefense() + $increase);
						$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + $increase);

						continue;
					}

					if($this->data[$player->getName()] === $increase){
						continue;
					}

					$player->getPveData()->setDefense($player->getPveData()->getDefense() - $this->data[$player->getName()]);
					$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() - $this->data[$player->getName()]);

					$player->getPveData()->setDefense($player->getPveData()->getDefense() + $increase);
					$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + $increase);


					continue;
				}

				break;
			}

			if(isset($this->data[$player->getName()]) && $player->isOnline()){
				$player->getPveData()->setDefense($player->getPveData()->getDefense() - $this->data[$player->getName()]);
				$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() - $this->data[$player->getName()]);
			}

			unset($this->data[$player->getName()]);
		});

	}
	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getAbilities() : array{
		return [];
	}

	public function getIdentifier() : string{
		return "emerald_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§5Emerald " . $piece;
	}

	public function getLore(Item $item) : array{
		return [
			"§r§l§5Set Bonus: §5Tank",
			"§r§l§5 » §r§5Increases " . PveUtils::getHealth() . "§5 by §a+1",
			"§r§l§5   §r§5and " . PveUtils::getDefense() . "§5 by §a+1§5 for",
			"§r§l§5   §r§5every §b3,000§5 Emeralds in your",
			"§r§l§5   §r§5collection. Max 350 each.",
		];
	}

	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::EMERALD_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::EMERALD_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::EMERALD_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::EMERALD_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::epic();
	}
}