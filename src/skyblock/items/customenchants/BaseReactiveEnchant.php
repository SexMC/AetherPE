<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

use pocketmine\event\Event;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use skyblock\customenchants\rare\Silence;
use skyblock\events\CustomEntityDamageByEntityEvent;
use skyblock\items\ItemEditor;
use skyblock\traits\PlayerCooldownTrait;

abstract class BaseReactiveEnchant extends BaseCustomEnchant {

	use PlayerCooldownTrait;

    /** @var string[] */
    private array $events = [];

    public function getEvents(): array {
        return $this->events;
    }

	/**
	 * @param array $events
	 */
	public function setEvents(array $events) : void{
		$this->events = $events;
	}

    abstract public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance): void;

	/**
	 * @param Player                $player
	 * @param Event                 $event
	 * @param CustomEnchantInstance $enchantInstance
	 *
	 * @return bool if true do the negation in here
	 */
    abstract public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance): bool;

	/**
	 * @param Player $player
	 *
	 * @return BaseCustomEnchant[]
	 */
	public static function setupCustomReaction(CustomEntityDamageByEntityEvent $event): void {

	}

	private static function setupReactionChain(Player $player, Event $event): CustomEnchantReactionChain {
		$chain = new CustomEnchantReactionChain($event, $player, [], [], []);

		$all = self::getActivatingCustomEnchants($player, $event);
		foreach($all as $ce){
			if(is_array($ce)){
				foreach($ce as $c){
					$chain->addActivatingCustomEnchantment($c);
				}
			} else $chain->addActivatingCustomEnchantment($ce);
		}

		return $chain;
	}

	public static function doReaction(Player $player, Event $event): void {
		$chain = self::setupReactionChain($player, $event);
		$chain->execute();
	}


	/**
	 * @param Player $player
	 * @param Event  $event
	 *
	 * @return CustomEnchantInstance[]
	 */
	private static function getActivatingCustomEnchants(Player $player, Event $event): array {
		$class = $event::class;

		/** @var array<ceID, array<CustomEnchantInstance[]>> $v */
		$v = [];
		foreach(array_merge($player->getArmorInventory()?->getContents(), [$player->getInventory()?->getItemInHand()]) as $item){
			if($item instanceof ICustomEnchantable){
				foreach($item->getCustomEnchants() as $enchantInstance){
					$ce = $enchantInstance->getCustomEnchant();
					$id = $ce->getIdentifier()->getId();

					if($ce instanceof BaseReactiveEnchant && in_array($class, $ce->getEvents())){

						if ($enchantInstance->getLevel() < 1) {
							ItemEditor::removeCustomEnchant($item, $ce->getIdentifier()->getId());
							continue;
						}

						if($ce->preReaction($player, $event, $enchantInstance) === false){
							continue;
						}

						if(!$ce->getIdentifier()->isStackable()){
							if(isset($v[$id]) && ($v[$id][0]->getLevel() >= $enchantInstance->getLevel())){
								continue;
							}

							$v[$id] = [$enchantInstance];
							continue;
						}

						$v[$id][] = $enchantInstance;
					}
				}
			}
		}

		return $v;
	}
}