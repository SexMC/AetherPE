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
use skyblock\utils\Utils;

class PetCollectionTakeMenu extends AetherMenu {

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

		$player = $this->player;

		$active = $player->getPetData()->getActivePetId();
		foreach($player->getPetData()->getPetCollection() as $pet){
			$i = $pet->buildPetItem();
			if($pet->getPet()->getIdentifier() === $active){
				ItemEditor::glow($i);
			}

			$lore = $i->getLore();
			$lore[] = "§r";
			$lore[] = "§r§eClick to take out.";
			$i->setLore($lore);

			$menu->getInventory()->addItem($i);
		}

		$menu->getInventory()->setItem(0, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To pet menu"]));



		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$player = $transaction->getPlayer();
		$item = $transaction->getItemClicked();
		//TODO: pagination for the future when there's lots of pets in the game


		assert($player instanceof AetherPlayer);

		if($slot === 0){
			(new PetCollectionMenu($player, $this))->send($player);
			return;
		}

		$pet = PetInstance::fromItem($item);

		if($pet === null) return;

		$active = $player->getPetData()->getActivePetId();
		$all = $player->getPetData()->getPetCollection();
		if($active === $pet->getPet()->getIdentifier()){
			$player->getPetData()->setActivePetId(null);
		}

		if(!isset($all[$pet->getPet()->getIdentifier()])) return;

		unset($all[$pet->getPet()->getIdentifier()]);
		$player->getPetData()->setPetCollection($all);


		$this->getMenu()->getInventory()->setItem($slot, VanillaItems::AIR());
		Utils::addItem($player, $pet->buildPetItem());
		PetHandler::getInstance()->updatePets($player);

	}
}