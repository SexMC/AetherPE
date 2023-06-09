<?php

declare(strict_types=1);

namespace skyblock\player;

use Closure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use skyblock\utils\Utils;

class AetherEffect extends EffectInstance {

	private ?Closure $closure = null;

	public function decreaseDuration(int $ticks): EffectInstance
	{
		$return = parent::decreaseDuration($ticks);

		if($this->hasExpired() && $this->getClosure() !== null){
			$this->getClosure()();


			$this->closure = null;
		}

		return $return;
	}

	/**
	 * @param Closure $closure
	 */
	public function setClosure(Closure $closure): void
	{
		$this->closure = $closure;
	}

	/**
	 * @return Closure
	 */
	public function getClosure(): ?Closure
	{
		return $this->closure;
	}

	public static function fromPlayer(Player $player, EffectInstance $effectInstance): self {
		$effect = $player->getEffects()->get($effectInstance->getType());

		if($effect !== null){
			$effect = clone $effect;
		}

		$add = new AetherEffect($effectInstance->getType(), $effectInstance->getDuration(), $effectInstance->getAmplifier(), $effectInstance->isVisible(), $effectInstance->isAmbient(), $effectInstance->getColor());

		if($effect instanceof EffectInstance) {
			$add->setClosure(function () use ($effect, $player): void {
					Utils::executeLater(function() use($effect, $player) : void{
						if ($player->isOnline() && $player->isConnected()){
							$player->getEffects()->add($effect);
						}
					}, 1);
			});
		}

		return $add;
	}
}