<?php

declare(strict_types=1);

namespace skyblock\menus\items;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use skyblock\items\ItemEditor;
use skyblock\items\pets\PetHandler;
use skyblock\items\pets\PetInstance;
use skyblock\menus\AetherMenu;
use skyblock\player\AetherPlayer;

class PetCollectionMenu extends AetherMenu {

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$menu->setName("Pets");



		$i = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");
		foreach(array_merge(range(0, 17), [18, 27, 36, 45, 26, 35, 44, 53]) as $slot){
			$menu->getInventory()->setItem($slot, $i);
		}

		$p = $this->player;
		$active = $p->getPetData()->getActivePetId();
		foreach($p->getPetData()->getPetCollection() as $pet){
			$i = $pet->buildPetItem();
			if($pet->getPet()->getIdentifier() === $active){
				ItemEditor::glow($i);
			}

			$menu->getInventory()->addItem($i);
		}

		$menu->getInventory()->setItem(0, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock menu"]));
		$menu->getInventory()->setItem(1, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§aTake out pets")->setLore(["§r§7Click to take out pets."]));



		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$player = $transaction->getPlayer();
		$item = $transaction->getItemClicked();

		assert($player instanceof AetherPlayer);

		//TODO: pagination for the future when there's lots of pets in the game


		if($slot === 0){
			(new SkyblockMenu($player, $this))->send($player);
			return;
		}

		if($slot === 1){
			(new PetCollectionTakeMenu($player, $this))->send($player);
			return;
		}

		$pet = PetInstance::fromItem($item);

		if($pet === null) return;

		$active = $player->getPetData()->getActivePetId();
		if($active === $pet->getPet()->getIdentifier()){
			$player->getPetData()->setActivePetId(null);
			ItemEditor::removeGlow($item);
			$this->getMenu()->getInventory()->setItem($slot, $item);
			PetHandler::getInstance()->updatePets($player);
			return;
		}

		if($active !== null){
			foreach($this->getMenu()->getInventory()->getContents() as $s => $content){
				$p = PetInstance::fromItem($content);
				if(!$p) continue;

				if($p->getPet()->getIdentifier() === $active){
					$this->getMenu()->getInventory()->setItem($s, $p->buildPetItem());
					break;
				}
			}
		}

		$player->getPetData()->setActivePetId($pet->getPet()->getIdentifier());
		ItemEditor::glow($item);
		$this->getMenu()->getInventory()->setItem($slot, $item);
		PetHandler::getInstance()->updatePets($player);
	}
}