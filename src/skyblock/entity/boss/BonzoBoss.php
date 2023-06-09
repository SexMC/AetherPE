<?php

declare(strict_types=1);

namespace skyblock\entity\boss;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use skyblock\player\AetherPlayer;
use skyblock\utils\EntityUtils;

class BonzoBoss extends Living {
	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0.4, 1.2);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::TURTLE;
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setScale(2.5);

		$this->setNameTag($this->getName());
		$this->setNameTagAlwaysVisible();

		$this->setCanSaveWithChunk(false);
	}


	public function useAbility(AetherPlayer $player): void {
		$player->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), 20 * 10, 3));

		$player->sendMessage("§r");
		$player->sendMessage("§r§l§a» Mr Bonzo «§r§7 Do you feel a bit §ddizzy??");
		$player->sendMessage("§r§l§a» Mr Bonzo «§r§7 Feel my power!");
		$player->sendMessage("§r");

		$player->getPveData()->setHealth(1);

		for($i = 0; $i <= 3; $i++){
			EntityUtils::spawnLightning($player->getLocation());
		}
	}


	public function getName() : string{
		return "§r§a§lMr. Bonzo";
	}
}