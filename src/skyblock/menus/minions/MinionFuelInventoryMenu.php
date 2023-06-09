<?php

declare(strict_types=1);

namespace skyblock\menus\minions;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use skyblock\entity\minion\BaseMinion;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\minion\BioMinionFuel;
use skyblock\items\special\types\minion\CoalMinionFuel;
use skyblock\items\special\types\minion\DieselMinionFuel;
use skyblock\menus\AetherMenu;
use skyblock\player\ranks\AetherPlusRank;
use skyblock\player\ranks\AetherRank;
use skyblock\player\ranks\AstronomicalRank;
use skyblock\player\ranks\AuroraRank;
use skyblock\player\ranks\BaseRank;
use skyblock\player\ranks\HydraRank;
use skyblock\player\ranks\SinonRank;
use skyblock\player\ranks\TrojeRank;
use skyblock\sessions\Session;
use skyblock\utils\TimeUtils;
use skyblock\utils\Utils;

class MinionFuelInventoryMenu extends AetherMenu {

	private array $slots = [
		11, 12, 13, 14, 15,
		20, 21, 22, 23, 24
	];

	private array $rankNeeds = [
		14 => SinonRank::class,
		15 => TrojeRank::class,
		20 => HydraRank::class,
		21 => AuroraRank::class,
		22 => AetherRank::class,
		23 => AetherPlusRank::class,
		24 => AstronomicalRank::class,
	];

	public function __construct(
		private BaseMinion $minion,
		private AetherMenu $aetherMenu,
	){
		foreach($this->rankNeeds as $k => $v){
			$this->rankNeeds[$k] = new $v;
		}

		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu->getMenu();
		$menu->getInventory()->clearAll();


		$session = new Session($this->minion->getString("owner"));
		$topRank = $session->getTopRank();

		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK())->asItem()->setCustomName(" "));
		}



		$menu->setName("Minion Fuel");

		$defaultDuration = fn (string $class): int => match($class) {
			BioMinionFuel::class => 8 * 3600,
			CoalMinionFuel::class => 16 * 3600,
			DieselMinionFuel::class => 24 * 3600
		};

		foreach($this->minion->getFuelInventory()->getContents(true) as $k => $content){
			$slot = $this->slots[$k];

			if(isset($this->rankNeeds[$slot])){
				/** @var BaseRank $neededRank */
				$neededRank = $this->rankNeeds[$slot];
				if($topRank->getTier() < $neededRank->getTier()){
					$menu->getInventory()->setItem($slot, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::WHITE())->asItem()->setCustomName("§r§cUnlock this slot with the {$neededRank->getColour()}{$neededRank->getName()} Rank"));
					continue;
				}
			}

			if($content->isNull()){
				$menu->getInventory()->setItem($slot, $content);
				continue;
			}

			$lore = $content->getLore();
			$lore[] = "§r";
			$lore[] = "§r§cDuration left: " . TimeUtils::getFormattedTime($content->getNamedTag()->getInt("duration", $defaultDuration(SpecialItem::getSpecialItem($content)::class)));
			$content->setLore($lore);

			$menu->getInventory()->setItem($slot, $content);
		}

		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§cGo back to minion menu"));

		return $menu;
	}


	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$player = $transaction->getPlayer();

		if(in_array($slot, $this->slots)){
			$currentItemInSlot = $this->getMenu()->getInventory()->getItem($slot);
			if(!$currentItemInSlot->isNull()) return;

			$in = $transaction->getIn();
			$special = SpecialItem::getSpecialItem($in);

			if($special === null) return;
			$class = $special::class;

			if(!in_array($class, [BioMinionFuel::class, CoalMinionFuel::class, DieselMinionFuel::class])){
				return;
			}

			$this->getMenu()->getInventory()->setItem($slot, $in);
			$player->getInventory()->removeItem($in);

			if($player->getCursorInventory()->getItem(0)->equals($in)){
				$player->getCursorInventory()->clearAll();
			}

			$this->minion->getFuelInventory()->addItem($in);
		}

		if($slot === 49){
			(new MinionMenu($this->minion, $this))->send($player);
		}
	}
}