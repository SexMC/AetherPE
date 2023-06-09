<?php

declare(strict_types=1);

namespace skyblock\items\potions;

use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\Server;
use skyblock\items\potions\types\CriticalPotion;
use skyblock\items\potions\types\HastePotion;
use skyblock\items\potions\types\HealthPotion;
use skyblock\items\potions\types\ManaPotion;
use skyblock\items\potions\types\SpeedPotion;
use skyblock\items\potions\types\StunPotion;
use skyblock\items\potions\types\TestPotion;
use skyblock\items\SkyblockItemFactory;
use skyblock\player\AetherPlayer;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class AetherPotionHandler {
	use AetherHandlerTrait;
	use AwaitStdTrait;

	private int $meta = 80;

	private array $potions = [];
	/** @var AetherPotionInstance[] */
	private array $activatedCache = [];

	private array $potionsByIngredient = [];

	public function onEnable() : void{

		$this->registerSkyBlockPotion(SpeedPotion::class, 80);
		$this->registerSkyBlockPotion(ManaPotion::class, 81);
		$this->registerSkyBlockPotion(HealthPotion::class, 82);
		$this->registerSkyBlockPotion(HastePotion::class, 83);
		$this->registerSkyBlockPotion(CriticalPotion::class, 84);
		$this->registerSkyBlockPotion(StunPotion::class, 85);

		$this->expireTick();
	}

	public function expireTick(): void {
		Await::f2c(function() {
			while(true){
				yield $this->getStd()->sleep(60);

				foreach(Server::getInstance()->getOnlinePlayers() as $player){
					assert($player instanceof AetherPlayer);


					foreach($player->getPotionData()->getActivePotions() as $potionInstance){
						$potionInstance->leftDuration -= 3;


						if($potionInstance->leftDuration <= 0){
							$potionInstance->item->onDeActivate($player);
							$player->getPotionData()->deActivatePotion($potionInstance->item);
						}
					}
				}
			}
		});
	}

	public function registerSkyBlockPotion(string $class, int $meta): void {
		/** @var SkyBlockPotion $s */
		SkyblockItemFactory::getInstance()->register($s = new $class(new ItemIdentifier(ItemIds::POTION, $meta)));
		var_dump("registers meta: $meta");
		$this->potions[strtolower($s->getPotionName())] = clone $s;

		Utils::executeLater(function() use($s) {
			foreach($s->getInputWithOutputs() as $inputWithOutput){
				/** @var Item $input */
				$input = $inputWithOutput[0];
				/** @var Item $output */
				$output = clone $inputWithOutput[1];

				$this->potionsByIngredient[$input->getName() . $input->getId() . $input->getMeta() . implode(";", $input->getLore())] = $output;
			}
		}, 1);
	}


	public function getPotionByIngredient(Item $ingredient): ?Item {
		return $this->potionsByIngredient[$ingredient->getName() . $ingredient->getId() . $ingredient->getMeta() . implode(";", $ingredient->getLore())] ?? null;
	}


	/**
	 * @return array
	 */
	public function getPotions() : array{
		return $this->potions;
	}

	public function getPotion(string $v): ?SkyBlockPotion {
		return $this->potions[strtolower($v)] ?? null;
	}
}