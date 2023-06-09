<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\inventory\Inventory;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\items\ItemEditor;
use skyblock\items\special\types\EnchantmentDustPouch;
use skyblock\items\special\types\XPBottleItem;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\utils\Utils;

class TinkererMenu extends AetherMenu {

	protected $type = self::NORMAL;

	private array $transactionSlots;
	private array $notAllowed;

	private bool $isAwaiting = false;
	private bool $done = false;

	protected bool $dupeCheck = false;

	public function __construct(){
		$this->transactionSlots = range(1, 26);
		$this->notAllowed = range(28, 53);



		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Tinkerer");

		$menu->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 14)->setCustomName("§aClick to accept trade"));
		$menu->getInventory()->setItem(27, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 5)->setCustomName(" "));

		return $menu;
	}

	public function onClose(Player $player, Inventory $inventory) : void{
		parent::onClose($player, $inventory);

		if($this->done === false){
			foreach($this->transactionSlots as $slot){
				$i = $this->getMenu()->getInventory()->getItem($slot);

				if($i->isNull()) continue;
				Utils::addItem($player, $i);
			}
			$player->sendMessage(Main::PREFIX . "Tinkerer trade was cancelled");
		}
	}

	public function onNormalTransaction(InvMenuTransaction $transaction) : InvMenuTransactionResult{
		$slot = $transaction->getAction()->getSlot();

		if($slot === 0 && $this->isAwaiting === false){
			if($this->getMenu()->getInventory()->getItem($slot)->getMeta() === 14){
				$this->done = true;
				$transaction->getPlayer()->removeCurrentWindow();

				foreach($this->notAllowed as $slot){
					$i = $this->getMenu()->getInventory()->getItem($slot);

					if($i->isNull()) continue;
					$i->getNamedTag()->removeTag("menuItem");


					Utils::addItem($transaction->getPlayer(), $i);
				}

				$transaction->getPlayer()->sendMessage(Main::PREFIX . "Good trade, see you soon!");

				return $transaction->discard();
			}
		}

		if(in_array($slot, $this->transactionSlots)){
			if(!$transaction->getIn()->isNull() && !$this->canAdd($transaction->getIn())){
				return $transaction->discard();
			}

			$this->isAwaiting = true;

			Utils::executeLater(function() : void{
				$this->tinker();
			}, 1);

			return $transaction->continue();
		}


		return $transaction->discard();
	}

	public function tinker(): void {
		$this->isAwaiting = false;

		foreach($this->notAllowed as $v){
			$this->getMenu()->getInventory()->clear($v);
		}

		foreach($this->transactionSlots as $slot){
			$item = $this->getMenu()->getInventory()->getItem($slot);

			if($item->isNull()) continue;

			if($item instanceof Armor || $item instanceof Tool){
				$count = 1;
				
				$count += count(ItemEditor::getCustomEnchantments($item));
				$count += count($item->getEnchantments());
				
				$this->getMenu()->getInventory()->setItem($slot + 27, XPBottleItem::getItem(20 * $count, "Tinkerer"));
			}
			
			if($item->getNamedTag()->getString("unique_book_id", "") !== ""){
				if(!isset(array_values(ItemEditor::getCustomEnchantments($item))[0])) continue;
				/** @var \skyblock\items\customenchants\CustomEnchantInstance $ce */
				$ce = array_values(ItemEditor::getCustomEnchantments($item))[0];
				if($ce !== null && $ce->getCustomEnchant()->getRarity()->getTier() < ICustomEnchant::RARITY_HEROIC){
					$this->getMenu()->getInventory()->setItem($slot + 27, EnchantmentDustPouch::getItem($ce->getCustomEnchant()->getRarity()->getTier()));
				}
			}
		}
	}

	public function canAdd(Item $item): bool {
		if($item instanceof Armor || $item instanceof Tool){
			if(!empty(ItemEditor::getCustomEnchantments($item))){
				return true;
			}

			if(!empty($item->getEnchantments())){
				return true;
			}
		}

		if($item->getNamedTag()->getString("unique_book_id", "") !== ""){
			return true;
		}


		return false;
	}
}