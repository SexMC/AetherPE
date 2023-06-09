<?php

declare(strict_types=1);


namespace skyblock\misc\collection;

use LogicException;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\misc\skills\CombatSkill;
use skyblock\misc\skills\FarmingSkill;
use skyblock\misc\skills\FishingSkill;
use skyblock\misc\skills\ForagingSkill;
use skyblock\misc\skills\MiningSkill;
use skyblock\misc\trades\Trade;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\CustomEnchantUtils;

abstract class Collection {

	abstract public function getItem(): Item;
	abstract public function onLevelUp(Player $player, int $oldLevel, int $newLevel): void;

	abstract public function getUnlockRecipes(): array;
	abstract public function getName(): string;

	public function levelUp(AetherPlayer $player): void {
		$s = $player->getCurrentProfile()->getProfileSession();
		$lvl = $s->getCollectionLevel($this->getName());

		$s->setCollectionLevel($this->getName(), $lvl+1);
		$s->setCollectionCount($this->getName(), $s->getCollectionCount($this->getName()) - self::getNeededForLevel($lvl + 1));

		$this->onLevelUp($player, $lvl, $lvl+1);

		$rewards = $this->getUnlockRecipes()[$lvl+1];
		$player->sendMessage("§r§e" . str_repeat("-", 10));
		$player->sendMessage("§r§l§6COLLECTION LEVEL UP §r§e" . $this->getName() . " " . CustomEnchantUtils::roman($lvl+1));
		$player->sendMessage("§r");
		$player->sendMessage("§r§l§aREWARDS");
		foreach(self::getRewardsAsString($this->getUnlockRecipes()[$lvl+1]) as $string){
			$player->sendMessage("§r§g- $string");
		}
		$player->sendMessage("§r§e" . str_repeat("-", 10));


		if(!is_array($rewards)){
			$rewards = [$rewards];
		}
		foreach($rewards as $item){
			var_dump($item);
			if($item instanceof Item){

				$recipe = RecipesHandler::getInstance()->getRecipeByItem($item);

				if($recipe === null){
					throw new LogicException("Unknown recipe reward in collection {$this->getName()}");
				}

				$player->getCurrentProfile()->getProfileSession()->unlockRecipe($recipe->getName());
				var_dump("unlocks recipe {$recipe->getName()}");

				if($item->getId() === ItemIds::SPAWN_EGG && str_contains(strtolower($item->getCustomName()), "minion")){
					for($i = 2; $i <= 11; $i++){
						$r = RecipesHandler::getInstance()->getRecipe(str_replace(CustomEnchantUtils::roman(1), CustomEnchantUtils::roman($i), $recipe->getName()));
						if($r === null){
							throw new LogicException("Unknown recipe reward in collection {$this->getName()}");
						}

						$player->getCurrentProfile()->getProfileSession()->unlockRecipe($r->getName());
						var_dump("unlocks recipe {$r->getName()}");
					}
				}
			}

			if($item instanceof Trade){
				$player->getCurrentProfile()->getProfileSession()->unlockTrade($item->getId());
				var_dump("unlocks trade {$item->getId()}");
			}

			if(is_string($item)){
				$amount = (int) preg_replace('/[^0-9]/', '', $item);
				$skill = match(true) {
					str_contains("mining", $item) => MiningSkill::id(),
					str_contains("combat", $item) => CombatSkill::id(),
					str_contains("fishing", $item) => FishingSkill::id(),
					str_contains("foraging", $item) => ForagingSkill::id(),
					str_contains("farming", $item) => FarmingSkill::id(),
					default => null,
				};

				if($skill === null) continue;

				$player->getSkillData()->increaseSkillXp($skill, $amount);
				var_dump("increases skill xp skill: $skill   amount: $amount");
			}
		}
	}

	public static function getRewardsAsString(Item|Trade|array $rewards): array {
		if($rewards instanceof Item){
			return [$rewards->getCustomName() . "§r§7 Recipe"];
		}

		if($rewards instanceof Trade){
			return [$rewards->getId() . " Trade"];
		}

		$s = [];
		foreach($rewards as $reward){
			if($reward instanceof Item){
				$s[] = $reward->getCustomName() . "§r§7 Recipe";
			} else $s[] = $reward;
		}

		return $s;
	}


	public function getMaxLevel(): int {
		return sizeof($this->getUnlockRecipes());
	}

	public static function getNeededForLevel(int $lvl): int {
		return match($lvl) {
			1 => 50,
			2 => 100,
			3 => 250,
			4 => 1000,
			5 => 5000,
			6 => 15000,
			7 => 30000,
			8 => 50000,
			9 => 100000,
			10 => 250000,
			11 => 500000,
			12 => 1000000,
		};
	}
}