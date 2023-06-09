<?php

declare(strict_types=1);

namespace skyblock\items\lootbox\animations;

use muqsit\invmenu\InvMenu;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\world\sound\XpLevelUpSound;
use SebastianBergmann\CodeCoverage\Report\PHP;
use skyblock\items\lootbox\Lootbox;
use skyblock\logs\LogHandler;
use skyblock\logs\types\LootboxLog;
use skyblock\menus\AetherMenu;
use skyblock\utils\Utils;

class LootboxAnimation extends AetherMenu {

	private Lootbox $lootbox;
	/** @var int[] */
	private array $lootSlots = [];
	/** @var int[] */
	private array $sideSlots = [];

	private int $ticksLeft;

	private bool $ended = false;

	private Player $player;

	public function __construct(Lootbox $lootbox, Player $player){
		$this->lootbox = $lootbox;
		$this->player = $player;

		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setName($this->lootbox::getName());

		$this->lootSlots = match ($this->lootbox->getBasicLootRewardCount() + $this->lootbox->getJackpotLootRewardCount()){
			1 => [13],
			2 => [12, 14],
			3 => range(12, 14),
			4 => range(11, 14),
			5 => range(11, 15),
			6 => range(10, 15),
			7 => range(10, 16),
		};

		foreach(range(9, 17) as $i){
			if(!in_array($i, $this->lootSlots)){
				$this->sideSlots[] = $i;

				$menu->getInventory()->setItem($i, $this->getSideItem());
			}
		}

		$this->startTicking(9, 20, ($this->ticksLeft = 8), 10);

		foreach($this->lootSlots as $i){
			$this->startTicking($i, 15, $this->ticksLeft, 10);
		}



		return $menu;
	}

	public function onTick(int $slot, bool $lastTick) : bool{
		if($slot === 9){
			$this->ended = $lastTick;
			$this->ticksLeft--;

			foreach($this->sideSlots as $slot){
				$this->menu->getInventory()->setItem($slot, $this->menu->getInventory()->getItem($slot)->setCount($this->ticksLeft));
			}

			if($lastTick){
				$this->player->broadcastSound(new XpLevelUpSound(30), [$this->player]);
			} else $this->player->broadcastSound(new XpCollectSound(), [$this->player]);

			return true;
		}

		if(in_array($slot, $this->lootSlots)){
			$reward = $this->lootbox->getRandomReward(true);
			$i = $reward->getItem();
			if($reward->getMinCount() !== $reward->getMaxCount()){
				$i->setCount(mt_rand($reward->getMinCount(), $reward->getMaxCount()));
			}

			$this->menu->getInventory()->setItem($slot, $i);
			return true;
		}

		return $this->ended;
	}

	public function onClose(Player $player, Inventory $inventory) : void{

		parent::onClose($player, $inventory);
		$msg = "§r§f§l";
		$color = Utils::getRandomColor();

		$msg .= TextFormat::EOL . "§r§l$color(!) §r$color{$player->getName()} opened a {$this->lootbox::getItem()->getName()}$color and received:";

		$rewards = [];
		foreach($this->lootSlots as $slot){

			if($this->ended === true){
				$item = $inventory->getItem($slot);
			} else $item = $this->lootbox->getRandomReward(true)->getItem();

			$msg .= TextFormat::EOL . "§r§l$color * §r§f{$item->getCount()}x §d{$item->getName()}";

			Utils::addItem($player, $item);
			$rewards[] = $item;
		}
		$msg .= TextFormat::EOL . "§r§f§l";

		if($this->lootbox->shouldAnnounce()){
			Utils::announce($msg);
		} else $player->sendMessage($msg);

		$this->ended = true;

		LogHandler::getInstance()->log(new LootboxLog($player, $this->lootbox::getName(), $rewards));
	}

	public function getSideItem(): Item {
		$item = VanillaBlocks::ELEMENT_ZERO()->asItem();
		$item->setCustomName("§c???");
		$item->setCount(8);

		return $item;
	}
}