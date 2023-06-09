<?php

declare(strict_types=1);

namespace skyblock\menus\items;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use skyblock\items\misc\PersonalCompactor4000;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedBeef;
use skyblock\items\special\types\crafting\EnchantedBone;
use skyblock\items\special\types\crafting\EnchantedCarrot;
use skyblock\items\special\types\crafting\EnchantedChicken;
use skyblock\items\special\types\crafting\EnchantedCoal;
use skyblock\items\special\types\crafting\EnchantedCobblestone;
use skyblock\items\special\types\crafting\EnchantedDiamond;
use skyblock\items\special\types\crafting\EnchantedEmerald;
use skyblock\items\special\types\crafting\EnchantedGold;
use skyblock\items\special\types\crafting\EnchantedGunpowder;
use skyblock\items\special\types\crafting\EnchantedHaybale;
use skyblock\items\special\types\crafting\EnchantedIron;
use skyblock\items\special\types\crafting\EnchantedLapis;
use skyblock\items\special\types\crafting\EnchantedMelon;
use skyblock\items\special\types\crafting\EnchantedMutton;
use skyblock\items\special\types\crafting\EnchantedObsidian;
use skyblock\items\special\types\crafting\EnchantedPorkchop;
use skyblock\items\special\types\crafting\EnchantedPotato;
use skyblock\items\special\types\crafting\EnchantedPumpkin;
use skyblock\items\special\types\crafting\EnchantedRabbitFoot;
use skyblock\items\special\types\crafting\EnchantedRedstone;
use skyblock\items\special\types\crafting\EnchantedRottenFlesh;
use skyblock\items\special\types\crafting\EnchantedSlimeball;
use skyblock\items\special\types\crafting\EnchantedSpiderEye;
use skyblock\items\special\types\crafting\EnchantedString;
use skyblock\items\special\types\crafting\EnchantedSugarcane;
use skyblock\items\special\types\PersonalCompactorItem;
use skyblock\menus\AetherMenu;
use skyblock\misc\recipes\RecipesHandler;

class PersonalCompactorMenu extends AetherMenu {

	private array $enchantedItems = [];

	public function __construct(private PersonalCompactor4000 $item){
		$this->enchantedItems = [
			SkyblockItems::ENCHANTED_COBBLESTONE(),
			SkyblockItems::ENCHANTED_OBSIDIAN(),
			SkyblockItems::ENCHANTED_COAL(),
			SkyblockItems::ENCHANTED_EMERALD(),
			SkyblockItems::ENCHANTED_GOLD(),
			SkyblockItems::ENCHANTED_LAPIS_LAZULI(),
			SkyblockItems::ENCHANTED_IRON(),
			SkyblockItems::ENCHANTED_DIAMOND(),
			SkyblockItems::ENCHANTED_REDSTONE(),


			SkyblockItems::ENCHANTED_CHICKEN(),
			SkyblockItems::ENCHANTED_GUNPOWDER(),
			SkyblockItems::ENCHANTED_RABBIT_FOOT(),
			SkyblockItems::ENCHANTED_PORKCHOP(),
			SkyblockItems::ENCHANTED_MUTTON(),
			SkyblockItems::ENCHANTED_STRING(),
			SkyblockItems::ENCHANTED_SPIDER_EYE(),
			SkyblockItems::ENCHANTED_ROTTEN_FLESH(),
			SkyblockItems::ENCHANTED_SLIMEBALL(),


			SkyblockItems::ENCHANTED_HAY_BALE(),
			SkyblockItems::ENCHANTED_CARROT(),
			SkyblockItems::ENCHANTED_POTATO(),
			SkyblockItems::ENCHANTED_PUMPKIN(),
			SkyblockItems::ENCHANTED_MELON(),
			SkyblockItems::ENCHANTED_SUGARCANE(),
		];
		

		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Personal Compactor");


		for($i = 0; $i <= 8; $i++){
			$menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
		}

		$menu->getInventory()->setItem(0, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));

		$menu->getInventory()->setItem(4, $this->getSelectedItem());

		foreach($this->enchantedItems as $v){
			$menu->getInventory()->addItem($v);
		}

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();

		if($slot === 0){
			$player->removeCurrentWindow();
			return;
		}


		if($slot > 8){
			$s = $slot - 9;

			if(isset($this->enchantedItems[$s])){
				$selected = $this->enchantedItems[$s];
				$foundSlot = null;

				foreach($player->getInventory()->getContents() as $k => $content){
					if($content->equals($this->item)){
						$foundSlot = $k;
						break;
					}
				}

				if($foundSlot === null){
					$player->sendMessage("§r§cError: Compactor not found in your inventory");
					$player->removeCurrentWindow();
					return;
				}

				$this->item = $this->item->setSelectedItem($selected);
				$player->getInventory()->setItem($foundSlot, $this->item);

				$this->getMenu()->getInventory()->setItem(4, $this->getSelectedItem());
			}
		}
	}


	public function getSelectedItem(): Item {

		$selected = $this->item->getSelectedItem();

		if($selected === null){
			$item = VanillaBlocks::IRON_BARS()->asItem();
			$item->setCustomName("§r§eSelect an item!");
			$item->setLore(["§r§7Select an item to compact."]);
			return $item;
		}

		return $selected->setCustomName("§r§eSelected item: " . $selected->getCustomName());

	}
}