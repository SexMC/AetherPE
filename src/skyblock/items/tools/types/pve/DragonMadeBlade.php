<?php

declare(strict_types=1);

namespace skyblock\items\tools\types\pve;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\sound\BowShootSound;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\PvEItemEditor;
use skyblock\items\tools\SpecialWeapon;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\PlayerCooldownTrait;

class DragonMadeBlade extends SpecialWeapon {
	use PlayerCooldownTrait;
	use AwaitStdTrait;

	public function getDesiredEvents() : array{
		return [PlayerItemUseEvent::class];
	}

	public static function getItem() : Item{
		$item = VanillaItems::IRON_SWORD();
		$item->setCustomName("§r§l§8» §5Dragon-Made Blade §l« §r§7(Right-Click");
		$item->setUnbreakable();

		PvEItemEditor::setDamage($item, 35);
        PvEItemEditor::setStrength($item, 10);

		self::addNametag($item);

		return $item;
	}

	public static function getName() : string{
		return "DragonMadeBlade";
	}

	public function getExtraLore() : array{
		return [
            "§r§7It is I, the Wealthiest Man in Skyblock.",
            "§r",
            "§r§5§lABILITY \"§r§dWealthy King§l§5\"",
            "§r§d\"Deal damage by throwing coins at",
            "§r§dyour enemies!\"",
            "§r§b§lMANA COST \"§r§310§l§b\"",
            "§r",
            "§r§c§l» §r§cRequires Level 10 Gnideel' B Reputation",
            "§r§cto be able to be used!",
            "§r§c§l» §r§cObtained from Gnideel' B Reputation Shop!",
            "§r",
            "§r§5§l» EPIC WEAPON «",
		];
	}

	public function tryCall(Event $event) : void{
		if($event instanceof PlayerItemUseEvent){
			$p = $event->getPlayer();
			$hand = $p->getInventory()->getItemInHand();

			if($hand->getNamedTag()->getString(self::TAG_SPECIAL_TOOL, "") === self::getName()){
				$this->onActivate($p, $event);
			}
		}
	}

	/**
	 * @param AetherPlayer $player
	 * @param Event  $event
	 *
	 * @return void
	 */
	public function onActivate(Player $player, Event $event) : void{
		if($event instanceof PlayerItemUseEvent){
			$event->cancel();
			if($this->isOnCooldown($player)){
				return;
			}

			$manaCost = 10;

			$mana = $player->getPveData()->getIntelligence();
			if($mana < $manaCost){
				$player->sendActionBarMessage("§cNot enough mana");
				return;
			}

			$moneyCost = 35;
			$s = new Session($player);
			if($s->getPurse() < $moneyCost){
				$player->sendActionBarMessage("§cNot enough coins");
				return;
			}

			$s->decreasePurse($moneyCost);

			$player->getPveData()->setIntelligence($mana - $manaCost);
			$player->sendActionBarMessage("§c-{$manaCost} Mana");
            $player->broadcastSound(new BowShootSound());

			$origin = clone $player->getEyePos();
			$direction = $player->getDirectionVector();

			$loc = $origin;

			for($i = 0; $i <= 20; $i++){
				$loc = $loc->addVector($direction);
				$player->getWorld()->addParticle($loc, new BlockBreakParticle(VanillaBlocks::GOLD()));

				foreach($player->getWorld()->getNearbyEntities(new AxisAlignedBB($loc->x - 1, $loc->y - 1, $loc->z - 1, $loc->x + 1, $loc->y + 1, $loc->z + 1)) as $e){
					if($e instanceof PveEntity){
						(new PlayerAttackPveEvent($player, $e, 35))->call();
					}
				}
			}




			$this->setCooldown($player, 0.1);
		}
	}
}