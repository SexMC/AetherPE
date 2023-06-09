<?php

declare(strict_types=1);

namespace skyblock\items\masks\types;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Skin;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ItemEditor;
use skyblock\items\masks\IMaskHolder;
use skyblock\items\masks\Mask;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\misc\skills\FarmingSkill;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;

class PufferfishMask extends Mask {
	public function getDesiredEvents() : array{
		return [PlayerAttackPveEvent::class];
	}

	public static function getItem() : Item{
		$item = SkyblockItems::MASK_ITEM();
		$item->setMask(self::getName());
		$item->setCustomName("§r§l§fMask \"§r§f§lPufferfish Mask§f§l\"");
		$item->getProperties()->setDescription([
			"§r§bWhile wearing, your attacks have",
			"§r§ba chance to deal §a10 damage",
			"§r§aplus 20%§b of your Strength to",
			"§r§bnearby enemies.",
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
		return "pufferfishmask";
	}

	public function getBlock() : Block{
		return VanillaBlocks::SPONGE();
	}

	public function getFormat() : string{
		return "§r§f§lPufferfish Mask";
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}

	public function tryCall(Event $event) : void{
		if($event instanceof PlayerAttackPveEvent){
			$p = $event->getPlayer();

			if(mt_rand(1, 20) === 1){
				if(isset($event->getData()[self::getName()])) return;


				$item = $p->getArmorInventory()->getHelmet();
				if($item instanceof IMaskHolder && $item->getMask() instanceof $this){
					$this->onActivate($p, $event);
				}
			}
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		assert($event instanceof PlayerAttackPveEvent);
		assert($player instanceof AetherPlayer);


		foreach($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy(5, 5, 5)) as $e){
			if($e instanceof PveEntity){
				$e = (new PlayerAttackPveEvent($player, $e, 10 + ($player->getPveData()->getStrength() * 0.2)));

				$e->setData([self::getName() => true]);
				$e->call();
			}
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
		$player->getPveData()->setStrength($player->getPveData()->getStrength() + 10);
		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + 20);

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
		$player->getPveData()->setStrength($player->getPveData()->getStrength() - 10);
		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() - 20);
		return true;
	}
}