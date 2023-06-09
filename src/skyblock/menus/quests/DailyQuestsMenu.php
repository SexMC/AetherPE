<?php

declare(strict_types=1);

namespace skyblock\menus\quests;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use skyblock\menus\AetherMenu;
use skyblock\misc\quests\DailyQuestInstance;
use skyblock\misc\quests\QuestHandler;
use skyblock\player\AetherPlayer;

class DailyQuestsMenu extends AetherMenu {

	public function __construct(private AetherPlayer $player){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$this->menu = $menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setName("Daily Quests");

		$this->menu->getInventory()->setItem(4, VanillaBlocks::IRON_BARS()->asItem()->setCustomName(" "));

		$this->setupQuests();

		foreach($this->menu->getInventory()->getContents(true) as $k => $v){
			if($v->isNull()){
				$menu->getInventory()->setItem($k, VanillaBlocks::IRON_BARS()->asItem()->setCustomName(" "));
			}
		}


		return $menu;
	}

	public function setupQuests(): void {
		QuestHandler::getInstance()->checkDailyQuests($this->player);

		foreach($this->player->quests as $questList){
			foreach($questList as $quest) {
				if($quest instanceof DailyQuestInstance){
					$this->menu->getInventory()->addItem($quest->getMenuItem());
				}
			}
		}
	}
}