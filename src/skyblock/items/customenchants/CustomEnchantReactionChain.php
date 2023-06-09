<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\items\customenchants\modifiers\ReactionModifier;

class CustomEnchantReactionChain {

	/**
	 * @param Event         $event
	 * @param Player        $player
	 * @param CustomEnchantInstance[] $activatingEnchants array<CE ID: string, array<CE>> the array value is an array so stackable ces can activate multiple times
	 * @param array         $modifiers
	 * @param CustomEnchantInstance[]         $deactivatedEnchants
	 */
	public function __construct(
		private Event $event,
		private Player $player,
		private array $activatingEnchants,
		private array $modifiers,
		private array $deactivatedEnchants,
	){ }

	public function execute(): void {
		/**
		 * @var string                                                      $modifierID
		 * @var \skyblock\items\customenchants\modifiers\ReactionModifier[] $modifierArray */
		foreach($this->modifiers as $modifierID => $modifierArray){
			$modifierArray[0]->handle($modifierArray, $this);
		}

		foreach($this->getActivatingEnchants() as $ceID => $ceArray){
			foreach($ceArray as $instance){
				/** @var BaseReactiveEnchant $ce */
				$ce = $instance->getCustomEnchant();

				if($ce->getIdentifier()->isImportant()){
					$this->getPlayer()->sendMessage($ce->getActivateMessage($this->getPlayer()));
				}

				$ce->Reaction($this->getPlayer(), $this->getEvent(), $instance);
			}
		}
	}


	/**
	 * @return array
	 */
	public function getActivatingEnchants() : array{
		return $this->activatingEnchants;
	}

	/**
	 * @return Event
	 */
	public function getEvent() : Event{
		return $this->event;
	}

	/**
	 * @return array
	 */
	public function getModifiers() : array{
		return $this->modifiers;
	}

	/**
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}

	/**
	 * @return array
	 */
	public function getDeactivatedEnchants() : array{
		return $this->deactivatedEnchants;
	}

	public function addModifier(ReactionModifier $modifier): void {
		$id = $modifier::getId();

		if(!isset($this->modifiers[$id])){
			$this->modifiers[$id] = [];
		}

		$this->modifiers[$id][] = $modifier;
	}

	public function addActivatingCustomEnchantment(CustomEnchantInstance $instance): void {
		$id = $instance->getCustomEnchant()->getIdentifier()->getId();

		if(!isset($this->activatingEnchants[$id])){
			$this->activatingEnchants[$id] = [];
		}

		$this->activatingEnchants[$id][] = $instance;
	}

	/**
	 * @param string $ceID
	 *
	 * @return void
	 */
	public function deactivateCustomEnchantment(string $ceID): void {
		if(!isset($this->activatingEnchants[$ceID])){
			return;
		}

		$this->deactivatedEnchants[$ceID] = $this->activatingEnchants[$ceID];
		unset($this->activatingEnchants[$ceID]);
	}
}