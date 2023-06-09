<?php

declare(strict_types=1);

namespace skyblock\items\lootbox\animations;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\world\sound\XpLevelUpSound;
use skyblock\items\lootbox\Lootbox;
use skyblock\logs\LogHandler;
use skyblock\logs\types\LootboxLog;
use skyblock\menus\AetherMenu;
use skyblock\utils\Utils;

class AetherCrateAnimation extends AetherMenu{

	private array $lootslots = [
		12, 13, 14,
		21, 22, 23,
		30, 31, 32
	];

	private array $final = [];

	private int $jackpotslot = 49;

	private array $alreadyAdded = [];

	public function __construct(private Lootbox $lootbox, private Player $player){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
		$menu->setName($this->lootbox::getName());

		for($i = 0; $i <= 53; $i++){
			if(in_array($i, $this->lootslots)){
				$chest = ItemFactory::getInstance()->get(ItemIds::ENDER_CHEST);
				$chest->getNamedTag()->setByte("rollchest", 1);
				$chest->setCustomName("§f§l???");
				$chest->setLore(["§7Click to redeem an item from", "§7your§f {$this->lootbox::getName()}§7 Aether Crate."]);
				$menu->getInventory()->setItem($i, $chest);
				continue;
			}

			if($i === $this->jackpotslot){
				$menu->getInventory()->setItem($this->jackpotslot, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 14)->setCustomName("§c§l???")->setLore([
					"§7Redeem all your other rewards to",
					"§7unlock this special jackpot item."
				]));
				continue;
			}

			$menu->getInventory()->setItem($i, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, mt_rand(1, 13))->setCustomName(" "));
		}

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$out = $transaction->getOut();
		$slot = $transaction->getAction()->getSlot();

		if($out->getNamedTag()->getByte("rollchest", 0) === 1){
			$this->startTicking($slot, 5, 16);
		}

		if($slot === $this->jackpotslot && $out->getId() === ItemIds::STAINED_GLASS_PANE && count($this->alreadyAdded) >= 9){
			$reward = $this->lootbox->getJackpotReward();

			$this->getMenu()->getInventory()->setItem($this->jackpotslot, $reward->getItem()->setCount(mt_rand($reward->getMinCount(), $reward->getMaxCount())));
			$this->player->broadcastSound(new XpLevelUpSound(30), [$this->player]);
		}
	}

	public function onClose(Player $player, Inventory $inventory) : void{
		parent::onClose($player, $inventory);

		$rewards = [];
		foreach($this->lootslots as $slot){
			$i = $this->menu->getInventory()->getItem($slot);

			if($i->getNamedTag()->getByte("rollchest", 0) === 1 || !in_array($slot, $this->final)){
				$reward = $this->lootbox->getRandomReward();
				while(isset($this->alreadyAdded[$reward->uuid])){
					$reward = $this->lootbox->getRandomReward();
				}
				$this->alreadyAdded[$reward->uuid] = $reward->uuid;

				$i = $reward->getItem();
				if($reward->getMinCount() !== $reward->getMaxCount()){
					$i->setCount(mt_rand($reward->getMinCount(), $reward->getMaxCount()));
				}
			}

			$rewards[] = $i;
		}

		if($jp = $this->menu->getInventory()->getItem($this->jackpotslot)){
			if($jp->getId() === ItemIds::STAINED_GLASS_PANE){
				$reward = $this->lootbox->getJackpotReward();

				$i = $reward->getItem();
				if($reward->getMinCount() !== $reward->getMaxCount()){
					$i->setCount(mt_rand($reward->getMinCount(), $reward->getMaxCount()));
				}

				$rewards[] = $i;
			}else $rewards[] = $jp;
		}

		$msg = "§r§f§l";
		$color = Utils::getRandomColor();

		$msg .= TextFormat::EOL . "§r§l$color(!) §r$color{$player->getName()} opened a {$this->lootbox::getItem()->getName()}$color and received:";
		foreach($rewards as $item){
			Utils::addItem($player, $item);
			$msg .= TextFormat::EOL . "§r§l$color * §r§f{$item->getCount()}x §d{$item->getName()}";
		}
		$msg .= TextFormat::EOL . "§r§f§l";

		Utils::announce($msg);

		LogHandler::getInstance()->log(new LootboxLog($player, $this->lootbox::getName(), $rewards));
	}

	public function playSound(Player $player) : void{
		$player->broadcastSound(new XpCollectSound(), [$player]);
	}

	public function onTick(int $slot, bool $lastTick) : bool{
		$random = $this->lootbox->getRandomReward();
		while(isset($this->alreadyAdded[$random->uuid])){
			$random = $this->lootbox->getRandomReward();
		}


		$item = $random->getItem();
		if($random->getMinCount() !== $random->getMaxCount()){
			$item->setCount(mt_rand($random->getMinCount(), $random->getMaxCount()));
		}

		if($lastTick){
			$this->alreadyAdded[$random->uuid] = $random->uuid;
			$this->final[] = $slot;
		}
		$this->getMenu()->getInventory()->setItem($slot, $item);


		for($i = 0; $i <= 53; $i++){
			if(in_array($i, $this->lootslots) || $i === $this->jackpotslot) continue;

			$this->getMenu()->getInventory()->setItem($i, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, mt_rand(1, 13))->setCustomName(" "));
		}

		if($this->player->isOnline()){
			if($lastTick){
				$this->player->broadcastSound(new XpLevelUpSound(30), [$this->player]);
			}else $this->player->broadcastSound(new XpCollectSound(), [$this->player]);
		}

		return true;


	}
}