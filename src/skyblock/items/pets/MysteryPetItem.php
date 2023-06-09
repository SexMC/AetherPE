<?php

declare(strict_types=1);

namespace skyblock\items\pets;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\items\Equipment;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItemProperties;

class MysteryPetItem extends Equipment implements ItemComponents{
	use ItemComponentsTrait;

	const TYPE_ENCHANTED = "enchanted";
	const TYPE_SUPER_ENCHANTED = "super_enchanted";

	const TAG_TYPE = "tag_pet_type"; //enchanted or super enchanted, default: enchanted
	const TAG_PET = "tag_pet";

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("question_mark", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));
	}


	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		//TOOD: opening of this

		if($this->getPet()){
			$pet = $this->getPet();


		}

		return parent::onClickAir($player, $directionVector);
	}

	public function resetLore(array $lore = []) : void{

		if($this->getPet()){
			$pet = $this->getPet();

			$lore = [
				"§r§8" . $pet->getSkillClassByType() . " Pet",
			];
			
			$this->properties->setDescription([
				"§r§6Perks:",
				"§r§f§l???",
				"§r",
				"§r§6Quality:",
				"§r§f§l???"
			]);
			
			$this->properties->setRarity(($this->getType() === self::TYPE_ENCHANTED ? Rarity::rare() : Rarity::legendary()));
			$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Mystery " . $pet->getName());
		}


		parent::resetLore($lore);
	}


	public function setPet(Pet $pet): self {
		$this->getNamedTag()->setString(self::TAG_PET, $pet->getIdentifier());
		$pet->applyStats($this, 1, Pet::RARITY_UNCOMMON);



		$this->resetLore();

		return $this;
	}

	public function setType(string $type): self {
		$this->getNamedTag()->setString(self::TAG_TYPE, $type);

		$this->resetLore();

		return $this;
	}

	public function getType(): string {
		return $this->getNamedTag()->getString(self::TAG_TYPE, self::TYPE_ENCHANTED);
	}

	public function getPet(): ?Pet {
		$id = $this->getNamedTag()->getString(self::TAG_PET, "");

		if($id === "") return null;


		return PetHandler::getInstance()->getPet($id);
	}

	public function buildProperties() : SkyblockItemProperties{
		return new SkyblockItemProperties();
	}
}