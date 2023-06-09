<?php

declare(strict_types=1);

namespace skyblock\items\vanilla;

use muqsit\random\WeightedRandom;
use pocketmine\item\FishingRod;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\entity\projectile\FishingRodEntity;
use skyblock\events\player\PlayerBaitConsumeEvent;
use skyblock\events\player\PlayerFishEvent;
use skyblock\events\player\PlayerPreFishEvent;
use skyblock\items\ItemEditor;
use skyblock\items\lootbox\types\FishingLoottableLootbox;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\fishing\Bait;
use skyblock\Main;
use skyblock\misc\fishing\FishingHandler;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\misc\skills\FishingSkill;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class CustomFishingRod extends FishingRod {

	private static array $cache = [];

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		if(isset(self::$cache[$player->getName()])){
			/** @var FishingRodEntity $e */
			$e = self::$cache[$player->getName()][0];
			if(!$e->isClosed() && !$e->isFlaggedForDespawn()){
				if($e->isActive()){
					$this->onCatch($player, $e, self::$cache[$player->getName()][1]);
				}

				$e->flagForDespawn();

				self::$cache[$player->getName()] = null;
				return parent::onClickAir($player, $directionVector);
			}
		}

		$loc = $player->getLocation();
		$loc->y += 1;
		$e = new FishingRodEntity($loc, $player);
		$bait = $this->getAndConsumeBait($player);


		$event = new PlayerPreFishEvent($player, 100, 1.0);
		$bait?->onPreFish($event);
		$event->call();

		$e->setMaxFishTime($event->getOriginalFishTime());

		$e->handleHookCasting($player->getDirectionVector()->multiply(2), 2, 2);
		$e->setOwningEntity($player);
		$e->spawnToAll();
		self::$cache[$player->getName()] = [$e, $bait];



		return parent::onClickAir($player, $directionVector);
	}

	public function getAndConsumeBait(Player $player): ?Bait {
		foreach($player->getInventory()->getContents() as $k => $v){
			if(($b = SpecialItem::getSpecialItem($v)) instanceof Bait){
				$player->sendActionBarMessage("§r§7Consumed " . $v->getCustomName());

				$ev = new PlayerBaitConsumeEvent($player, $b);
				$ev->call();

				if($ev->isShouldConsume()){
					$v->pop();
					$player->getInventory()->setItem($k, $v);
				}

				return $b;
			}
		}

		return null;
	}

	public function onCatch(AetherPlayer $player, FishingRodEntity $entity, ?Bait $bait = null): void {
		$weighted = $this->getLootedWeightedRandom();
		$x = null;
		foreach($weighted->generate(1) as $reward) {
			$x = $reward;
		}
		$item = $x[0]; //Item
		$xp = $x[1]; //int
		$event = new PlayerFishEvent($player, $entity, $xp, [$item]);
		$bait?->onFish($event);
		$event->call();

		$player->getSkillData()->increaseSkillXp(FishingSkill::id(), $event->getFinalTotalFishingXP());

		$session = new Session($player);

		foreach($event->getRewards() as $reward){
			Utils::addItem($player, $reward);
			$player->sendMessage(Main::PREFIX . "Found§r§c " . $reward->getCount() . "x " . $reward->getName());
		}


		QuestHandler::getInstance()->increaseProgress(IQuest::FISH, $player, $session, $event);
	}

	public function setCount(int $count) : Item{
		if($count > $this->getMaxStackSize()) return $this;

		return parent::setCount($count);
	}

	public function getLootedWeightedRandom(): WeightedRandom {
		if(mt_rand(1, 20) === 1) { //5% good drops
			if(mt_rand(1, 10) === 1){ //10% of the 5% or 0.5% in total, great drops
				return FishingHandler::getInstance()->getRandomGreat();
			}

			return FishingHandler::getInstance()->getRandomGood();
		}

		return FishingHandler::getInstance()->getRandomNormal();
	}

	public function getMaxStackSize() : int{
		return 1;
	}

	public function getMiningEfficiency(bool $isCorrectTool) : float{
		return 10;
	}

	public static function getItem(int $level, int $xp = 0): Item {
		$item = VanillaItems::FISHING_ROD();
		$item->setCustomName("§r§l§fFishing Rod §r§f§l(§r§fLVL: §7{$level}§f§l)");
		$item->getNamedTag()->setInt("level", $level);
		$item->getNamedTag()->setInt("xp", $xp);
		ItemEditor::updateCosmetics($item);

		return $item;
	}
}