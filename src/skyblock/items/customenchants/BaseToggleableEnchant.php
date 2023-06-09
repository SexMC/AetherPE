<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use skyblock\items\Equipment;
use skyblock\items\ItemEditor;
use skyblock\items\SkyblockArmor;

abstract class BaseToggleableEnchant extends BaseCustomEnchant {

	public static array $array = [];

	private array $toggled = [];

	abstract function toggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance): void;

	abstract function unToggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance): void;

	public function isToggled(Player $player): bool {
		return isset($this->toggled[$player->getName()]);
	}

	public function addToggled(Player $player): void {
		$this->toggled[$player->getName()] = 1;
	}

	public function removeToggled(Player $player): void {
		unset($this->toggled[$player->getName()]);
	}

	public function checkToggle(Player $player, Item $newItem, Item $oldItem, CustomEnchantInstance $enchantInstance): void {
		$id = $this->identifier->getId();


		if($oldItem instanceof ICustomEnchantable && $newItem instanceof ICustomEnchantable){
			if($oldItem->hasCustomEnchant($id) && !$newItem->hasCustomEnchant($id)){
				$this->unToggle($player, $oldItem, $enchantInstance);
				$this->removeToggled($player);

			} elseif(!$oldItem->hasCustomEnchant($id) && $newItem->hasCustomEnchant($id)){
				if(!$this->isToggled($player)){
					if($this->getIdentifier()->isImportant()){
						$player->sendMessage($this->getActivateMessage($player));
					}
					$this->toggle($player, $newItem, $enchantInstance);
					$this->addToggled($player);

					if($newItem instanceof Equipment && !$newItem instanceof SkyblockArmor){
						if(isset(self::$array[$player->getName()])){
							self::$array[$player->getName()][] = $enchantInstance;
						} else self::$array[$player->getName()] = [$enchantInstance];
					}
				}
			}
		}

		if($oldItem instanceof ICustomEnchantable && !$newItem instanceof ICustomEnchantable){
			$this->unToggle($player, $oldItem, $enchantInstance);
			$this->removeToggled($player);
		}

		if(!$oldItem instanceof ICustomEnchantable && $newItem instanceof ICustomEnchantable){

			if(!$this->isToggled($player)){
				if($this->getIdentifier()->isImportant()){
					$player->sendMessage($this->getActivateMessage($player));
				}
				$this->toggle($player, $newItem, $enchantInstance);
				$this->addToggled($player);

				if($newItem instanceof Equipment && !$newItem instanceof SkyblockArmor){
					if(isset(self::$array[$player->getName()])){
						self::$array[$player->getName()][] = $enchantInstance;
					} else self::$array[$player->getName()] = [$enchantInstance];
				}
			}
		}
	}

	/**
	 * @param Item $item
	 *
	 * @return CustomEnchantInstance[]
	 */
	public static function getToggleables(Item $item): array {

		$toggle = [];
		if($item instanceof ICustomEnchantable){
			foreach($item->getCustomEnchants() as $customEnchantInstance){
				$ce = $customEnchantInstance->getCustomEnchant();
				if(!$ce instanceof BaseToggleableEnchant) continue;

				if(isset($toggle[$ce::class]) && $customEnchantInstance->getLevel() <= $toggle[$ce::class]->getLevel()){
					continue;
				}

				$toggle[$ce::class] = $customEnchantInstance;
			}

			return $toggle;
		}

		return [];
	}
}