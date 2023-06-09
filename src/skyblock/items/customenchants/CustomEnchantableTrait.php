<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;

trait CustomEnchantableTrait {

	/**
	 * @return CustomEnchantInstance[]
	 */
	public function getCustomEnchants(): array {
		$arr = [];
		
		foreach(($this->getNamedTag()->getCompoundTag("tag_custom_enchants") ?? new CompoundTag())->getValue() as $enchName => $intTag) {
			if($intTag instanceof IntTag){
				$arr[] = new CustomEnchantInstance(CustomEnchantFactory::getInstance()->get($enchName), $intTag->getValue());
			}
		}


		return $arr;
	}

	public function addCustomEnchant(CustomEnchantInstance $instance): self {
		$tag = ($this->getNamedTag()->getCompoundTag("tag_custom_enchants") ?? new CompoundTag());

		$tag->setInt($instance->getCustomEnchant()->getIdentifier()->getId(), $instance->getLevel());

		$this->getNamedTag()->setTag("tag_custom_enchants", $tag);

		$this->resetLore();

		return $this;
	}

	public function removeCustomEnchant(BaseCustomEnchant $ce): void {
		$tag = ($this->getNamedTag()->getCompoundTag("tag_custom_enchants") ?? new CompoundTag());

		$tag->removeTag($ce->getIdentifier()->getId());

		$this->getNamedTag()->setTag("tag_custom_enchants", $tag);

		$this->resetLore();
	}

	public function hasCustomEnchant(string $id): bool {
		foreach($this->getCustomEnchants() as $ce){
			if($ce->getCustomEnchant()->getIdentifier()->getId() === $id){
				return true;
			}
		}

		return true;
	}



}