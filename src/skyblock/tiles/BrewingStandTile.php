<?php

declare(strict_types=1);

namespace skyblock\tiles;

use pocketmine\block\tile\Container;
use pocketmine\block\tile\ContainerTrait;
use pocketmine\block\tile\Tile;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\ItemIds;
use pocketmine\item\Potion;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use skyblock\items\potions\AetherPotionHandler;
use skyblock\items\potions\SkyBlockPotion;
use skyblock\items\SkyblockItems;

class BrewingStandTile extends Tile implements Container{
	use ContainerTrait;

	const TAG_LAST_INGREDIENT_UNIX = "tag_last_ingredient_unix";

	private SimpleInventory $inventory;

	private array $levelModifiers;
	private array $durationModifiers;

	public int $lastIngredientUnix;
	private string $lastIngredientProfile = "";

	public function __construct(World $world, Vector3 $pos){
		$this->lastIngredientUnix = time();
		parent::__construct($world, $pos);

		$this->levelModifiers = [
			1 => VanillaItems::GLOWSTONE_DUST(),
			2 => SkyblockItems::ENCHANTED_GLOWSTONE_DUST(),
			3 => SkyblockItems::ENCHANTED_GLOWSTONE(),
		];

		$this->durationModifiers = [
			8*60 => VanillaItems::REDSTONE_DUST(),
			16*60 => SkyblockItems::ENCHANTED_REDSTONE(),
			40*60 => SkyblockItems::ENCHANTED_REDSTONE_BLOCK(),
		];
		
		$this->inventory = new SimpleInventory(4); //0-3, slot 0 is the ingredient and the others are the potion slots
	}

	public function update(): bool {
		$ingredient = $this->getInventory()->getItem(0);

		if($ingredient->isNull()) {
			return false;
		}
		$output = AetherPotionHandler::getInstance()->getPotionByIngredient($ingredient);


		if($output === null && $ingredient->getId() !== ItemIds::NETHER_WART) {
			$found = false;
			foreach($this->durationModifiers as $durationModifier){
				if($durationModifier->equals($ingredient)){
					$found = true;
					break;
				}
			}

			foreach($this->levelModifiers as $durationModifier){
				if($durationModifier->equals($ingredient)){
					$found = true;
					break;
				}
			}

			if(!$found){
				return false;
			}
		}

		$passed = time() - $this->lastIngredientUnix > 3;
		$found = false;

		$set = false;
		for($i = 1; $i <= 3; $i++){
			$inside = $this->getInventory()->getItem($i);

			if($inside instanceof Potion){
				if($inside->getType()->id() === PotionType::AWKWARD()->id()){
					if($output !== null){
						if($passed){
							$this->getInventory()->setItem($i, $output);
							$set = true;
						}
						$found = true;
					}
				} elseif($inside->getType()->id() === PotionType::WATER()->id() && $ingredient->getId() === ItemIds::NETHER_WART){
					if($passed){
						$this->getInventory()->setItem($i, VanillaItems::AWKWARD_POTION());
						$set = true;
					}

					$found = true;
				}
			}

			if($inside instanceof SkyBlockPotion){
				if(!$inside->usedGlowstoneModifier()){
					foreach($this->levelModifiers as $lvl => $modifier){
						if($ingredient->equals($modifier)){
							if($passed){
								$this->getInventory()->setItem($i, $inside->setPotionLevel($inside->getPotionLevel() + $lvl)->setUsedGlowstoneModifier());
								$set = true;
							}

							$found = true;
						}
					}
				}

				if(!$inside->usedRedstoneModifier()){
					foreach($this->durationModifiers as $lvl => $modifier){
						if($ingredient->equals($modifier)){
							if($passed){
								$this->getInventory()->setItem($i, $inside->setDuration($inside->getDuration() + $lvl)->setUsedRedstoneModifier());
								$set = true;
							}

							$found = true;
						}
					}
				}
			}

		}

		if($set){
			$this->getInventory()->setItem(0, VanillaItems::AIR());
		}

		return $found;
	}

	public function readSaveData(CompoundTag $nbt) : void{
		$this->loadItems($nbt);

		$this->lastIngredientUnix = $nbt->getInt(self::TAG_LAST_INGREDIENT_UNIX, time());
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		$this->saveItems($nbt);

		$nbt->setInt(self::TAG_LAST_INGREDIENT_UNIX, $this->lastIngredientUnix);
	}

	public function getRealInventory() : SimpleInventory{
		return $this->inventory;
	}

	public function getInventory(): SimpleInventory{
		return $this->inventory;
	}
}