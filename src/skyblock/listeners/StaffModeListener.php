<?php

declare(strict_types=1);

namespace skyblock\listeners;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use skyblock\communication\CommunicationData;
use skyblock\forms\staff\GetPlayerInfoForm;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class StaffModeListener implements Listener {

    /**
     * @param PlayerJoinEvent $event
     * @priority HIGH
     *
    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $session = new Session($player);
        if ($session->isStaffMode()) {
			$session->enableStaffMode();
        }
    }*/

    /**
     * @param PlayerMoveEvent $event
     * @priority HIGH
     */
    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();

		if(!$player instanceof AetherPlayer) return;

        if ($player->frozen) {
            $event->cancel();
            if (!$player->isImmobile()) {
                $player->setImmobile();
            }
        }
    }

    /**
     * @param InventoryTransactionEvent $event
     * @priority HIGH
     */
    public function onInventoryTransaction(InventoryTransactionEvent $event): void {
        $player = $event->getTransaction()->getSource();
        $session = new Session($player);

		if(!$player instanceof AetherPlayer) return;
        if ($player->inStaffMode && !$player->hasPermission("skyblock.staffmode.inventory.edit") && !Server::getInstance()->isOp($player->getName())) {
            $event->cancel();
        }
    }

    /**
     * @param EntityItemPickupEvent $event
     * @priority HIGH
     */
    public function onPickup(EntityItemPickupEvent $event): void {
        $entity = $event->getEntity();
		if ($entity instanceof AetherPlayer && $entity->inStaffMode) {
			$event->cancel();
		}
    }

    /**
     * @param EntityDamageEvent $event
     * @priority HIGH
     */
    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof AetherPlayer && $entity->inStaffMode) {
			$event->cancel();
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     * @priority HIGH
     */
    public function onEntityDamageByEntityEvent(EntityDamageByEntityEvent $event): void {
        $entity = $event->getDamager();
		if ($entity instanceof AetherPlayer && $entity->inStaffMode) {
			$event->cancel();
		}
    }

}