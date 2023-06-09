<?php

declare(strict_types=1);

namespace skyblock\menus\island;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use skyblock\islands\Island;
use skyblock\islands\upgrades\IslandUpgradeHandler;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\utils\CustomEnchantUtils;

class IslandUpgradeMenu extends AetherMenu {

	private array $slots = [0, 2, 10, 18, 20, 6, 16, 8, 24, 26];

	public function __construct(private Island $island){
		parent::__construct();
	}


	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setName("§c§lIsland Upgrades");

		$money = $this->island->getBankMoney();
		$regular = $this->island->getQuestTokens();
		$heroic = $this->island->getHeroicQuestTokens();
		$essence = $this->island->getBankEssence();

		$upgrades = array_values(IslandUpgradeHandler::getInstance()->getAllUpgrades());
		foreach($this->slots as $key => $slot){
			if(isset($upgrades[$key])){
				$menu->getInventory()->setItem($slot,
					$upgrades[$key]->getMenuItem($this->island->getIslandUpgrade($upgrades[$key]::getIdentifier()), $money, $regular, $heroic, $essence));
			}
		}

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$item = $transaction->getOut();
		$player = $transaction->getPlayer();

		if(($type = $item->getNamedTag()->getString("type", "")) !== ""){
			$upgrade = IslandUpgradeHandler::getInstance()->getUpgrade($type);

			if($upgrade === null) return;

			$level = $this->island->getIslandUpgrade($upgrade::getIdentifier());

			if($level >= $upgrade->getMaxLevel()){
				$player->sendMessage(Main::PREFIX . "§c{$upgrade->getName()}§7 upgrade is already maxed");
				$player->removeCurrentWindow();
				return;
			}

			$next = $upgrade->upgrades[$level + 1];

			$bank = $this->island->getBankMoney();
			$regular = $this->island->getQuestTokens();
			$heroic = $this->island->getHeroicQuestTokens();
			$essence = $this->island->getBankEssence();


			if($bank < $next->moneyCost){
				$player->sendMessage(Main::PREFIX . "There's not enough money in the island bank.");
				$player->removeCurrentWindow();
				return;
			}

			if($regular < $next->regularQuestTokenCost){
				$player->sendMessage(Main::PREFIX . "There's not enough regular quest tokens in the island bank.");
				$player->removeCurrentWindow();
				return;
			}

			if($heroic < $next->heroicQuestTokenCost){
				$player->sendMessage(Main::PREFIX . "There's not enough heroic quest tokens in the island bank.");
				$player->removeCurrentWindow();
				return;
			}

			if($essence < $next->essence){
				$player->sendMessage(Main::PREFIX . "There's not enough essence in the island bank.");
				$player->removeCurrentWindow();
				return;
			}

			$this->island->increaseIslandUpgrade($upgrade::getIdentifier(), 1);
			$this->island->decreaseHeroicQuestTokens($next->heroicQuestTokenCost);
			$this->island->decreaseBankMoney($next->moneyCost);
			$this->island->decreaseQuestTokens($next->regularQuestTokenCost);
			$this->island->decreaseBankEssence($next->essence);
			$this->island->announce("\n" . Main::PREFIX . "§c{$player->getName()}§7 has upgraded the {$upgrade->getMenuColor()}{$upgrade->getName()}§7 to level §a" . CustomEnchantUtils::roman($next->level) . "\n");
			$upgrade->onUpgrade($this->island);
			$player->removeCurrentWindow();
		}
	}
}