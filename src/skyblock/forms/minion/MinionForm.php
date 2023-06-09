<?php

declare(strict_types=1);

namespace skyblock\forms\minion;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\entity\minion\BaseMinion;
use skyblock\entity\minion\types\MinerMinion;
use skyblock\entity\minion\types\SlayerMinion;
use skyblock\items\special\types\minion\MinerMinionSpawnEggItem;
use skyblock\items\special\types\minion\SlayerMinionSpawnEggItem;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class MinionForm extends MenuForm {

	public function __construct(private BaseMinion $minion, string $text){
		parent::__construct("Minion Form", $text, $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button): void {
		$btn = $this->getOption($button);

		switch(explode("\n", strtolower(TextFormat::clean($btn->getText())))[0]){
			case "open inventory":
				$this->minion->openInventory($player);
				break;//todo:
			case "rotate":
				$this->minion->lookAt($player->getPosition());
				break;
			case "pick up":
				if($this->minion instanceof SlayerMinion){
					$egg = SlayerMinionSpawnEggItem::getItem(
						$this->minion->getInt("level", $this->minion->getInt("level", 1)),
						$this->minion->getInt("xp", $this->minion->getInt("xp", 0)),
						$this->minion->getInt("speed", $this->minion->getInt("speed", 80)),
						$this->minion->getInt("looting", $this->minion->getInt("looting", 0)),
						$this->minion->getInt("durability", $this->minion->getInt("durability", $this->minion->sword->getMaxDurability())),
						$this->minion->getInt("maxDurability", $this->minion->getInt("maxDurability", $this->minion->sword->getMaxDurability())),
						$this->minion->getInt("size", $this->minion->getInt("size", 54)),
					);

					$this->minion->flagForDespawn();
					$player->sendMessage(Main::PREFIX . "Successfully picked up minion");
					Utils::addItem($player, $egg);
				}
				if($this->minion instanceof MinerMinion){

					$egg = MinerMinionSpawnEggItem::getItem(
						$this->minion->getInt("level", $this->minion->getInt("level", 1)),
						$this->minion->getInt("xp", $this->minion->getInt("xp", 0)),
						$this->minion->getInt("speed", $this->minion->getInt("speed", 80)),
						$this->minion->getInt("fortune", $this->minion->getInt("fortune", 0)),
						$this->minion->getInt("durability", $this->minion->getInt("durability", VanillaItems::DIAMOND_AXE()->getMaxDurability())),
						$this->minion->getInt("maxDurability", $this->minion->getInt("maxDurability", VanillaItems::DIAMOND_AXE()->getMaxDurability())),

						$this->minion->getInt("size", $this->minion->getInt("size", 54)),
					);

					$this->minion->flagForDespawn();
					$player->sendMessage(Main::PREFIX . "Successfully picked up minion");
					Utils::addItem($player, $egg);
				}
				break;
			case "collect essence":
				if($this->minion instanceof MinerMinion){
					$essence = $this->minion->getInt("essence", 0);

					if($essence <= 0){
						$player->sendMessage(Main::PREFIX . "There's no essence to collect");
						return;
					}

					$this->minion->setInt("essence", 0);
					$session = new Session($player);
					$session->increaseEssence($essence);
					$player->sendMessage(Main::PREFIX . "Collected §c" . number_format($essence) . " essence");
				}
				break;
			case "repair pickaxe":
				if($this->minion instanceof MinerMinion){
					$session = new Session($player);
					if($session->getEssence() <= 200){
						$player->sendMessage(Main::PREFIX . "You need §c200§7 essence to repair the miners pickaxe");
						return;
					}

					$session->decreaseEssence(200);
					$this->minion->setInt("durability", $this->minion->getInt("maxDurability", VanillaItems::DIAMOND_AXE()->getMaxDurability()));
					$player->sendMessage(Main::PREFIX . "Successfully repaired the pickaxe");
				}
				break;
			case "repair sword":
				if($this->minion instanceof SlayerMinion){
					$session = new Session($player);
					if($session->getEssence() <= 200){
						$player->sendMessage(Main::PREFIX . "You need §c200§7 essence to repair the miners pickaxe");
						return;
					}

					$session->decreaseEssence(200);
					$this->minion->setInt("durability", $this->minion->getInt("maxDurability", $this->minion->sword->getMaxDurability()));
					$player->sendMessage(Main::PREFIX . "Successfully repaired the sword");
				}
				break;
			case "collect xp":
				if($this->minion instanceof SlayerMinion && $player instanceof AetherPlayer){
					$essence = $this->minion->getInt("collectableXP", 0);

					if($essence <= 0){
						$player->sendMessage(Main::PREFIX . "There's no xp to collect");
						return;
					} //TODO: pick up

					$this->minion->setInt("collectableXP", 0);
					$player->getXpManager()->addXp($essence, true, false);
					$player->sendMessage(Main::PREFIX . "Collected §c" . number_format($essence) . " xp");
				}
				break;
		}
	}

	public function getButtons(): array {
		$array = [
			new MenuOption("Open Inventory"),
			new MenuOption("Rotate"),
			new MenuOption("Pick Up"),
			new MenuOption("Close Menu"),
		];

		if($this->minion instanceof MinerMinion){
			array_unshift($array, new MenuOption("Repair Pickaxe\n§cCosts 200 essence"));
			array_unshift($array, new MenuOption("Collect Essence"));
		}

		if($this->minion instanceof SlayerMinion) {
			array_unshift($array, new MenuOption("Repair Sword\n§cCosts 200 essence"));
			array_unshift($array, new MenuOption("Collect XP"));
		}

		return $array;
	}
}