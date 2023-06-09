<?php

declare(strict_types=1);

namespace skyblock\menus\skills;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\misc\skills\CombatSkill;
use skyblock\misc\skills\FarmingSkill;
use skyblock\misc\skills\FishingSkill;
use skyblock\misc\skills\ForagingSkill;
use skyblock\misc\skills\MiningSkill;
use skyblock\misc\skills\Skill;
use skyblock\player\AetherPlayer;

class SkillsBrowseMenu extends AetherMenu {

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("§r§d§lSkills");

		$glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem();
		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, $glass);
		}

		$this->addSkillItem(20, new MiningSkill());
		$this->addSkillItem(21, new CombatSkill());
		$this->addSkillItem(22, new FarmingSkill());
		$this->addSkillItem(23, new FishingSkill());
		$this->addSkillItem(24, new ForagingSkill());

		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock menu"]));

		return $menu;
	}

	public function addSkillItem(int $slot, Skill $skill): void {
		$i = $skill->getBaseItem($this->player);
		$lore = $i->getLore();
		$lore[] = "§r";
		$lore[] = "§r§eClick to view!";

		$i->setLore($lore);
		$i->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 1));

		$this->getMenu()->getInventory()->setItem($slot, $i);
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$player = $transaction->getPlayer();

		if($slot === 20){
			(new SkillsMenu($player, new MiningSkill(), $this));
		}

		if($slot === 21){
			(new SkillsMenu($player, new CombatSkill(), $this));
		}

		if($slot === 22){
			(new SkillsMenu($player, new FarmingSkill(), $this));
		}

		if($slot === 23){
			(new SkillsMenu($player, new FishingSkill(), $this));
		}

		if($slot === 24){
			(new SkillsMenu($player, new ForagingSkill(), $this));
		}

		if($slot === 48){
			(new SkyblockMenu($this->player, $this))->send($this->player);
		}
	}
}