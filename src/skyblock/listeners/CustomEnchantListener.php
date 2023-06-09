<?php

declare(strict_types=1);

namespace skyblock\listeners;

use Closure;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\customenchants\rare\Dominate;
use skyblock\customenchants\rare\Silence;
use skyblock\events\CustomEntityDamageByEntityEvent;
use skyblock\events\player\EntityHitBowEvent;
use skyblock\events\player\PlayerBaitConsumeEvent;
use skyblock\events\player\PlayerFishEvent;
use skyblock\events\player\PlayerPreFishEvent;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\customenchants\BaseToggleableEnchant;
use skyblock\items\itemskins\ItemSkin;
use skyblock\items\itemskins\ItemSkinHandler;
use skyblock\player\CustomPlayerArmorInventoryListener;
use skyblock\player\CustomPlayerInventoryListener;
use skyblock\utils\Utils;
use slapper\entities\SlapperEntity;

class CustomEnchantListener implements Listener {

	/**
	 * @param PlayerJoinEvent $event
	 * @priority NORMAL
	 */
	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$player->getInventory()->getListeners()->add(($listener = new CustomPlayerInventoryListener($player)));
		$player->getInventory()->getHeldItemIndexChangeListeners()->add(Closure::fromCallable([$listener, "onHeldItemIndexChange"]));
		$player->getArmorInventory()->getListeners()->add($list = new CustomPlayerArmorInventoryListener($player));
		Utils::executeLater(function() use($player, $list) {
			if($player->isOnline()){
				$list->onSlotChange($player->getArmorInventory(), ArmorInventory::SLOT_HEAD, VanillaItems::AIR());
				$list->onSlotChange($player->getArmorInventory(), ArmorInventory::SLOT_CHEST, VanillaItems::AIR());
				$list->onSlotChange($player->getArmorInventory(), ArmorInventory::SLOT_LEGS, VanillaItems::AIR());
				$list->onSlotChange($player->getArmorInventory(), ArmorInventory::SLOT_FEET, VanillaItems::AIR());
			}
		}, 10);
	}

    /**
     * @param PlayerQuitEvent $event
     * @priority NORMAL
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        foreach (array_merge($player->getArmorInventory()->getContents(), [$player->getInventory()->getItemInHand()]) as $item) {
            foreach ($item->getEnchantments() as $enchantmentInstance) {
                $enchant = $enchantmentInstance->getType();
                if ($enchantmentInstance instanceof CustomEnchantInstance && $enchant instanceof BaseToggleableEnchant && $enchant->isToggled($player)) {
                    $enchant->unToggle($player, $item, $enchantmentInstance);
                }
            }
        }
    }

	/**
	 * @param EntityDamageByEntityEvent $event
	 */
	public function onEntityDamage(EntityDamageByEntityEvent $event): void {
		$damager = $event->getDamager();

		if($event->getEntity() instanceof SlapperEntity) {
			$event->cancel();
			return;
		}

		if($damager instanceof Player){
			BaseReactiveEnchant::doReaction($damager, $event);
		}
	}

	public function entityHitBowEvent(EntityHitBowEvent $event): void {
		$damager = $event->getDamager();
		$e = $event->getEntity();
		$projectile = $event->getArrow();

		BaseReactiveEnchant::doReaction($damager, $event);

		if($e instanceof Player){
			BaseReactiveEnchant::doReaction($e, $event);
		}
	}


	public function onPlayerAttackPve(PlayerAttackPveEvent $event): void {
		$p = $event->getPlayer();

		BaseReactiveEnchant::doReaction($p, $event);
	}


	/**
	 * @param CustomEntityDamageByEntityEvent $event
	 * @priority NORMAL
	 */
	public function onCustomEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
		$damager = $event->getDamager();
		$entity = $event->getEntity();

		if($entity instanceof SlapperEntity) {
			$event->cancel();
			return;
		}

		if($event->getCause() === EntityDamageEvent::CAUSE_PROJECTILE){
			$projectile = $event->getChild();
			$entity = $event->getEntity();

			if($projectile instanceof Arrow){
				$owner = $projectile->getOwningEntity();

				if($owner instanceof Player){
					$damager = $owner;

					(new EntityHitBowEvent($damager, $entity, $projectile, $event))->call();
				}
			}
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 * @priority HIGH
	 */
	public function onBlockBreak(BlockBreakEvent $event): void {
		BaseReactiveEnchant::doReaction($event->getPlayer(), $event);
	}

	/**
	 * @param PlayerPreFishEvent $event
	 * @priority HIGH
	 */
	public function onPreFish(PlayerPreFishEvent $event): void {
		BaseReactiveEnchant::doReaction($event->getPlayer(), $event);
	}

	/**
	 * @param PlayerBaitConsumeEvent $event
	 * @priority HIGH
	 */
	public function onBaitConsume(PlayerBaitConsumeEvent $event): void {
		BaseReactiveEnchant::doReaction($event->getPlayer(), $event);
	}

	/**
	 * @param PlayerFishEvent $event
	 * @priority HIGH
	 */
	public function onFish(PlayerFishEvent $event): void {
		BaseReactiveEnchant::doReaction($event->getPlayer(), $event);
	}

	/**
	 * @param EntityEffectAddEvent $event
	 * @priority HIGH
	 */
	public function onEffectAdd(EntityEffectAddEvent $event): void {
		if(($p = $event->getEntity()) instanceof Player){
			BaseReactiveEnchant::doReaction($p, $event);
		}
	}

	/**
	 * @param EntityShootBowEvent $event
	 * @priority HIGH
	 */
	public function onShootBow(EntityShootBowEvent $event): void {
		if(($p = $event->getEntity()) instanceof Player){
			BaseReactiveEnchant::doReaction($p, $event);
		}

	}

	/**
	 * @param PlayerItemConsumeEvent $event
	 * @priority HIGH
	 */
	public function onItemConsume(PlayerItemConsumeEvent $event): void {
		if(($p = $event->getPlayer()) instanceof Player){
			BaseReactiveEnchant::doReaction($p, $event);
		}

	}

	/**
	 * @param EntityDeathEvent $event
	 * @priority HIGH
	 */
	public function onEntityDeath(EntityDeathEvent $event): void {
		$ent = $event->getEntity();
		$lastDmg = $ent->getLastDamageCause();
		if ($lastDmg instanceof EntityDamageByEntityEvent) {
			$damager = $lastDmg->getDamager();
			if ($damager instanceof Player) {
				BaseReactiveEnchant::doReaction($damager, $event);
			}
		}
	}
}