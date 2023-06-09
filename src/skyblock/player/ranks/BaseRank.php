<?php

declare(strict_types=1);

namespace skyblock\player\ranks;

use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

abstract class BaseRank  {

	/** @var string */
	private string $name;
	/** @var string */
	private string $format;
	/** @var int */
	private int $tier;
	/** @var int */
	private int $discordGroupID;
	/** @var bool */
	private bool $isDefault;
	/** @var bool */
	private bool $isStaff;
	/** @var array */
	private array $permissions;
	/** @var string */
	private string $colour;
	/** @var int  */
	private int $ahLimit;
    /** @var bool */
	private bool $isPerm = false;

	private int $extraProfiles;

	public function __construct(array $data) {
		$this->loadData($data);
	}

	private function loadData(array $data): void {
		$this->name = $data["name"];
		$this->format = $data["format"];
		$this->tier = (int) $data["tier"] ?? 0;
		$this->discordGroupID = (int) $data["discordGroupID"] ?? 0;
		$this->isDefault = $data["isDefault"] ?? false;
		$this->isStaff = $data["isStaff"] ?? false;
		$this->permissions = $data["permissions"] ?? [];
		$this->colour = $data["colour"];
		$this->ahLimit = $data["ahLimit"] ?? 2;
		$this->extraProfiles = $data["extraProfiles"] ?? 0;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getMultiplier(): float
	{
		return 1;
	}

	public function getFormat(): string {
		return $this->format;
	}

	public function getExtraProfiles() : int{
		return $this->extraProfiles;
	}

	public function getAhLimit(): int
	{
		return $this->ahLimit;
	}

	public function getTier(): int {
		return $this->tier;
	}

	public function getDiscordGroupID(): int {
		return $this->discordGroupID;
	}

	public function isDefault(): bool {
		return $this->isDefault;
	}

	public function getColour(): string {
		return $this->colour;
	}

	public function isStaff(): bool {
		return $this->isStaff;
	}

	public function getPermissions(): array {
		return $this->permissions;
	}

    /**
     * @return Item[]
     */
    public function getReclaim(): array {
        return [];
    }

	public function isPerm() : bool{
		return $this->isPerm;
	}


	public function setIsPerm(bool $isPerm) : self{
		$this->isPerm = $isPerm;
		return $this;
	}
}