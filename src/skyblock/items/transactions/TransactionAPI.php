<?php

declare(strict_types=1);

namespace skyblock\items\transactions;

use Closure;
use pocketmine\event\EventPriority;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\Server;
use skyblock\items\SkyblockItem;
use skyblock\items\transactions\handlers\MaskTransactionHandler;
use skyblock\Main;

class TransactionAPI {

	/** @var BaseTransactionHandler[]  */
	private array $handlers = [];


	public function __construct(){
		Server::getInstance()->getPluginManager()->registerEvent(
			InventoryTransactionEvent::class,
			Closure::fromCallable([$this, "onTransaction"]),
			EventPriority::HIGH,
			Main::getInstance()
		);

		//$this->registerHandler(new CustomEnchantBookTransactionHandler());
		//$this->registerHandler(new HolyIngotTransactionHandler());
		//$this->registerHandler(new EnchantmentDustTransactionHandler());
		//$this->registerHandler(new CustomEnchantmentExpanderTransactionHandler());
		//$this->registerHandler(new ItemModTransactionHandler());
		//$this->registerHandler(new ItemModExpanderTransactionHandler());
		//$this->registerHandler(new HeroicUpgradeTransactionHandler());
		$this->registerHandler(new MaskTransactionHandler());
		//$this->registerHandler(new ArachnesStackingSetTransactionHandler());
		//$this->registerHandler(new CarrotCandyTransactionHandler());
		//$this->registerHandler(new SellWandTransactionHandler());
		//$this->registerHandler(new DestructionEssenceTransactionHandler());
		//$this->registerHandler(new EnhancementAttributeTransactionHandler());
		//$this->registerHandler(new EnhancementAttributeBoosterTransactionHandler());
		//$this->registerHandler(new PetFoodTransactionHandler());
		//$this->registerHandler(new SplitItemTransactionHandler());
		//$this->registerHandler(new TrackerTransactionHandler());
	}

	public function registerHandler(BaseTransactionHandler $handler): void {
		$this->handlers[] = $handler;
	}

public function onTransaction(InventoryTransactionEvent $event): void {
		return;
		$transaction = $event->getTransaction();
		$player = $transaction->getSource();

		foreach($transaction->getActions() as $action){
			if($action instanceof SlotChangeAction){
				$itemClickedWith = $action->getSourceItem();
				$itemClicked = $action->getTargetItem();

				var_dump("itemclicked ID: " . $itemClicked->getId());
				var_dump("itemclickedwith ID: " . $itemClickedWith->getId());

				if($itemClickedWith instanceof SkyblockItem) {
					var_dump("is sb item");
					//if($itemClicked->isNull() || $itemClickedWith->isNull()) continue;
					var_dump("after");
					$levelAction = null;
					$action = null;

					foreach ($event->getTransaction()->getActions() as $a) {
						if ($a instanceof SlotChangeAction) {
							if ($itemClickedWith->equals($a->getSourceItem())) {
								$levelAction = $a;
								$itemClicked = $a->getTargetItem();
								var_dump("sets heresd");
							} else {
								$action = $a;
								$itemClicked = $a->getSourceItem();
								var_dump("no sets at action");
								//$itemClicked = $a->getSourceItem();
							}
						}
					}

					if($action !== null && $levelAction !== null){
						var_dump("here calls transaction");
						$itemClickedWith->onTransaction($player, $itemClicked, $itemClickedWith, $action, $levelAction, $event);
					}
				}
			}
		}
	}
}