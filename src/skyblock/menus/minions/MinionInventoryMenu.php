<?php

declare(strict_types=1);

namespace skyblock\menus\minions;

use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use skyblock\entity\minion\BaseMinion;
use skyblock\menus\common\ViewPagedItemsMenu;
use skyblock\utils\Utils;

class MinionInventoryMenu extends ViewPagedItemsMenu {

	protected $type = self::NORMAL;

	protected bool $dupeCheck = false;

	public function __construct(private BaseMinion $minion, string $menuName, array $contents){
		parent::__construct($menuName, $contents);
	}

	public function onNormalTransaction(InvMenuTransaction $transaction) : InvMenuTransactionResult{
		$slot = $transaction->getAction()->getSlot();
		if($slot === 52){
			if(isset($this->entries[$this->currentPage - 1])){
				$this->currentPage--;

				$this->updateEntries();
				$this->setCurrentPageItems(true);
				return $transaction->discard();
			}

			return $transaction->discard();
		} elseif($slot === 53){
			if(isset($this->entries[$this->currentPage + 1])){
				$this->currentPage++;

				$this->updateEntries();
				$this->setCurrentPageItems(true);

				return $transaction->discard();
			}

			return $transaction->discard();
		}


		if($transaction->getItemClicked()->equals($transaction->getItemClickedWith()) && $transaction->getItemClickedWith()->getCount() === $transaction->getItemClicked()->getCount()){
			return $transaction->continue();
		}

		if ($transaction->getItemClickedWith()->getId() !== ItemIds::AIR) {
			return $transaction->discard();
		}

		if($transaction->getItemClicked()->getId() !== ItemIds::AIR){
			$this->updateEntries();
		}

		return $transaction->continue();
	}

	public function setCurrentPageItems(bool $update = false) : void{
		parent::setCurrentPageItems();
	}

	public function updateEntries(): void {
		Utils::executeLater(function() : void{
			if(isset($this->entries[$this->currentPage])){
				$this->entries[$this->currentPage] = $this->getMenu()->getInventory()->getContents();
				unset($this->entries[$this->currentPage][53]);
				unset($this->entries[$this->currentPage][52]);
			}
		}, 1);
	}

	public function onClose(Player $player, Inventory $inventory) : void{
		parent::onClose($player, $inventory);

		$array = [];
		foreach($this->entries as $v){
			$array = array_merge($array, $v);
		}
		$this->minion->getMinionInventory()->setContents($array);
	}
}