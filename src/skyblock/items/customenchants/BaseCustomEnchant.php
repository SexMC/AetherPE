<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use skyblock\items\rarity\RarityTrait;

abstract class BaseCustomEnchant implements ICustomEnchant{
    use RarityTrait;

    protected CustomEnchantIdentifier $identifier;

	protected string $description = "";

    protected int $applicableTo = self::ITEM_ALL;
    protected int $maxLevel = 1;

    abstract public function prepare(): CustomEnchantIdentifier;

	public function __construct() {
        $this->identifier = $this->prepare();
	}

	#[Pure]
	public function getActivateMessage(Player $player): string {
		return $this->getRarity()->getColor() . "§l** {$this->identifier->getName()} **";
	}

    public function getMaxLevel(): int {
        return $this->maxLevel;
    }

    public function setMaxLevel(int $maxLevel): void {
        $this->maxLevel = $maxLevel;
    }

    public function getApplicableTo(): int {
        return $this->applicableTo;
    }

    public function setApplicableTo(int $applicable): void {
        $this->applicableTo = $applicable;
    }

    public function getIdentifier(): CustomEnchantIdentifier {
        return $this->identifier;
    }

	public function getDescription() : string{
		return $this->description;
	}

	public function setDescription(string $description) : void{
		$this->description = $description;
	}

	public function getChildEnchantmentId(): string {
		return "";
	}
}