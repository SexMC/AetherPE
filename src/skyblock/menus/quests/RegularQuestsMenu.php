<?php

declare(strict_types=1);

namespace skyblock\menus\quests;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;
use skyblock\misc\quests\DailyQuestInstance;
use skyblock\misc\quests\Quest;
use skyblock\misc\quests\QuestHandler;
use skyblock\misc\quests\QuestInstance;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use pocketmine\utils\TextFormat as C;

class RegularQuestsMenu extends AetherMenu {
	/** @var Quest[] */
	private array $chunks;
	/** @var int  */
	private int $currentPage = 0;

	public function __construct(private AetherPlayer $player, private Session $session)
	{
		parent::__construct();
	}


	public function onReadonlyTransaction(InvMenuTransaction $transaction): void
	{
		$item = $transaction->getOut();
		$slot = $transaction->getAction()->getSlot();

		if($item->getId() === ItemIds::ARROW && $slot === 26){
			$this->currentPage += 1;
			$this->buildInventory($this->menu);
		} elseif($item->getId() === ItemIds::ARROW && $slot === 18){
			$this->currentPage -= 1;
			$this->buildInventory($this->menu);
		}
	}

	public function constructMenu(): InvMenu
	{
		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$this->buildInventory($menu);

		return $menu;
	}

	public function buildInventory(InvMenu $menu): void {
		$inv = $menu->getInventory();
		$inv->clearAll();

		$session = $this->session;
		$level = $this->player->currentQuestIndex;
		$active = QuestHandler::getInstance()->getActiveRegularQuest($this->player);
		$progress = ($active?->progress) ?? 0;
		$black = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 15, 1)->setCustomName(" ");

		$this->chunks = array_chunk(QuestHandler::getInstance()->normalQuests, 18, true);
		$chunk = $this->chunks[$this->currentPage];
		$slot = 0;
		/**
		 * @var  $questLevel
		* @var Quest $quest */
		foreach ($chunk as $questLevel => $quest){
			$status = 14;
			if($level > $questLevel){
				$status = 13;
			} elseif($level === $questLevel){
				$status = 4;
			}

			$inv->setItem($slot, $quest->getMenuItem($progress, $status), false);
			$slot++;
		}

		foreach ($inv->getContents(true) as $index => $content){
			if($content->getId() === ItemIds::AIR){
				$inv->setItem($index, $black, false);
			}
		}

		if(isset($this->chunks[$this->currentPage + 1])) {
			$inv->setItem(26, VanillaItems::ARROW()->setCustomName(C::AQUA . C::BOLD . "Next page"));
		} else $inv->setItem(26, $black);

		if(isset($this->chunks[$this->currentPage - 1])) {
			$inv->setItem(18, VanillaItems::ARROW()->setCustomName(C::AQUA . C::BOLD . "Previous page"));
		} else $inv->setItem(18, $black);
	}
}