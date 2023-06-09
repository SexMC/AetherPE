<?php

declare(strict_types=1);

namespace skyblock\menus\skills;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\Block;
use pocketmine\block\StainedGlassPane;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;
use skyblock\misc\skills\Skill;
use skyblock\player\AetherPlayer;
use skyblock\utils\CustomEnchantUtils;

class SkillsMenu extends AetherMenu {

	private $fillingSlots = [
		9, 18, 27, 28, 29,
		20, 11, 2, 3, 4, 13,
		22, 31, 32, 33, 24, 15,
		6, 7, 8, 17, 26,
		35, 44, 53,
	];

	private int $add = 0;

	public function __construct(
		private AetherPlayer $player,
		private Skill $skill,
		private ?AetherMenu $aetherMenu = null,
	){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();

		$menu->setName($this->skill::id() . " Skill");
		$level = $this->player->getSkilldata()->getSkillLevel($this->skill::id()) ;
		$xp = $this->player->getSkilldata()->getSkillXp($this->skill::id()) ;

		foreach($this->fillingSlots as $index => $slot){
			$lvl = $index+1 + $this->add;

			$itemBlock = $lvl % 5 === 0 ? $this->skill->getMenuItemEvery5Levels() : VanillaBlocks::STAINED_GLASS_PANE();
			$string = ["§r"];
			$color = "§a";
			if($level >= $lvl){
				if($itemBlock instanceof StainedGlassPane){
					$itemBlock->setColor(DyeColor::LIME());
				}
				$string[] = "§r§aUNLOCKED";
			} elseif($level === $lvl-1){
				if($itemBlock instanceof StainedGlassPane){
					$itemBlock->setColor(DyeColor::YELLOW());
				}
				$color = "§e";

				$string[] = "§r§7Progress: §e" . number_format(100 / $this->skill->getXpForLevel($level+1) * $xp) . "%";
				$string[] = "§r§e" . number_format($xp) . "§6/§e" . number_format($this->skill->getXpForLevel($level+1));
			} else {
				if($itemBlock instanceof StainedGlassPane){
					$itemBlock->setColor(DyeColor::RED());
				}
				$string = [];
				$color = "§c";
			}

			if($itemBlock instanceof Block){
				$item = $itemBlock->asItem();
			} else $item = $itemBlock;

			//ItemEditor::glow($item);
			$item->setCount($lvl);

			$item->setCustomName("§r$color" .  ucwords($this->skill::id()) . " Level " . CustomEnchantUtils::roman($lvl));
			$item->setLore(array_merge($this->skill->getMenuLore($this->player, $lvl), $string));

			$menu->getInventory()->setItem($slot, $item);
		}

		$item = $this->skill->getBaseItem($this->player);
		$menu->getInventory()->setItem(0, $item->setLore(array_merge($item->getLore(), [
			"§r",
		"§r§8Increase your " . $this->skill::id() . " level to",
		"§r§8unlock perks, statistic bonuses,",
		"§r§8and more!",
		])));

		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo back")->setLore(["§r§7To your skills"]));
		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem());
		$menu->getInventory()->setItem(50, VanillaItems::ARROW()->setCustomName("§r§aLevels 26 - 50")->setLore(["§r§eClick to view!"]));

		foreach($menu->getInventory()->getContents(true) as $slot => $content){
			if($content->isNull()){
				$i = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName("§r ");
				$i->getNamedTag()->setString("unique", uniqid());
				$menu->getInventory()->setItem($slot, $i);
			}
		}

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$item = $transaction->getItemClicked();
		$slot = $transaction->getAction()->getSlot();

		if($item->getId() === ItemIds::BARRIER){
			$player->removeCurrentWindow();
		}

		if($slot === 48){
			(new SkillsBrowseMenu($player, $this));
		}

		if($slot === 50){
			if($this->add === 0){
				$this->add = 25;
				$menu = $this->constructMenu();
				$this->getMenu()->getInventory()->setContents($menu->getInventory()->getContents());

				$this->getMenu()->getInventory()->setItem(50, VanillaItems::ARROW()->setCustomName("§r§aLevels 1 - 25")->setLore(["§r§eClick to view!"]));
			} else {
				$this->add = 0;
				$menu = $this->constructMenu();
				$this->getMenu()->getInventory()->setContents($menu->getInventory()->getContents());
				$this->getMenu()->getInventory()->setItem(50, VanillaItems::ARROW()->setCustomName("§r§aLevels 26 - 50")->setLore(["§r§eClick to view!"]));
			}
		}

	}
}