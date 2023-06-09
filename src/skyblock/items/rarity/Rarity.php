<?php

namespace skyblock\items\rarity;

use JetBrains\PhpStorm\Pure;
use pocketmine\utils\TextFormat;
use skyblock\items\customenchants\ICustomEnchant;

final class Rarity{

	public static function mastery() : self{
		return new self("mastery", "Mastery", TextFormat::DARK_RED, ICustomEnchant::RARITY_MASTERY);
	}

	public static function legendary() : self{
		return new self("legendary", "LEGENDARY", TextFormat::GOLD, ICustomEnchant::RARITY_LEGENDARY);
	}

	public static function special() : self{
		return new self("special", "SPECIAL", TextFormat::RED, ICustomEnchant::RARITY_SPECIAL);
	}

	public static function epic() : self{
		return new self("epic", "EPIC", TextFormat::DARK_PURPLE, ICustomEnchant::RARITY_RARE);
	}

	public static function rare() : self{
		return new self("rare", "RARE", TextFormat::DARK_AQUA, ICustomEnchant::RARITY_ELITE);
	}

	public static function common() : self{
		return new self("common", "COMMON", TextFormat::WHITE, 0);
	}

	public static function uncommon() : self{
		return new self("uncommon", "UNCOMMON", TextFormat::GREEN, ICustomEnchant::RARITY_UNCOMMON);
	}

	private string $id;
	private string $displayName;
	private string $color;
	private int $tier;

	public function __construct(string $id, string $displayName, string $color, int $tier){
		$this->id = $id;
		$this->displayName = $displayName;
		$this->color = $color;
		$this->tier = $tier;
	}

	#[Pure]
	public function equals(self $rarity) : bool{
		return $this->id === $rarity->getId();
	}

	public function getId() : string{
		return $this->id;
	}

	#[Pure]
	public function getDisplayName() : string{
		return $this->getColor() . $this->displayName;
	}

	public function getColor() : string{
		return $this->color;
	}

	public function getTier() : int{
		return $this->tier;
	}

}