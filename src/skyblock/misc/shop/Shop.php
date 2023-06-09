<?php

declare(strict_types=1);

namespace skyblock\misc\shop;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use skyblock\items\lootbox\Lootbox;
use skyblock\items\special\SpecialItem;
use skyblock\items\vanilla\CustomFishingRod;
use skyblock\logs\LogHandler;
use skyblock\logs\types\SellLog;
use skyblock\Main;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\misc\shop\categories\BlocksCategory;
use skyblock\misc\shop\categories\ConcreteCategory;
use skyblock\misc\shop\categories\FarmingCategory;
use skyblock\misc\shop\categories\FoodCategory;
use skyblock\misc\shop\categories\GlassCategory;
use skyblock\misc\shop\categories\PotionCategory;
use skyblock\misc\shop\categories\SpawnerCategory;
use skyblock\misc\shop\categories\TerracottaCategory;
use skyblock\misc\shop\categories\UtilityCategory;
use skyblock\misc\shop\categories\WoodCategory;
use skyblock\misc\shop\categories\WoolCategory;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class Shop {

	use SingletonTrait;

	private array $categories = [];

	private array $itemsByHashId = [];

	private array $itemsByName = [];
	/** @var array<string, SellEntry> */
	private array $sell = [];

	/**
	 * @return SellEntry[]
	 */
	public function getAllSellEntries() : array{
		return $this->sell;
	}

	public function getSellEntry(string $key): ?SellEntry {
		return $this->sell[$key] ?? null;
	}

	public function getSellPrice(Item $item): int {
		if($item->getNamedTag()->getString(SpecialItem::TAG_SPECIAL_ITEM, "") !== "") {
			return 0;
		}

		if($item->getNamedTag()->getString("lootbox", "") !== "") {
			return 0;
		}

		if($item->getNamedTag()->getByte("fish", -1) === 1){
			return 1000;
		}

		$key = "{$item->getId()}:{$item->getMeta()}";

		if(($entry = $this->getSellEntry($key)) !== null){
			if(!$entry->isMobDrop()){
				return $entry->getPrice();
			}

			if($item->getNamedTag()->getByte("md", 0) === 1){
				return $entry->getPrice();
			}
		}

		return 0;
	}

	/**
	 * @param Player $player
	 * @param Item   $item
	 *
	 * @return int returns the gain
	 */
	public function sellItem(Player $player, Item $item): int {
		$gain = 0;
		if(($price = $this->getSellPrice($item)) > 0){
			$gain += $price * $item->getCount();
		}

		$session = $player->getCurrentProfile()->getPlayerSession($player);
		$session->increaseMoney($gain);

		QuestHandler::getInstance()->increaseProgress(IQuest::SELL, $player, $session, [$item]);
		LogHandler::getInstance()->log(new SellLog($player, $gain, [$item]));

		return $gain;
	}

	/**
	 * @param Player    $player
	 * @param Inventory $inventory
	 *
	 * @return int returns the total gains
	 */
	public function sellInventory(Player $player, Inventory $inventory): int {
		$gain = 0;
		$sold = [];

		foreach($inventory->getContents() as $slot => $content){
			if(($price = $this->getSellPrice($content)) > 0){
				$sold[] = $content;
				$gain += $price * $content->getCount();

				$inventory->clear($slot);
			}
		}

		$session = $player->getCurrentProfile()->getPlayerSession($player);
		$session->increaseMoney($gain);
		QuestHandler::getInstance()->increaseProgress(IQuest::SELL, $player, $session, $sold);
		LogHandler::getInstance()->log(new SellLog($player, $gain, $sold));

		return $gain;
	}


	public function __construct(){
		$data = yaml_parse(stream_get_contents(Main::getInstance()->getResource("sell.yml")));

		foreach($data as $k => $price){
			$drop = false;
			$sell = -1;
			$meta = 0;
			$id = $k;



			if(is_string($k)){
				if(strpos($k, ":") !== false){
					$meta = (int) explode(":", (string) $k)[1];
					$id = (int) explode(":", (string) $k)[0];

					if($meta === 38){
						$drop = true;
						$meta = 0;
					}
				}
			}
				if(str_contains(":", (string) $k)){
					$meta = (int) explode(":", (string) $k)[1];
					$id = (int) explode(":", (string) $k)[0];

					if($meta === 38){
						$drop = true;
						$meta = 0;
					}
				}

			if($sell === -1){
				$sell = (int) $price;
			}

			$this->sell["$id:$meta"] = new SellEntry(ItemFactory::getInstance()->get((int) $id, (int) $meta), $sell, $drop);
		}

		$count = count($this->sell);
		Main::debug("Loaded $count sell prices");

		$this->addCategory(new BlocksCategory());
		$this->addCategory(new ConcreteCategory());
		$this->addCategory(new GlassCategory());
		//$this->addCategory(new WoodCategory());
		$this->addCategory(new FarmingCategory());
		$this->addCategory(new WoolCategory());
		$this->addCategory(new UtilityCategory());
		$this->addCategory(new TerracottaCategory());
		$this->addCategory(new FoodCategory());
		$this->addCategory(new SpawnerCategory());
		$this->addCategory(new PotionCategory());
	}

	public function addCategory(ShopCategory $category): void {
		$this->categories[$category->getCategoryName()] = $category;

		foreach($category->getItems() as $item){
			$this->itemsByHashId[$item->getId()] = $item;
			$this->itemsByName[str_replace(" ", "_", strtolower($item->getItem()->getName()))] = $item;
		}
	}

	/**
	 * @return ShopItem[]
	 */
	public function getAllItemsByName() : array{
		return $this->itemsByName;
	}

	/**
	 * @return ShopItem[]
	 */
	public function getAllItemsByHashId() : array{
		return $this->itemsByHashId;
	}

	/**
	 * @return ShopCategory[]
	 */
	public function getAllCategories() : array{
		return $this->categories;
	}

	public static function buy(AetherPlayer $player, ShopItem $item, int $amount): void {
		$amount = abs($amount);

		$clone = clone $item->getItem();
		$add = $player->getInventory()->getAddableItemQuantity($clone->setCount(55 * 64));

		if($amount > $add){
			$amount = $add;
		}

		if($amount <= 0){
			$player->sendMessage(Main::PREFIX . "You have no empty space in your inventory");
			return;
		}

		$originalAmount = $amount;
		$price = $amount * $item->getBuyPrice();

		$session = $player->getCurrentProfile()->getPlayerSession($player);

		if($session->getPurse() < $price){
			$player->sendMessage(Main::PREFIX . "You need §c$" . number_format($price) . "§7 to buy §c{$amount}x§7 {$item->getItem()->getName()}");
			return;
		}

		$session->decreasePurse($price);

		if($amount > $item->getItem()->getMaxStackSize()){
			$iter = 0;

			while(true){
				if(++$iter >= 80) {
					Main::debug("Shop iteration forcely killed");
					break;
					return;
				}

				if($amount <= 0) break;

				$r = $item->getItem()->getMaxStackSize();
				if($amount <= $item->getItem()->getMaxStackSize()) {
					$r = $amount;
				}

				$amount -= $item->getItem()->getMaxStackSize();

				$i = clone $item->getItem();
				$i->setCount($r);

				$player->getInventory()->addItem($i);
			}
		} else {
			$i = clone $item->getItem();
			$player->getInventory()->addItem($i->setCount($amount));
		}

		$player->sendMessage(Main::PREFIX . "Successfully bought §c{$originalAmount}x {$item->getItem()->getName()} §7for §c$" . number_format($price));
	}
}