<?php

declare(strict_types=1);

namespace skyblock\items\masks\types;

use pocketmine\block\Block;
use pocketmine\block\utils\SkullType;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\particle\HugeExplodeParticle;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ItemEditor;
use skyblock\items\masks\IMaskHolder;
use skyblock\items\masks\Mask;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\misc\pve\PveDataRegenerator;
use skyblock\player\AetherPlayer;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\PveUtils;
use SOFe\AwaitGenerator\Await;

class ZombiesHeartMask extends Mask {
	use AwaitStdTrait;

	public function getRarity() : Rarity{
		return Rarity::rare();
	}

	public function getDesiredEvents() : array{
		return [ProjectileHitEvent::class];
	}

	public static function getItem() : Item{
		$item = SkyblockItems::MASK_ITEM();
		$item->setMask(self::getName());
		$item->setCustomName("§r§l§fMask \"§r§3§lZombie's Heart§f§l\"");
		$item->getProperties()->setDescription([
			"§r§bDoubles your " . PveUtils::getHealth() . "§r§b and",
			"§r§b" . PveUtils::getIntelligence() . "§r§b regeneration speeds",
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
		return "zombiesheartmask";
	}

	public function getBlock() : Block{
		return VanillaBlocks::MOB_HEAD()->setSkullType(SkullType::ZOMBIE());
	}

	public function getFormat() : string{
		return "§r§a§lZombie's Heart Mask";
	}

	public function tryCall(Event $event) : void{
	}

	public function onActivate(Player $player, Event $event) : void{
	}


	public function listenToCancelled() : bool{
		return true;
	}

	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onWear(Player $player, Item $old, Item $new) : bool{
		Await::f2c(function() use($player) {
			assert($player instanceof AetherPlayer);

			while($player->isOnline() && ItemEditor::getMask($player->getArmorInventory()->getHelmet()) instanceof ZombiesHeartMask){
				PveDataRegenerator::regenerateIntelligence($player);
				PveDataRegenerator::regenerateHealth($player);

				yield $this->getStd()->sleep(40);
			}
		});
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