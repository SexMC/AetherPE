<?php

declare(strict_types=1);

namespace skyblock\items\masks\types;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\events\pve\PveAttackPlayerEvent;
use skyblock\items\ItemEditor;
use skyblock\items\masks\IMaskHolder;
use skyblock\items\masks\Mask;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;


//this is tested and works fine
class SpiderMask extends Mask {
	public function getDesiredEvents() : array{
		return [PveAttackPlayerEvent::class];
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}

	public static function getItem() : Item{
		$item = SkyblockItems::MASK_ITEM();
		$item->setMask(self::getName());
		$item->setCustomName("§r§l§fMask \"§r§a§lSpider Mask§f§l\"");
		$item->getProperties()->setDescription([
			"§r§a+25% " . PveUtils::getCritChance(),
			"§r",
			"§r§bWhen worn, spiders and cave",
			"§r§bspiders deal §a-30%§b damage",
			"§r§bto enemies within §a8§b blocks.",
			"§r",
			"§r§7§oAttach this mask to any helmet",
			"§r§7§oto give it a visual override!",
			"§r",
			"§r§7To equip, place this mask on a helmet.",
			"§r§7To remove, use /removemask while holding the helmet.",
		]);

		self::addNameTag($item);


		return $item;
	}

	public static function getName() : string{
		return "spidermask";
	}

	public function getBlock() : Block{
		return VanillaBlocks::COBWEB();
	}

	public function getFormat() : string{
		return "§r§a§lSpider Mask";
	}

	public function tryCall(Event $event) : void{
		if($event instanceof PveAttackPlayerEvent){
			$p = $event->getPlayer();
			$e = $event->getEntity();


			$item = $p->getArmorInventory()->getHelmet();
			if($item instanceof IMaskHolder && $item->getMask() instanceof $this && in_array($e->getNetworkID(), [EntityIds::SPIDER, EntityIds::CAVE_SPIDER])){ //20% proc chance
				$this->onActivate($p, $event);
			}
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		assert($event instanceof PveAttackPlayerEvent);
		$event->divideDamage(0.3, "spider mask");
	}



	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onWear(Player $player, Item $old, Item $new) : bool{
		$player->getPveData()->setCritChance($player->getPveData()->getCritChance() + 25);
		return true;
	}

	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onTakeOff(Player $player, Item $old, Item $new) : bool{
		$player->getPveData()->setCritChance($player->getPveData()->getCritChance() - 25);
		return true;
	}
}