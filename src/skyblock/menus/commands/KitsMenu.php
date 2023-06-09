<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\misc\kits\Kit;
use skyblock\misc\kits\KitHandler;
use skyblock\utils\TimeUtils;
use SOFe\AwaitGenerator\Await;

class KitsMenu extends AetherMenu {

	public static function create(Player $player) {
		Await::f2c(function() use ($player){
			(new self($player, yield KitHandler::getInstance()->getCooldownData($player->getName())))->send($player);
		});
	}

	public function __construct(private Player $player, private array $data){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);

		$this->menu = $menu;

		$items = [
			$this->getTraveler(),
			$this->getSinon(),
			$this->getTroje(),
			$this->getHydra(),
			$this->getTheseus(),
			$this->getAurora(),
			$this->getAether(),
			$this->getAetherPlus(),
			$this->getAstro(),
		];

		$slots = [
			0, 9, 18, 27, 36, 45, 8, 17, 26
		];

		foreach($items as $k => $i){
			$exploded = explode(" ", $i->getCustomName());

			$name = strtolower(TextFormat::clean($exploded[0] . " " . $exploded[1]));
			$kit = KitHandler::getInstance()->get($name);

			if($kit === null) continue;

			var_dump("left:", $this->getLeftCooldown($kit));
			$cd = $this->player->hasPermission($kit->getPermission()) ? ($this->getLeftCooldown($kit) <= 0 ? "§aClick to claim" : "§cKit is on cooldown for " . TimeUtils::getFormattedTime($this->getLeftCooldown($kit))) : "§lNo permissions";
			$menu->getInventory()->setItem($slots[$k], $i->setLore(str_replace("{cooldown}", $cd, $i->getLore())));
		}

		foreach($menu->getInventory()->getContents(true) as $slot => $content){
			if($content->isNull()){
				$menu->getInventory()->setItem($slot, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
			}
		}

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction): void {
		$item = $transaction->getOut();
		$player = $transaction->getPlayer();


		if(!$item->isNull()){
			$exploded = explode(" ", $item->getCustomName());

			$name = strtolower(TextFormat::clean($exploded[0] . " " . $exploded[1]));
			$kit = KitHandler::getInstance()->get($name);

			if($kit === null) return;

			$player->removeCurrentWindow();

			if(!$player->hasPermission($kit->getPermission())){
				$player->sendMessage(Main::PREFIX . "You don't have permissions for the {$kit->getName()}.");
				return;
			}

			if($this->getLeftCooldown($kit) > 0){
				$player->sendMessage(Main::PREFIX . "Kit is on cooldown for §c" . TimeUtils::getFormattedTime($this->getLeftCooldown($kit)));
				return;
			}


			$this->data[$kit->getName()] = time();

			Await::f2c(function() use($player, $kit){
				$success = yield KitHandler::getInstance()->setCooldownData($player->getName(), $this->data);

				if($player->isOnline()){
					if($success === true){
						$kit->give($player);

						$player->sendMessage(Main::PREFIX . "Successfully claimed kit " . $kit->getName());
					} else $player->sendMessage(Main::PREFIX . "An error occurred.");
				}
			});
		}
	}

	public function getLeftCooldown(Kit $kit): int {
		if(isset($this->data[$kit->getName()])){
			return $kit->getCooldown() -  (time() - $this->data[$kit->getName()]);
		}

		return 0;
	}

	public function getTraveler(): Item {
		$item = VanillaBlocks::GRASS()->asItem();
		$item->setCustomName("§r§l§7Traveler Kit §r§7(Click to Claim)");
		$item->setLore([
			"§r§7§oThe starter kit, not good, but it's",
			"§r§7§obetter than having nothing to start with.",
			"§r",
			"§r§f§lGuaranteed Loot (§r§7All§f§l)",
			"§r§l§f* §r§f1x §l§bStone Tools §r§7(Sharpness I, Efficiency I)",
			"§r§l§f* §r§f1x §l§bLeather Armor §r§7(Protection I)",
			"§r§l§f* §r§f1x §cLava Bucket",
			"§r§l§f* §r§f1x §bWater Bucket",
			"§r§l§f* §r§f16x §r§fWheat Seeds",
			"§r§l§f* §r§f8x §r§fBeetroot Seeds",
			"§r§l§f* §r§f4x §r§fPotato",
			"§r§l§f* §r§f16x §r§fSteak",
			"§r",
			"§r§f§lRandom Loot (§r§71 items§f§l)",
			"§r§l§f* §r§f1x §r§l§7Common §r§7Enchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1x §r§l§bElite §r§bEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1x §r§l§aRare §r§aEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1x §r§l§6Legendary §r§6Enchantment Book §r§7(Right-Click)",
			"§r",
			"§r§c§l#1 TIP: §r§cYou can use /eshop for vanilla enchantments.",
			"§r§c§l#2 TIP: §r§cEssence is gained from farming crops.",
			"§r§c§l#3 TIP: §r§cDo /ceshop to buy Custom Enchantments.",
			"§r§c§l#4 TIP: §r§cDo /guide for more tips.",
			"§r§c§lCOOLDOWN §r§c1 Day",
			"§r§c{cooldown}",
		]);

		return $item;
	}

	public function getSinon(): Item {
		$item = VanillaBlocks::STONE()->asItem();
		$item->setCustomName("§r§l§eSinon Kit §r§7(Click to Claim)");
		$item->setLore([
			"§r§7Sinon Kit! Not the best rank, but it's whatever.",
			"§r",
			"§r§f§lGuaranteed Loot (§r§7All§f§l)",
			"§r§l§f* §r§f1x §l§bIron Tools §r§7(Sharpness I, Efficiency I, Unbreaking I)",
			"§r§l§f* §r§f1x §l§bIron Armor §r§7(Protection I, Unbreaking I)",
			"§r§l§f* §r§f1x §l§bBow (Unbreaking I)",
			"§r§l§f* §r§f4x §l§gGolden Apple",
			"§r§l§f* §r§f16x §l§fSteak",
			"§r§l§f* §r§f64x §l§fArrow",
			"§r",
			"§r§f§lRandom Loot (§r§71 items§f§l)",
			"§r§l§f* §r§f1-2x §r§l§7Common §r§7Enchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-2x §r§l§bElite §r§bEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-2x §r§l§aRare §r§aEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-2x §r§l§6Legendary §r§6Enchantment Book §r§7(Right-Click)",
			"§r",
			"§r§c§lREQUIREMENT §r§eSinon Rank",
			"§r§c§lCOOLDOWN §r§c1 Day",
			"§r§c{cooldown}",
		]);
		
		return $item;
	}

	public function getTroje(): Item {
		$item = VanillaBlocks::COAL_ORE()->asItem();
		$item->setCustomName("§r§l§3Troje Kit §r§7(Click to Claim)");
		$item->setLore([
			"§r§7Troje Kit! Not the best rank, but it's whatever.",
			"§r",
			"§r§f§lGuaranteed Loot (§r§7All§f§l)",
			"§r§l§f* §r§f1x §l§bDiamond Tools §r§7(Sharpness I, Efficiency I, Unbreaking II)",
			"§r§l§f* §r§f1x §l§bDiamond Armor §r§7(Protection I, Unbreaking II)",
			"§r§l§f* §r§f1x §l§bBow §r§7(Power I, Unbreaking II)",
			"§r§l§f* §r§f8x §l§gGolden Apple",
			"§r§l§f* §r§f16x §l§fSteak",
			"§r§l§f* §r§f64x §l§fArrow",
			"§r",
			"§r§f§lRandom Loot (§r§71 items§f§l)",
			"§r§l§f* §r§f1-3x §r§l§7Common §r§7Enchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-3x §r§l§bElite §r§bEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-3x §r§l§aRare §r§aEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-3x §r§l§6Legendary §r§6Enchantment Book §r§7(Right-Click)",
			"§r",
			"§r§c§lREQUIREMENT §r§3Troje Rank",
			"§r§c§lCOOLDOWN §r§c1 Day",
			"§r§c{cooldown}",
		]);

		return $item;
	}

	public function getHydra(): Item {
		$item = VanillaBlocks::IRON_ORE()->asItem();
		$item->setCustomName("§r§l§dHydra Kit §r§7(Click to Claim)");
		$item->setLore([
			"§r§7Hydra Kit! Not the best rank, but it's whatever.",
			"§r",
			"§r§f§lGuaranteed Loot (§r§7All§f§l)",
			"§r§l§f* §r§f1x §l§bDiamond Tools §r§7(Sharpness II, Efficiency II, Unbreaking III)",
			"§r§l§f* §r§f1x §l§bDiamond Armor §r§7(Protection II, Unbreaking III)",
			"§r§l§f* §r§f1x §l§bBow §r§7(Power II, Unbreaking III)",
			"§r§l§f* §r§f12x §l§gGolden Apple",
			"§r§l§f* §r§f16x §l§fSteak",
			"§r§l§f* §r§f64x §l§fArrow",
			"§r",
			"§r§f§lRandom Loot (§r§71 items§f§l)",
			"§r§l§f* §r§f1-4x §r§l§7Common §r§7Enchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-4x §r§l§bElite §r§bEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-4x §r§l§aRare §r§aEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-4x §r§l§6Legendary §r§6Enchantment Book §r§7(Right-Click)",
			"§r",
			"§r§c§lREQUIREMENT §r§dHydra Rank",
			"§r§c§lCOOLDOWN §r§c1 Day",
			"§r§c{cooldown}",
		]);

		return $item;
	}

	public function getTheseus(): Item {
		$item = VanillaBlocks::GOLD_ORE()->asItem();
		$item->setCustomName("§r§l§6Theseus Kit §r§7(Click to Claim)");
		$item->setLore([
			"§r§7Theseus Kit! Not the best rank, but it's whatever.",
			"§r",
			"§r§f§lGuaranteed Loot (§r§7All§f§l)",
			"§r§l§f* §r§f1x §l§bDiamond Tools §r§7(Sharpness III, Unbreaking III, Efficiency III, Unbreaking III)",
			"§r§l§f* §r§f1x §l§bDiamond Armor §r§7(Protection III, Unbreaking III)",
			"§r§l§f* §r§f1x §l§bBow §r§7(Power III, Unbreaking III)",
			"§r§l§f* §r§f16x §l§gGolden Apple",
			"§r§l§f* §r§f16x §l§fSteak",
			"§r§l§f* §r§f64x §l§fArrow",
			"§r",
			"§r§f§lRandom Loot (§r§71 items§f§l)",
			"§r§l§f* §r§f1-5x §r§l§7Common §r§7Enchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-5x §r§l§bElite §r§bEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-5x §r§l§aRare §r§aEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-5x §r§l§6Legendary §r§6Enchantment Book §r§7(Right-Click)",
			"§r",
			"§r§c§lREQUIREMENT §r§6Theseus Rank",
			"§r§c§lCOOLDOWN §r§c1 Day",
			"§r§c{cooldown}",
		]);

		return $item;
	}

	public function getAurora(): Item {
		$item = VanillaBlocks::DIAMOND_ORE()->asItem();
		$item->setCustomName("§r§l§dAurora Kit §r§7(Click to Claim)");
		$item->setLore([
			"§r§7Aurora Kit! Not the best rank, but it's whatever.",
			"§r",
			"§r§f§lGuaranteed Loot (§r§7All§f§l)",
			"§r§l§f* §r§f1x §l§bDiamond Tools §r§7(Sharpness IV, Efficiency IV, Unbreaking III)",
			"§r§l§f* §r§f1x §l§bDiamond Armor §r§7(Protection IV, Unbreaking III)",
			"§r§l§f* §r§f1x §l§bBow §r§7(Power IV, Unbreaking III)",
			"§r§l§f* §r§f20x §l§gGolden Apple",
			"§r§l§f* §r§f16x §l§fSteak",
			"§r§l§f* §r§f64x §l§fArrow",
			"§r",
			"§r§f§lRandom Loot (§r§71 items§f§l)",
			"§r§l§f* §r§f1-6x §r§l§7Common §r§7Enchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-6x §r§l§bElite §r§bEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-6x §r§l§aRare §r§aEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-6x §r§l§6Legendary §r§6Enchantment Book §r§7(Right-Click)",
			"§r",
			"§r§c§lREQUIREMENT §r§dAurora Rank",
			"§r§c§lCOOLDOWN §r§c1 Day",
			"§r§c{cooldown}",
		]);

		return $item;
	}

	public function getAether(): Item {
		$item = VanillaBlocks::EMERALD_ORE()->asItem();
		$item->setCustomName("§r§l§bAether Kit §r§7(Click to Claim)");
		$item->setLore([
			"§r§7Aether Kit! Intergalactic Higher-Ups are",
			"§r§7starting to see you as a threat.",
			"§r",
			"§r§f§lGuaranteed Loot (§r§7All§f§l)",
			"§r§l§f* §r§f1x §l§bDiamond Tools §r§7(Sharpness V, Efficiency V, Unbreaking III)",
			"§r§l§f* §r§f1x §l§bDiamond Armor §r§7(Protection V, Unbreaking III)",
			"§r§l§f* §r§f1x §l§bBow §r§7(Power V, Unbreaking III)",
			"§r§l§f* §r§f24x §l§gGolden Apple",
			"§r§l§f* §r§f16x §l§fSteak",
			"§r§l§f* §r§f64x §l§fArrow",
			"§r",
			"§r§f§lRandom Loot (§r§71 items§f§l)",
			"§r§l§f* §r§f1-7x §r§l§7Common §r§7Enchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-7x §r§l§bElite §r§bEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-7x §r§l§aRare §r§aEnchantment Book §r§7(Right-Click)",
			"§r§l§f* §r§f1-7x §r§l§6Legendary §r§6Enchantment Book §r§7(Right-Click)",
			"§r",
			"§r§c§lREQUIREMENT §r§bAether Rank",
			"§r§c§lCOOLDOWN §r§c1 Day",
			"§r§c{cooldown}",
		]);

		return $item;
	}

	public function getAetherPlus(): Item {
		$item = VanillaBlocks::BEACON()->asItem();
		$item->setCustomName("§r§l§bAether§3+ §bKit §r§7(Click to Claim)");
		$item->setLore([
			"§r§7Aether+ Kit! Intergalactic Higher-Ups are",
			"§r§7starting to see you as a threat.",
			"§r",
			"§r§f§lLoot (§r§71 items§f§l)",
			"§r§l§f* §r§f1x §r§l§fLootbox: §r§l§bAether§3+",
			"§r",
			"§r§c§lREQUIREMENT §r§bAether§3+ §bRank",
			"§r§c§lCOOLDOWN §r§c1 Week",
			"§r§c{cooldown}",
		]);

		return $item;
	}

	public function getAstro(): Item {
		$item = VanillaBlocks::BEDROCK()->asItem();
		$item->setCustomName("§r§l§5§o+§r§5§l§o Astronomical Kit §r§l§5§o+§r§7 (Click to Claim)");
		$item->setLore([
			"§r§5Best Rank of them all, consider yourself",
			"§r§5part of the interdimensional government.",
			"§r",
			"§r§f§lLoot (§r§71 items§f§l)",
			"§r§l§f* §r§f1x §r§l§fLootbox: §r§l§5Astronomical",
			"§r",
			"§r§c§lREQUIREMENT §r§5Astronomical Rank",
			"§r§c§lCOOLDOWN §r§c1 Week",
			"§r§c{cooldown}",
		]);

		return $item;
	}
}