<?php

declare(strict_types=1);

namespace skyblock\items\itemattribute;

use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\PveUtils;
use function mb_chr;


class SkyBlockItemAttributeFactory{
	use AetherHandlerTrait;

	private array $list = [];

	public function onEnable() : void{
		$this->register(new ItemAttribute("Strength", mb_chr(0xE1F3), "§c"));
		$this->register(new ItemAttribute("Intelligence", mb_chr(0xE1F5), "§b"));
		$this->register(new ItemAttribute("Critical Chance", mb_chr(0xE1EB), "§6", true));
		$this->register(new ItemAttribute("Critical Damage", mb_chr(0xE1EB), "§6"));
		$this->register(new ItemAttribute("Defense", mb_chr(0xE1E9), "§a"));
		$this->register(new ItemAttribute("Damage", mb_chr(0xE100), "§c"));
		$this->register(new ItemAttribute("Speed", mb_chr(0xE1F4), "§f"));
		$this->register(new ItemAttribute("Health", mb_chr(0xE1E8), "§c"));
		$this->register(new ItemAttribute("Mining Speed", "", "§g"));
		$this->register(new ItemAttribute("Mining Wisdom", "", "§3"));
		$this->register(new ItemAttribute("Foraging Wisdom", "", "§3"));
		$this->register(new ItemAttribute("Combat Wisdom", "", "§3"));
		$this->register(new ItemAttribute("Mining Fortune", "", "§6"));
		$this->register(new ItemAttribute("Foraging Fortune", "", "§6"));
		$this->register(new ItemAttribute("Sea Creature Chance", "", "§6"));
		$this->register(new ItemAttribute("Fishing Speed", "", "§6"));
	}


	public function get(string $name) : ?ItemAttribute{
		return $this->list[str_replace(" ", "_", strtolower($name))] ?? null;
	}

	public function register(ItemAttribute $item) : void{
		$this->list[str_replace(" ", "_", strtolower($item->getName()))] = $item;
	}
}