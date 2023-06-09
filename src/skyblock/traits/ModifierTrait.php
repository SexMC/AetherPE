<?php

declare(strict_types=1);

namespace skyblock\traits;

use skyblock\items\customenchants\BaseCustomEnchant;
use skyblock\items\rarity\itemskin\ItemSkin;

trait ModifierTrait {
	private float $extraDamage = 0;

	/** @var ItemSkin[]  */
	private array $entityItemSkins = [];
	/** @var \skyblock\items\customenchants\BaseCustomEnchant[] */
	private array $entityCustomEnchants;
	/** @var int[] CE IDS */
	private array $negatedEntityCustomEnchants = [];
	/** @var string[] ITEMSKIN NAMES  */
	private array $negatedEntityItemSkins = [];


	/** @var ItemSkin[]  */
	private array $damagerItemSkins = [];
	/** @var BaseCustomEnchant[] */
	private array $damagerCustomEnchants;
	/** @var int[] CE IDS */
	private array $negatedDamagerCustomEnchants = [];
	/** @var string[] ITEMSKIN NAMES  */
	private array $negatedDamagerItemSkins = [];

	public function negateDamagerCustomEnchant(string $customEnchantID): void {
		$this->negatedDamagerCustomEnchants[] = $customEnchantID;
	}

	public function negateDamagerItemSkin(string $itemskin): void {
		$this->negatedDamagerItemSkins[] = $itemskin;
	}

	public function negateEntityCustomEnchant(int $customEnchantID): void {
		$this->negatedEntityCustomEnchants[] = $customEnchantID;
	}

	public function negateEntityItemSkin(string $itemskin): void {
		$this->negatedEntityItemSkins[] = $itemskin;
	}
    public function unNegateEntityItemSkin(string $itemskin): void {

    }

	public function getExtraDamage() : float{
		return $this->extraDamage;
	}

	public function addExtraDamage(float $extraDamage): void {
		$this->extraDamage += $extraDamage;
	}

	public function setExtraDamage(float $extraDamage) : void{
		$this->extraDamage = $extraDamage;
	}

	/**
	 * @return int[]
	 */
	public function getNegatedDamagerCustomEnchants() : array{
		return $this->negatedDamagerCustomEnchants;
	}

	/**
	 * @return string[]
	 */
	public function getNegatedDamagerItemSkins() : array{
		return $this->negatedDamagerItemSkins;
	}

	/**
	 * @return int[]
	 */
	public function getNegatedEntityCustomEnchants() : array{
		return $this->negatedEntityCustomEnchants;
	}

	/**
	 * @return string[]
	 */
	public function getNegatedEntityItemSkins() : array{
		return $this->negatedEntityItemSkins;
	}
}