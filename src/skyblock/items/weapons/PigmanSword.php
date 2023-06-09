<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\FlameParticle;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ability\ParticleBeamAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\PveUtils;
use SOFe\AwaitGenerator\Await;

class PigmanSword extends SkyBlockWeapon implements ItemComponents{
	use AwaitStdTrait;
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->properties->setDescription([
			"§r§7Protect yourself by",
			"§r§7burning enemies to death",
			"§r",
			"§r§6§lABILITY \"§r§eBurning Souls§l§6\"",
			"§r§e\"Gain §a+300" . PveUtils::getDefense() . "§e for",
			"§r§e§a5s§e and cast beams of flames",
			"§r§etowards enemies dealing upto\"",
			"§r§e§c10,000§e base damage..\"",
			"§r",
			"§r§b§lMANA COST \"§r§3400§l§b\"",
			"§r§b§lCOOLDOWN \"§r§35s§l§b\"",
		]);

		$this->initComponent("pigman_sword", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));


		$this->properties->setRarity(Rarity::legendary());


		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 200));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 100));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::INTELLIGENCE(), 300));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::CRITICAL_CHANCE(), 5));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::CRITICAL_DAMAGE(), 30));
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		parent::onClickAir($player, $directionVector);

		assert($player instanceof AetherPlayer);


		$alreadyDamaged = [];
		$last = null;
		foreach($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy(10, 10, 10)) as $e){
			if($e instanceof PveEntity){
				if(in_array($e->getId(), $alreadyDamaged)) continue;

				$a = new ParticleBeamAbility(
					new FlameParticle(),
					10000,
					10,
					$player->getPosition(),
					$e->getPosition(),
					"§eBurning Souls",
					400,
					1,
				);

				if($a->start($player, $this)){
					$a::setCooldownByName("§eBurning Souls", $player, 0);
					$last = $a;

					$alreadyDamaged[] = $e->getId();
				}
			}
		}

		if(empty($alreadyDamaged)){
			$player->sendActionBarMessage("§cNo Nearby Enemies");
		}

		if($last){
			$last::setCooldownByName("§eBurning Souls", $player, 5);

			$player->getPveData()->setDefense($player->getPveData()->getDefense() + 300);
			Await::f2c(function() use($player) {
				yield $this->getStd()->sleep(20 * 5);

				$player->getPveData()->setDefense($player->getPveData()->getDefense() - 300);
			});
		}

		return ItemUseResult::SUCCESS();
	}
}