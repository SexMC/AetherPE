<?php

declare(strict_types=1);

namespace skyblock\items\masks\types;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Skin;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\ItemEditor;
use skyblock\items\masks\IMaskHolder;
use skyblock\items\masks\Mask;
use skyblock\items\PvEItemEditor;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\misc\skills\FarmingSkill;
use skyblock\player\AetherPlayer;
use skyblock\traits\StaticPlayerCooldownTrait;
use skyblock\utils\PveUtils;
use skyblock\utils\Utils;

//TODO: change these

class ChickenHeadMask extends Mask {
	use StaticPlayerCooldownTrait;
	public function getDesiredEvents() : array{
		return [
			PlayerToggleSneakEvent::class,
		];
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}

	public static function getItem() : Item{
		$item = SkyblockItems::MASK_ITEM();
		$item->setMask(self::getName());
		$item->setCustomName("§r§l§fMask \"§r§a§lChicken Head§f§l\"");
		$item->getProperties()->setDescription([
			"§r§bLay eggs when you sneak.",
			"§r§bHas a §a20§b second cooldown",
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
		return "chickenheadmask";
	}

	public function getBlock() : Block{
		return VanillaBlocks::DRAGON_EGG();
	}

	public function getFormat() : string{
		return "§r§a§lChicken Head Mask";
	}

	public function tryCall(Event $event) : void{
		assert($event instanceof PlayerToggleSneakEvent);

		$p = $event->getPlayer();

		$item = $p->getArmorInventory()->getHelmet();
		if($item instanceof IMaskHolder && $item->getMask() instanceof $this){
			$this->onActivate($p, $event);
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		if(!self::isOnCooldown($player)){
			self::setCooldown($player, 20);
			$player->sendActionBarMessage("§r§7§obuk, buk, buk, ba-gawk");
			Utils::addItem($player, VanillaItems::EGG());
		}
	}

	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onWear(Player $player, Item $old, Item $new) : bool{

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

		return true;
	}
}